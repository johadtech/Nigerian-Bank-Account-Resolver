<?php

declare(strict_types=1);

namespace NigerianBankResolver;

final class Resolver
{
    public function __construct(private readonly BankRegistry $registry)
    {
    }

    /**
     * @return list<Bank>
     */
    public function resolve(string $accountNumber): array
    {
        $digits = $this->digitsOnly($accountNumber);
        if ($digits === '') {
            return [];
        }

        if ($this->isNationalPhoneNumber($digits)) {
            // Reasoning: a clearly formatted Nigerian phone input must not be polluted by accidental NUBAN checksum matches.
            return $this->matchPhoneAccounts();
        }

        if (strlen($digits) !== 10) {
            return [];
        }

        $matches = $this->matchByNuban($digits);
        if ($this->isNormalizedPhoneNumber($digits)) {
            // Reasoning: a 10-digit phone input is ambiguous with a NUBAN, so documented phone matches are added without discarding valid NUBAN matches.
            $matches = $this->mergeUnique($matches, $this->matchPhoneAccounts());
        }

        return $matches;
    }

    private function digitsOnly(string $input): string
    {
        // Reasoning: normalize presentation characters while refusing truncation that could hide malformed or oversized input.
        return preg_replace('/\D+/', '', $input) ?? '';
    }

    private function isNationalPhoneNumber(string $digits): bool
    {
        // Reasoning: only the explicit 11-digit Nigerian national form is unambiguously distinguishable from a 10-digit NUBAN.
        return strlen($digits) === 11 && $digits[0] === '0' && $this->isNormalizedPhoneNumber(substr($digits, 1));
    }

    private function isNormalizedPhoneNumber(string $digits): bool
    {
        // Reasoning: this broad Nigerian mobile shape supports zero-stripped input without claiming every number belongs to a particular provider.
        return preg_match('/^[789]\d{9}$/', $digits) === 1;
    }

    /**
     * @return list<Bank>
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
     * @return list<Bank>
     */
    private function matchPhoneAccounts(): array
    {
        // Reasoning: only institutions with explicit source-backed phone-account capability can be suggested for phone-based resolution.
        return array_values(array_filter(
            $this->registry->all(),
            static fn (Bank $bank): bool => $bank->supportsPhoneAccount,
        ));
    }

    /**
     * @param list<Bank> $left
     * @param list<Bank> $right
     * @return list<Bank>
     */
    private function mergeUnique(array $left, array $right): array
    {
        // Reasoning: deduplicate by stable institution slug so multiple evidence paths cannot produce repeated suggestions.
        $merged = [];
        foreach (array_merge($left, $right) as $bank) {
            $merged[$bank->slug] = $bank;
        }

        return array_values($merged);
    }
}
