<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Bank.php';
require_once __DIR__ . '/../src/BankRegistry.php';
require_once __DIR__ . '/../src/Nuban.php';
require_once __DIR__ . '/../src/Resolver.php';

use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Nuban;
use NigerianBankResolver\Resolver;

function assertTrue(bool $condition, string $message): void
{
    // Reasoning: fail immediately with a precise behavioral message so regressions are easy to diagnose without a test framework dependency.
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function slugs(array $banks): array
{
    // Reasoning: compare stable identifiers rather than display names or ordering so tests remain resilient to naming changes.
    return array_map(static fn ($bank): string => $bank->slug, $banks);
}

$registry = new BankRegistry();
$resolver = new Resolver($registry);

assertTrue(count($registry->all()) === 280, 'Expected the resolver registry to contain 280 institutions.');

$serial = '012345678';
$gtbAccount = $serial . Nuban::calculateCheckDigit('058', $serial);
$standardMatches = slugs($resolver->resolve($gtbAccount));
assertTrue(in_array('guaranty-trust-bank', $standardMatches, true), 'A valid GTBank NUBAN must match GTBank.');

$nationalPhoneMatches = slugs($resolver->resolve('08031234567'));
assertTrue($nationalPhoneMatches === ['moniepoint-mfb-ng', 'paycom'], 'A national phone input must return only documented phone-capable institutions.');

$normalizedPhoneMatches = slugs($resolver->resolve('8031234567'));
assertTrue(in_array('moniepoint-mfb-ng', $normalizedPhoneMatches, true), 'A normalized phone input must include Moniepoint.');
assertTrue(in_array('paycom', $normalizedPhoneMatches, true), 'A normalized phone input must include OPay.');
assertTrue(!in_array('kuda-bank', $normalizedPhoneMatches, true), 'Kuda must not be suggested as phone-account capable without source evidence.');
assertTrue(!in_array('carbon', $normalizedPhoneMatches, true), 'Carbon must not be suggested as phone-account capable without source evidence.');

assertTrue($resolver->resolve('123456789012') === [], 'Inputs longer than 11 digits must be rejected rather than truncated.');
assertTrue($resolver->resolve('abc') === [], 'Non-numeric input must return no matches.');

echo "Resolver tests passed: " . count($registry->all()) . " institutions loaded.\n";
