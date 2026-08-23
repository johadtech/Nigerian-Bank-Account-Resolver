<?php

declare(strict_types=1);

namespace NigerianBankResolver;

final class Bank
{
    public readonly string $name;
    public readonly string $code;
    public readonly string $nubanPrefix;
    public readonly string $slug;
    public readonly ?string $logoPath;
    public readonly bool $supportsPhoneAccount;

    public function __construct(
        string $name,
        string $code,
        string $nubanPrefix,
        string $slug,
        ?string $logoPath = null,
        bool $supportsPhoneAccount = false,
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->nubanPrefix = $nubanPrefix;
        $this->slug = $slug;
        $this->logoPath = $logoPath;
        $this->supportsPhoneAccount = $supportsPhoneAccount;
    }

    public function toArray(): array
    {
        // Reasoning: expose the complete immutable value object while retaining the capability distinction used by resolution.
        return [
            'name' => $this->name,
            'code' => $this->code,
            'nuban_prefix' => $this->nubanPrefix,
            'slug' => $this->slug,
            'logo_path' => $this->logoPath,
            'supports_phone_account' => $this->supportsPhoneAccount,
        ];
    }
}
