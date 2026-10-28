<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/Pay/Autoload.php';

final class ModelExtensionPaymentPaynlMetjebank extends Pay_Model
{
    protected $_paymentOptionId = Pay_Model::PAYMENT_METHOD_METJEBANK;
    protected $_paymentMethodName = 'paynl_metjebank';
}
