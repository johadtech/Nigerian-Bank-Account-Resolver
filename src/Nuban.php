<?php

namespace NigerianBankResolver;

/**
 * Class Nuban
 * 
 * Implements NIBSS NUBAN Modulo 10 check digit calculation and account validation.
 */
class Nuban
{
    /**
     * Standard NUBAN checksum weight multipliers for 12 digits (3 prefix + 9 serial).
     */
    private const WEIGHTS = [3, 7, 3, 3, 7, 3, 3, 7, 3, 3, 7, 3];

    /**
     * Validate a 10-digit account number against a given 3-digit bank NUBAN prefix.
     *
     * @param string $accountNumber
     * @param string $bankPrefix
     * @return bool
     */
    public static function validate(string $accountNumber, string $bankPrefix): bool
    {
        if (strlen($accountNumber) !== 10 || !ctype_digit($accountNumber)) {
            return false;
        }

        if (strlen($bankPrefix) !== 3 || !ctype_digit($bankPrefix)) {
            return false;
        }

        $serialNumber = substr($accountNumber, 0, 9);
        $checkDigit = (int) substr($accountNumber, 9, 1);

        return $checkDigit === self::calculateCheckDigit($bankPrefix, $serialNumber);
    }

    /**
     * Calculate the expected check digit using Modulo 10 algorithm.
     *
     * @param string $bankPrefix
     * @param string $serialNumber
     * @return int
     */
    public static function calculateCheckDigit(string $bankPrefix, string $serialNumber): int
    {
        $combined = $bankPrefix . $serialNumber;
        $sum = 0;

        // Iterate through all 12 digits (3 prefix + 9 serial)
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $combined[$i] * self::WEIGHTS[$i];
        }

        $modulo = $sum % 10;
        $checkDigit = 10 - $modulo;

        return ($checkDigit === 10) ? 0 : $checkDigit;
    }
}
