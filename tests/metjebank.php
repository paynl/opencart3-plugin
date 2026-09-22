<?php

declare(strict_types=1);

// Run with: php tests/metjebank.php
// OpenCart stand-ins keep this check runnable without a shop installation.
// phpcs:disable Squiz.Commenting.FunctionComment.Missing, PSR1.Classes.ClassDeclaration.MultipleClasses

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PayNL\Sdk\Api\Api;
use PayNL\Sdk\AuthAdapter\Basic;
use PayNL\Sdk\Model\CheckoutOption;
use PayNL\Sdk\Model\CheckoutOptions;
use PayNL\Sdk\Model\Method;
use PayNL\Sdk\Model\Methods;
use PayNL\Sdk\Model\Request\ServiceGetConfigRequest;
use PayNL\Sdk\Model\Response\ServiceGetConfigResponse;

define('DIR_SYSTEM', dirname(__DIR__) . '/upload/system');
define('DB_PREFIX', 'oc_');
define('VERSION', '3.0.3.9');

class Controller
{
    public function __construct(private array $registry = [])
    {
    }

    public function __get(string $key): mixed
    {
        return $this->registry[$key] ?? null;
    }
}

class Model extends Controller
{
}

require_once dirname(__DIR__) . '/upload/Pay/Autoload.php';
require_once dirname(__DIR__) . '/upload/catalog/controller/extension/payment/paynl_metjebank.php';
require_once dirname(__DIR__) . '/upload/catalog/controller/extension/payment/paynl_ideal.php';
require_once dirname(__DIR__) . '/upload/catalog/model/extension/payment/paynl_metjebank.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$config = new class {
    public array $values = [];

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }
};
$config->values = [
    'payment_paynl_general_apitoken' => 'test-token',
    'payment_paynl_general_tokencode' => 'AT-0000-0000',
    'payment_paynl_general_serviceid' => 'SL-0000-0000',
    'payment_paynl_general_testmode' => true,
    'payment_paynl_general_custom_exchange_url' => '',
    'payment_paynl_general_prefix' => '',
    'config_currency' => 'EUR',
];

$db = new class {
    public PDO $connection;
    public array $types = ['paynl_paymentoption_subs' => 'int(11)', 'paynl_transactions' => 'int(11)'];
    public int $migrations = 0;

    public function __construct()
    {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->sqliteCreateFunction('NOW', static fn(): string => '2026-01-01 00:00:00');
        $this->connection->sqliteCreateFunction('UNIX_TIMESTAMP', static fn(): int => 1767225600);
        $this->connection->exec('CREATE TABLE oc_paynl_paymentoptions (
            id INTEGER PRIMARY KEY, optionId INTEGER, serviceId TEXT, name TEXT, img TEXT, update_date TEXT)');
        $this->connection->exec('CREATE TABLE oc_paynl_paymentoption_subs (
            id INTEGER PRIMARY KEY, optionSubId TEXT, paymentOptionId INTEGER, name TEXT, img TEXT, update_date TEXT)');
        $this->connection->exec('CREATE TABLE oc_paynl_transactions (
            id TEXT, orderId INTEGER, optionId INTEGER, optionSubId TEXT, amount INTEGER,
            status TEXT, created INTEGER, start_data TEXT)');
    }

    public function escape(mixed $value): string
    {
        return str_replace("'", "''", (string) $value);
    }

    public function getLastId(): string
    {
        return $this->connection->lastInsertId();
    }

    public function query(string $sql): object
    {
        if (preg_match('/^(SHOW COLUMNS FROM|ALTER TABLE) `oc_(\w+)`/', $sql, $matches)) {
            $table = $matches[2];
            if ($matches[1] === 'ALTER TABLE') {
                $this->types[$table] = 'varchar(64)';
                $this->migrations++;
            }
            return (object) ['row' => [
                'Type' => $this->types[$table],
                'Null' => $table === 'paynl_transactions' ? 'YES' : 'NO',
            ]];
        }
        if (str_starts_with($sql, 'DELETE options,optionsubs')) {
            $sql = 'DELETE FROM oc_paynl_paymentoption_subs; DELETE FROM oc_paynl_paymentoptions';
            $this->connection->exec($sql);
            return (object) ['row' => [], 'rows' => []];
        }
        $rows = $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return (object) ['row' => $rows[0] ?? [], 'rows' => $rows];
    }
};

