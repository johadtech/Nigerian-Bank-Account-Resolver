<?php

declare(strict_types=1);

namespace NigerianBankResolver;

final class AccountVerificationResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly ?string $accountNumber,
        public readonly ?string $accountName,
        public readonly ?string $message = null,
    ) {
    }

    public static function success(string $accountNumber, ?string $accountName, ?string $message = null): self
    {
        // Reasoning: represent provider-confirmed account details without coupling the domain result to one provider's response schema.
        return new self(true, $accountNumber, $accountName, $message);
    }

    public static function failure(?string $message = null): self
    {
        // Reasoning: represent a completed provider lookup that did not confirm the supplied account details.
        return new self(false, null, null, $message);
    }
}
