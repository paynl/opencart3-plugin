<?php

# This is a minimal example on how to handle a Pay. exchange call and process an order
declare(strict_types=1);

# You might need to adjust this mapping for your situation
require '../../../../vendor/autoload.php';

use PayNL\Sdk\Util\Exchange;
use PayNL\Sdk\Model\Pay\PayStatus;

$exchange = new Exchange();

# Process the exchange request
$payOrder = $exchange->process();

if($payOrder->failed()) {
    $exchange->setResponse(false, $payOrder->getMessage());
}

$orderId = $payOrder->getReference();

switch ($payOrder->getStateId()) {
    case PayStatus::PENDING :
        $responseResult = yourCodeToProcessPendingOrder($orderId);
        $responseMessage = 'Processed pending';
        break;
    case PayStatus::PAID :
        $responseResult = yourCodeToProcessPaidOrder($orderId);
        $responseMessage = 'Processed paid. Order: ' . $orderId;
        break;
    default :
        $responseResult = false;
        $responseMessage = 'Unexpected payment state';
}

function yourCodeToProcessPendingOrder($orderId) { return true; }
function yourCodeToProcessPaidOrder($orderId) { return true; }

$exchange->setResponse($responseResult, $responseMessage);