$method = (new Method())->setId(5358)->setName('Metjebank.nl')->setImage('/payment_methods/471.svg');
$method->setOptions([
    ['id' => '27171', 'issuerId' => 'TRIONL2U', 'name' => 'Triodos', 'image' => '/issuers/27171.svg'],
    ['issuerId' => 'ASNBNL21', 'name' => 'ASN Bank', 'image' => 'https://static.pay.nl/issuers/27174.svg'],
    ['id' => '123', 'name' => "Bank O'Example", 'image' => ''],
]);
$checkout = (new CheckoutOption())->setTag('PM_5358')->setPaymentMethods((new Methods())->addMethod($method));
$serviceConfig = new ServiceGetConfigResponse();
$serviceConfig->setCheckoutOptions((new CheckoutOptions())->addCheckoutOption($checkout));
$cacheKey = 'service_getconfig_' . md5(json_encode(['AT-0000-0000', 'test-token', 'SL-0000-0000']));
(new ReflectionProperty(ServiceGetConfigRequest::class, 'cache'))->setValue(null, [$cacheKey => $serviceConfig]);

$model = new ModelExtensionPaymentPaynlMetjebank(['db' => $db, 'config' => $config]);
$model->refreshPaymentOptions('SL-0000-0000', 'test-token', 'AT-0000-0000');
$banks = $model->getPaymentOption(5358)['optionSubs'];
check(array_column($banks, 'id') === ['ASNBNL21', '123', '27171'], 'Bank identifiers must survive storage.');
check($banks[2]['img'] === 'https://static.pay.nl/issuers/27171.svg', 'Relative logos need the Pay host.');
check($banks[0]['img'] === 'https://static.pay.nl/issuers/27174.svg', 'Absolute logos must remain unchanged.');
check($banks[1]['img'] === '', 'Missing logos must remain empty.');
check($db->migrations === 2, 'Existing numeric bank columns must be upgraded.');
$model->refreshPaymentOptions('SL-0000-0000', 'test-token', 'AT-0000-0000');
check($db->migrations === 2, 'The schema upgrade must be idempotent.');
check(count($model->getPaymentOption(5358)['optionSubs']) === 3, 'Refreshing must replace cached banks.');

$loader = new class {
    public function language(string $path): void
    {
    }

    public function model(string $path): void
    {
    }

    public function view(string $path, array $data): array
    {
        return $data;
    }
};
$registry = [
    'config' => $config,
    'load' => $loader,
    'model_extension_payment_paynl_metjebank' => $model,
    'model_extension_payment_paynl_ideal' => new class {
        public function getPaymentOption(int $id): array
        {
            return ['optionSubs' => [['id' => '123', 'name' => 'Legacy bank']]];
        }
    },
    'session' => (object) ['data' => ['currency' => 'EUR']],
    'language' => new class {
        public function get(string $key): string
        {
            return $key;
        }
    },
    'request' => (object) ['post' => [], 'server' => ['REMOTE_ADDR' => '127.0.0.1']],
    'url' => new class {
        public function link(string $route): string
        {
            return 'https://shop.example/index.php?route=' . $route;
        }
    },
    'cart' => new class {
        public function getProducts(): array
        {
            return [];
        }

        public function getTaxes(): array
        {
            return [];
        }
    },
    'model_setting_extension' => new class {
        public function getExtensions(string $type): array
        {
            return [];
        }
    },
    'currency' => new class {
        public function convert(float $amount, string $from, string $to): float
        {
            return $amount;
        }
    },
];
$controller = new ControllerExtensionPaymentPaynlMetjebank($registry);
check(count($controller->index()['optionSubList']) === 3, 'Metjebank must display the cached banks by default.');
check($controller->index()['bankSelection'] === 'dropdown', 'The default bank selector must be a dropdown.');
$config->values['payment_paynl_metjebank_bank_selection'] = 'radio';
check($controller->index()['bankSelection'] === 'radio', 'The radio setting must reach the template.');
$config->values['payment_paynl_metjebank_banklist'] = '0';
check($controller->index()['optionSubList'] === [], 'Disabled bank lists must be hidden.');
check((new ControllerExtensionPaymentPaynlideal($registry))->index()['optionSubList'] === [], 'iDEAL must not show banks.');

