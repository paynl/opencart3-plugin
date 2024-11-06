<?php

declare(strict_types=1);

namespace PayNL\Sdk\Util;

use PayNL\Sdk\Config\Config as PayConfig;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PayNL\Sdk\Model\Pay\PayStatus;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Exception\PayException;
use Exception;

/**
 * Class Signing
 *
 * @package PayNL\Sdk\Util
 */
class Exchange
{
    private array $payload;
    private ?array $custom_payload;
    private $headers;

    /**
     * @param array|null $payload
     */
    public function __construct(array $payload = null)
    {
        $this->custom_payload = $payload;
    }

    /**
     * @return bool
     */
    public function eventStateChangeToPaid()
    {
        return $this->getAction() === PayStatus::EVENT_PAID;
    }

    /**
     * Set your exchange response in the end of your exchange processing
     *
     * @param bool $result
     * @param string $message
     * @param $returnOutput If If true, then this method returs the output string
     * @return false|string|void
     */
    public function setResponse(bool $result, string $message, $returnOutput = false)
    {
        if ($this->isSignExchange() === true) {
            $response = json_encode(['result' => $result, 'description' => $message]);
        } else {
            $response = ($result === true ? 'TRUE' : 'FALSE') . '| ' . $message;
        }

        if ($returnOutput === true) {
            return $response;
        } else {
            echo $response;
            exit();
        }
    }

    /**
     * @return false|mixed|string
     */
    public function getAction()
    {
        $payload = $this->getPayload();
        return $payload['action'] ?? false;
    }

    /**
     * @return mixed|string
     */
    public function getReference()
    {
        $payload = $this->getPayload();
        return $payload['reference'] ?? '';
    }

    /**
     * @return false|mixed|string
     */
    public function getPayOrderId()
    {
        $payload = $this->getPayload();
        return $payload['payOrderId'] ?? false;
    }

