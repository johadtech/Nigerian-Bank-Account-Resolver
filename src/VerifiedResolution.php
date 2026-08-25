<?php

declare(strict_types=1);

namespace NigerianBankResolver;

final class VerifiedResolution
{
    /**
     * @param list<Bank> $candidates
     */
    public function __construct(
        public readonly string $status,
        public readonly ?Bank $bank = null,
        public readonly ?string $accountName = null,
        public readonly array $candidates = [],
        public readonly ?string $message = null,
    ) {
    }

    public static function verified(Bank $bank, ?string $accountName = null): self
    {
        // Reasoning: expose a single institution only after an external provider confirms the account against that institution.
        return new self('verified', $bank, $accountName, [$bank]);
    }

    /**
     * @param list<Bank> $candidates
     */
    public static function ambiguous(array $candidates, ?string $message = null): self
    {
        // Reasoning: preserve legitimate uncertainty instead of ranking mathematically compatible institutions without evidence.
        return new self('ambiguous', null, null, $candidates, $message);
    }

    public static function unavailable(?string $message = null): self
    {
        // Reasoning: distinguish provider outages or missing configuration from an account that was actually rejected.
        return new self('unavailable', null, null, [], $message);
    }

    public static function notFound(?string $message = null): self
    {
        // Reasoning: make a completed verification failure explicit so callers do not mistake it for a successful bank identification.
        return new self('not_found', null, null, [], $message);
    }
}