$history = [];
$mock = new MockHandler();
$stack = HandlerStack::create($mock);
$stack->push(Middleware::history($history));
$auth = (new Basic())->setUsername('test')->setPassword('test');
$api = new Api($auth, new Client(['handler' => $stack, 'base_uri' => 'https://pay.example/']));
$transaction = new Pay_Controller_Transaction($controller);
$transaction->payConfig = new class ($controller, $api) extends Pay_Controller_Config {
    public function __construct(Controller $controller, private Api $api)
    {
        parent::__construct($controller);
    }

    public function getConfig($useCore = false, $tokencode = null, $apitoken = null): \PayNL\Sdk\Config\Config
    {
        $config = parent::getConfig($useCore, $tokencode, $apitoken);
        $config->set('service_manager', ['services' => [Api::class => $this->api]]);
        return $config;
    }
};
$order = [
    'order_id' => '123', 'currency_code' => 'EUR', 'language_code' => 'en-gb', 'total' => 10.0,
    'payment_iso_code_2' => 'NL', 'payment_address_1' => 'Test street 1', 'payment_address_2' => '',
    'payment_postcode' => '1234AB', 'payment_city' => 'Test', 'payment_zone_code' => '',
    'shipping_iso_code_2' => 'NL', 'shipping_address_1' => 'Test street 1', 'shipping_address_2' => '',
    'shipping_postcode' => '1234AB', 'shipping_city' => 'Test', 'shipping_zone_code' => '',
];
foreach ([['1', 'ASNBNL21'], ['1', '27171'], ['1', ''], ['0', '27171']] as [$enabled, $selected]) {
    $config->values['payment_paynl_metjebank_banklist'] = $enabled;
    $registry['request']->post = ['optionSubId' => $selected, 'coc' => '', 'vat' => '', 'dob' => ''];
    $mock->append(new Response(200, [], json_encode([
        'orderId' => 'test-order', 'links' => ['redirect' => 'https://bank.example/pay'],
    ])));
    $url = $transaction->startTransaction($order, 5358, 'paynl_metjebank');
    check($url === 'https://bank.example/pay', 'The Pay redirect URL must reach the checkout.');
    $body = json_decode((string) end($history)['request']->getBody(), true);
    $expected = $enabled === '1' && $selected !== '' ? $selected : null;
    check($body['paymentMethod']['id'] === 5358, 'The request must use Metjebank.');
    $expectedIssuer = $expected !== null && ctype_digit($expected) ? (int) $expected : $expected;
    check(
        ($body['paymentMethod']['input']['issuerId'] ?? null) === $expectedIssuer,
        'The request must respect bank selection.'
    );
    $stored = $db->connection->query('SELECT optionSubId FROM oc_paynl_transactions ORDER BY rowid DESC')->fetchColumn();
    check($stored === ($expected ?? '0'), 'Transaction storage must match the bank sent to Pay.');
}
$config->values['payment_paynl_metjebank_banklist'] = '1';
foreach (['unknown-bank', ['TRIONL2U']] as $invalid) {
    $registry['request']->post = ['optionSubId' => $invalid];
    $rejected = false;
    try {
        $transaction->startTransaction($order, 5358, 'paynl_metjebank');
    } catch (InvalidArgumentException $exception) {
        $rejected = true;
    }
    check($rejected, 'Unknown and malformed banks must be rejected before contacting Pay.');
}
check(count($history) === 4, 'Invalid banks must not create a payment.');
$method->setOptions([]);
$model->refreshPaymentOptions('SL-0000-0000', 'test-token', 'AT-0000-0000');
check($controller->index()['optionSubList'] === [], 'An empty response must remove stale banks.');

echo "Metjebank checks passed: bank cache, logos, upgrade, settings, iDEAL, API payload and redirect.\n";