    /**
     * @return array|string Array with payload or string with fault message.
     */
    public function getPayLoad()
    {
        try {
            if (!empty($this->payload)) {
                return $this->payload;
            }

            if (!empty($this->custom_payload)) {
                $request = $this->custom_payload;
            } else {
                $request = $_REQUEST ?? false;
                if ($request === false) {
                    throw new Exception('Empty payload');
                }
            }

            $action = $request['action'] ?? null;

            if (!empty($action)) {
                # The argument "action" tells us this is not coming from TGU
                $action = $request['action'] ?? null;
                $paymentProfile = $request['payment_profile_id'] ?? null;
                $payOrderId = $request['order_id'] ?? '';
                $orderId = $request['extra1'] ?? null;
                $reference = $request['extra1'] ?? null;
            } else {
                # TGU
                if (isset($request['object'])) {
                    $tguData['object'] = $request['object'];
                } else {
                    $rawBody = file_get_contents('php://input');
                    if (empty(trim($rawBody))) {
                        throw new Exception('Empty Payload');
                    }

                    $tguData = json_decode($rawBody, true, 512, 4194304);

                    $exchangeType = $tguData['type'] ?? null;
                    if ($exchangeType != 'order') {
                        throw new Exception('Cant handle exchange type other then order');
                    }
                }

                if (empty($tguData['object'])) {
                    throw new Exception('Payload error: object empty');
                }

                foreach (($tguData['object']['payments'] ?? []) as $payment) {
                    $ppid = $payment['paymentMethod']['id'] ?? null;
                }
                $paymentProfile = $ppid ?? '';
                $payOrderId = $tguData['object']['orderId'] ?? '';
                $internalStateId = (int)$tguData['object']['status']['code'] ?? 0;
                $internalStateName = $tguData['object']['status']['action'] ?? '';
                $orderId = $tguData['object']['reference'] ?? '';

                $action = in_array($internalStateId, [PayStatus::PAID, PayStatus::AUTHORIZE]) ? 'new_ppt' : $internalStateName;

                $reference = $tguData['object']['reference'] ?? '';
                $checkoutData = $tguData['object']['checkoutData'] ?? null;

                $amount = $tguData['object']['amount']['value'] ?? '';
                $amountCap = $tguData['object']['capturedAmount']['value'] ?? '';
                $amountAuth = $tguData['object']['authorizedAmount']['value'] ?? '';
            }

            $this->payload = [
                'amount' => $amount ?? null,
                'amountCap' => $amountCap ?? null,
                'amountAuth' => $amountAuth ?? null,
                'reference' => $reference,
                'action' => strtolower($action),
                'paymentProfile' => $paymentProfile ?? null,
                'payOrderId' => $payOrderId,
                'orderId' => $orderId,
                'internalStateId' => $internalStateId ?? 0,
                'internalStateName' => $internalStateName ?? null,
                'checkoutData' => $checkoutData ?? null,
                'fullPayload' => $tguData ?? $request
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->payload;
    }

    /**
     * Process the exchange request.
     *
     * @param Config|null $config
     * @return PayOrder
     */
    public function process(PayConfig $config = null): PayOrder
    {
        $payload = $this->getPayload();
        $payStatus = new PayStatus();
        if (!is_array($payload)) {
            return (new PayOrder([]))->setMessage($payload);
        }
        if (empty($config)) {
            $config = Config::getConfig();
        }

        $signingResult = $this->checkSignExchange($config->getUsername(), $config->getPassword());

        $payOrder = new PayOrder($payload['fullPayload']);

        if ($signingResult === true) {
            # This was a signing exchange, and it was authorised. Returning the current payment state.
            $internalState = $payload['internalStateId'];
            dbg('This was a signing exchange, and it was authorised.' . PHP_EOL .
                'Returning the current payment state. ' . PHP_EOL . 'Result internalstateid: ' .
                $internalState . PHP_EOL . 'Order id: ' . $payload['payOrderId']);

        } else {
            # This was not a signing exchange or the signing exchange was invalid.
            if ($this->isSignExchange()) {
                return $payOrder->setMessage('Signing request, but failed');
            }

            if ($payStatus->get($payload['internalStateId']) === PayStatus::PENDING) {
                $internalState = $payload['internalStateId'];
                dbg($payload['payOrderId'] . PHP_EOL . 'State is pending, so NO manual API request for confirming order status.');
            } else {
                # Continue to check the order status manually
                try {
                    if (empty($payload['payOrderId'])) {
                        return $payOrder->setMessage('Missing pay order id in payload');
                    }
                    dbg('payOrderId:' . $payload['payOrderId']);

                    $request = new OrderStatusRequest($payload['payOrderId']);
                    if (!empty($config)) {
                        $request->setConfig($config);
                    }
                    $transaction = $request->start();
                    $internalState = $transaction->getStatusCode();
                    dbg('Retrieving order manually with: ' . $payload['payOrderId'] . PHP_EOL . 'Result internalstateid: ' . $internalState);
                } catch (PayException $e) {
                    $internalState = 0;
                    $message = $e->getFriendlyMessage();
                    dbg($message);
                }
            }
        }

        $payOrder->setAmount($payload['amount']);
        $payOrder->setPaymentProfileId($payload['paymentProfile']);
        $payOrder->setOrderId($payload['payOrderId']);
        $payOrder->setReference($payload['reference'] ?? '');
        $payOrder->setMessage($message ?? '');

        try {
            $payOrder->setStateId($payStatus->get($internalState));
        } catch (Exception $e) {
        }

        return $payOrder;
    }

    /**
     * @param string $username Token code
     * @param string $password API Token
     * @return bool Returns true if the signing is successful and authorised
     */
    public function checkSignExchange(string $username = '', string $password = ''): bool
    {
        try {
            if (!$this->isSignExchange()) {
                throw new Exception('No signing exchange');
            }

            if (empty($username) || empty($password)) {
                $config = Config::getConfig();
                $username = (string)$config->getUsername();
                $password = (string)$config->getPassword();
            }

            $headers = $this->getRequestHeaders();
            $tokenCode = trim($headers['signature-keyid'] ?? '');

            if (empty($tokenCode)) {
                throw new Exception('TokenCode empty');
            }
            if ($tokenCode !== $username) {
                throw new Exception('TokenCode invalid');
            }
            $rawBody = file_get_contents('php://input');
            $signature = hash_hmac($headers['signature-algorithm'] ?? 'sha256', $rawBody, $password);

            if (!hash_equals($headers['signature'] ?? '', $signature)) {
                throw new Exception('Signature failed');
            }
        } catch (Exception $e) {
            dbg('checkSignExchange: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isSignExchange(): bool
    {
        $headers = $this->getRequestHeaders();
        $signingMethod = $headers['signature-method'] ?? null;
        return $signingMethod === 'HMAC';
    }

    /**
     * @return array|false|string
     */
    private function getRequestHeaders()
    {
        if (empty($this->headers)) {
            $this->headers = array_change_key_case(getallheaders());
        }
        return $this->headers;
    }

}