<?php

namespace NigerianBankResolver;

/**
 * Class Resolver
 * 
 * Suggests potential matching banks for typed account numbers using NUBAN checksums
 * across all active Nigerian commercial banks, merchant banks, PSBs, and microfinance banks/fintechs.
 * 
 * Includes robust handling for fintech accounts that use mobile phone numbers.
 */
class Resolver
{
    private BankRegistry $registry;

    /**
     * Resolver constructor.
     *
     * @param BankRegistry $registry
     */
    public function __construct(BankRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Resolve matching banks for a given account number string.
     * 
     * Handles:
     * - Standard 10-digit NUBANs.
     * - 11-digit phone numbers (strips leading '0' to get 10-digit fintech identifier).
     * - 10-digit phone numbers (already stripped).
     *
     * @param string $accountNumber
     * @return Bank[]
     */
    public function resolve(string $accountNumber): array
    {
        $sanitized = $this->sanitizeAccountNumber($accountNumber);

        if (strlen($sanitized) < 10) {
            return [];
        }

        $matches = $this->matchByNuban($sanitized);

        // Apply fintech phone heuristic if applicable (wallets starting with 7, 8, 9)
        if (preg_match('/^[789]\d{9}$/', $sanitized)) {
            $matches = $this->applyFintechHeuristics($matches);
        }

        return $matches;
    }

    /**
     * Sanitize input to ensure a 10-digit identifier.
     * 
     * If an 11-digit number starting with '0' is provided (Nigerian phone format),
     * the leading '0' is stripped to align with fintech 10-digit account mappings.
     *
     * @param string $input
     * @return string
     */
    private function sanitizeAccountNumber(string $input): string
    {
        $digits = preg_replace('/\D/', '', $input);

        // If 11 digits starting with 0, strip the leading 0 (phone number to account mapping)
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return substr($digits, 0, 10);
    }

    /**
     * Match banks using NUBAN Modulo 10 algorithm across all registered institutions.
     *
     * @param string $accountNumber
     * @return Bank[]
     */
    private function matchByNuban(string $accountNumber): array
    {
        $matches = [];

        foreach ($this->registry->all() as $bank) {
            if (Nuban::validate($accountNumber, $bank->nubanPrefix)) {
                $matches[] = $bank;
            }
        }

        return $matches;
    }

    /**
     * Include prominent fintechs that allow phone number as account numbers.
     *
     * @param Bank[] $currentMatches
     * @return Bank[]
     */
    private function applyFintechHeuristics(array $currentMatches): array
    {
        // Verified slugs from the banks.json dataset
        $fintechSlugs = [
            'paycom',                // OPay
            'palmpay',               // PalmPay
            'moniepoint-mfb-ng',     // Moniepoint
            'kuda-bank',             // Kuda
            'carbon',                // Carbon
            'vfd-microfinance-bank-ng' // VFD
        ];

        foreach ($this->registry->all() as $bank) {
            if (in_array($bank->slug, $fintechSlugs, true)) {
                if (!$this->isAlreadyMatched($currentMatches, $bank)) {
                    $currentMatches[] = $bank;
                }
            }
        }

        return $currentMatches;
    }

    /**
     * Check if a bank is already present in matches list.
     *
     * @param Bank[] $matches
     * @param Bank   $bank
     * @return bool
     */
    private function isAlreadyMatched(array $matches, Bank $bank): bool
    {
        foreach ($matches as $match) {
            if ($match->code === $bank->code) {
                return true;
            }
        }

        return false;
    }
}
