<?php

class Pay_Helper
{


  /**
   * Bepaal de status aan de hand van het statusid.
   * Over het algemeen worden allen de statussen -90(CANCEL), 20(PENDING) en 100(PAID) gebruikt
   *
   * @param int $statusId
   * @return string De status
   */
  public static function getStateText($stateId)
  {
    switch ($stateId)
    {
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
        if ($stateId < 0)
        {
          return 'CANCEL';
        } else
        {
          return 'UNKNOWN';
        }
    }
  }

  //remove all empty nodes in an array
  public static function filterArrayRecursive($array)
  {
    $newArray = array();
    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
        $value = self::filterArrayRecursive($value);
      }
      if (!empty($value))
      {
        $newArray[$key] = $value;
      }
    }
    return $newArray;
  }

    public static function splitAddress($strAddress)
    {
        $strAddress = trim($strAddress);
        $a = preg_split('/(\\s+)([0-9]+)/', $strAddress, 2,
            PREG_SPLIT_DELIM_CAPTURE);
        $strStreetName = trim(array_shift($a));
        $strStreetNumber = trim(implode('', $a));
        if (empty($strStreetName) || empty($strStreetNumber)) { // American address notation
            $a = preg_split('/([a-zA-Z]{2,})/', $strAddress, 2,
                PREG_SPLIT_DELIM_CAPTURE);
            $strStreetNumber = trim(array_shift($a));
            $strStreetName = implode('', $a);
        }

        // if streetnumber > 10 the api will throw an error, so we just omit the address
        if(strlen($strStreetNumber)>10){
            return array('', '');
        }

        return array($strStreetName, $strStreetNumber);
    }

  /**
   * Determine the tax class to send to PAY.
   *
   * @param int|float $amountInclTax
   * @param int|float $taxAmount
   * @return string The tax class (N, L or H)
   */
  public static function calculateTaxClass($amountInclTax, $taxAmount)
  {
    $taxClasses = array(
      0 => 'N',
      6 => 'L',
      21 => 'H'
    );
    // return 0 if amount or tax is 0
    if ($taxAmount == 0 || $amountInclTax == 0)
    {
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
   * @param int $number
   * @param array $numbers
   * @return int|bool nearest number false on error
   */
  private static function nearest($number, $numbers)
  {
    $output = FALSE;
    $number = intval($number);
    if (is_array($numbers) && count($numbers) >= 1)
    {
      $NDat = array();
      foreach ($numbers as $n)
      {
        $NDat[abs($number - $n)] = $n;
      }
      ksort($NDat);
      $NDat = array_values($NDat);
      $output = $NDat[0];
    }
    return $output;
  }

  public static function getPaymentOptions()
  {

  }
}
