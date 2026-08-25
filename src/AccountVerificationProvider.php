<?php

declare(strict_types=1);

namespace NigerianBankResolver;

interface AccountVerificationProvider
{
    public function verify(string $accountNumber, string $bankCode): AccountVerificationResult;
}
