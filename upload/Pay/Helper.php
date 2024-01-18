<?php

class Pay_Helper
{
    /**
     * Bepaal de status aan de hand van het statusid.
     * Over het algemeen worden allen de statussen -90(CANCEL), 20(PENDING) en 100(PAID) gebruikt
     *
     * @param integer $stateId
     * @return string De status
     */
    public static function getStateText($stateId)
    {
        switch ($stateId) {
            case -70:
            case -71:
                return 'CHARGEBACK';
            case -51:
                return 'PAID CHECKAMOUNT';
            case -81:
                return 'REFUND';
            case -82:
                return 'PARTIAL REFUND';
            case 20:
            case 25:
            case 50:
                return 'PENDING';
            case 60:
                return 'OPEN';
            case 75:
            case 76:
                return 'CONFIRMED';
            case 80:
                return 'PARTIAL PAYMENT';
            case 100:
                return 'PAID';
            default:
                if ($stateId < 0) {
                    return 'CANCEL';
                } else {
                    return 'UNKNOWN';
                }
        }
    }

    /**
     * remove all empty nodes in an array
     *
     * @param array $array
     * @return string De status
     */
    public static function filterArrayRecursive($array)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::filterArrayRecursive($value);
            }
            if (!empty($value)) {
                $newArray[$key] = $value;
            }
        }
        return $newArray;
    }

    /**
     * @param string $strAddress
     * @return array
     */
    public static function splitAddress($strAddress)
    {
        $strAddress = trim($strAddress);
        $a = preg_split(
            '/(\\s+)([0-9]+)/',
            $strAddress,
            2,
            PREG_SPLIT_DELIM_CAPTURE
        );
        $strStreetName = trim(array_shift($a));
        $strStreetNumber = trim(implode('', $a));
        if (empty($strStreetName) || empty($strStreetNumber)) { // American address notation
            $a = preg_split(
                '/([a-zA-Z]{2,})/',
                $strAddress,
                2,
                PREG_SPLIT_DELIM_CAPTURE
            );
            $strStreetNumber = trim(array_shift($a));
            $strStreetName = implode('', $a);
        }

        // if streetnumber > 10 the api will throw an error, so we just omit the address
        if (strlen($strStreetNumber) > 10) {
            return array('', '');
        }

        return array($strStreetName, $strStreetNumber);
    }

    /**
     * @param $payState
     * @return string
     */
    public static function getStatus($payState)
    {
        $status = Pay_Model::STATUS_PENDING;
        if ($payState == 100) {
            $status = Pay_Model::STATUS_COMPLETE;
        } elseif ($payState == -81) {
            $status = Pay_Model::STATUS_REFUNDED;
        } elseif ($payState < 0) {
            $status = Pay_Model::STATUS_CANCELED;
        }
        return $status;
    }

    /**
     * @param $payState
     * @param $settings
     * @param $name
     * @return mixed
     */
    public static function getOrderStatusId($payState, $settings, $name)
    {
        $statusPending = $settings['payment_' . $name . '_pending_status'];
        $statusComplete = $settings['payment_' . $name . '_completed_status'];
        $statusCanceled = $settings['payment_' . $name . '_canceled_status'];
        $statusRefunded = $settings['payment_' . $name . '_refunded_status'];

        $orderStatusId = $statusPending;
        if ($payState == 100) {
            $orderStatusId = $statusComplete;
        } elseif ($payState == -81) {
            $orderStatusId = empty($statusRefunded) ? 11 : $statusRefunded;
        } elseif ($payState < 0) {
            $orderStatusId = $statusCanceled;
        }
        return $orderStatusId;
    }

    /**
     * Determine the tax class to send to Pay.
     *
     * @param integer|float $amountInclTax
     * @param integer|float $taxAmount
     * @return string The tax class (N, L or H)
     */
    public static function calculateTaxClass($amountInclTax, $taxAmount)
    {
        $taxClasses = array(
            0 => 'N',
            6 => 'L',
            21 => 'H',
        );
        // return 0 if amount or tax is 0
        if ($taxAmount == 0 || $amountInclTax == 0) {
            return $taxClasses[0];
        }
        $amountExclTax = $amountInclTax - $taxAmount;
        $taxRate = ($taxAmount / $amountExclTax) * 100;
        $nearestTaxRate = self::nearest($taxRate, array_keys($taxClasses));
        return ($taxClasses[$nearestTaxRate]);
    }

    /**
     * Get the nearest number
     *
     * @param integer $number
     * @param array $numbers
     * @return integer|boolean nearest number false on error
     */
    private static function nearest($number, $numbers)
    {
        $output = false;
        $number = intval($number);
        if (is_array($numbers) && count($numbers) >= 1) {
            $NDat = array();
            foreach ($numbers as $n) {
                $NDat[abs($number - $n)] = $n;
            }
            ksort($NDat);
            $NDat = array_values($NDat);
            $output = $NDat[0];
        }
        return $output;
    }

    /**
     * @return void
     */
    public static function getPaymentOptions()
    {
    }
}
