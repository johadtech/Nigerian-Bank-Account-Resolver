<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Bank.php';
require_once __DIR__ . '/../src/BankRegistry.php';
require_once __DIR__ . '/../src/Nuban.php';
require_once __DIR__ . '/../src/AccountVerificationResult.php';
require_once __DIR__ . '/../src/AccountVerificationProvider.php';
require_once __DIR__ . '/../src/VerifiedResolution.php';
require_once __DIR__ . '/../src/Resolver.php';

use NigerianBankResolver\AccountVerificationProvider;
use NigerianBankResolver\AccountVerificationResult;
use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Resolver;

function assertVerified(bool $condition, string $message): void
{
    // Reasoning: fail immediately with a business-level message so provider orchestration regressions remain easy to diagnose.
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class FixtureProvider implements AccountVerificationProvider
{
    public function __construct(private readonly string $expectedAccount, private readonly string $expectedCode)
    {
    }

    public function verify(string $accountNumber, string $bankCode): AccountVerificationResult
    {
        // Reasoning: simulate only the documented provider contract and never simulate an undocumented HTTP response.
        if ($accountNumber === $this->expectedAccount && $bankCode === $this->expectedCode) {
            return AccountVerificationResult::success($accountNumber, 'Verified Test Account');
        }

        return AccountVerificationResult::failure('No match');
    }
}

$resolver = new Resolver(new BankRegistry());

$uba = $resolver->resolveVerified(
    '2170861119',
    new FixtureProvider('2170861119', '033'),
);
assertVerified($uba->status === 'verified', 'Provider verification must produce a verified result for UBA.');
assertVerified($uba->bank?->slug === 'united-bank-for-africa', 'Provider verification must identify UBA.');

$firstBank = $resolver->resolveVerified(
    '3140537382',
    new FixtureProvider('3140537382', '011'),
);
assertVerified($firstBank->status === 'verified', 'Provider verification must produce a verified result for First Bank.');
assertVerified($firstBank->bank?->slug === 'first-bank-of-nigeria', 'Provider verification must identify First Bank.');

$ambiguous = $resolver->resolveVerified(
    '2170861119',
    new FixtureProvider('different-account', '033'),
);
assertVerified($ambiguous->status === 'not_found', 'Unconfirmed candidates must not be presented as verified.');

$phoneCandidates = $resolver->resolveVerified(
    '7085352316',
    new FixtureProvider('different-account', '992'),
);
assertVerified($phoneCandidates->status === 'not_found', 'Unconfirmed phone candidates must not be presented as verified.');

 echo "Verified resolution tests passed.\n";
