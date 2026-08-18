<?php

require_once __DIR__ . '/../src/Bank.php';
require_once __DIR__ . '/../src/BankRegistry.php';
require_once __DIR__ . '/../src/Nuban.php';
require_once __DIR__ . '/../src/Resolver.php';

use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Resolver;
use NigerianBankResolver\Nuban;

// Initialize registry and resolver
$registry = new BankRegistry();
$resolver = new Resolver($registry);

echo "Total registered institutions: " . count($registry->all()) . "\n";

// Test 1: Standard NUBAN (GTBank)
$serial = '012345678';
$checkDigit = Nuban::calculateCheckDigit('058', $serial);
$gtbAccount = $serial . $checkDigit;

echo "\n--- Test 1: Standard NUBAN ---\n";
echo "Testing: {$gtbAccount}\n";
$matches = $resolver->resolve($gtbAccount);
foreach ($matches as $bank) {
    echo "- {$bank->name} (Code: {$bank->code})\n";
}

// Test 2: 11-digit Phone Number (Starting with 0)
$phone11 = '08031234567';
echo "\n--- Test 2: 11-digit Phone Number (Starting with 0) ---\n";
echo "Testing: {$phone11}\n";
$matches = $resolver->resolve($phone11);
foreach ($matches as $bank) {
    echo "- {$bank->name} (Code: {$bank->code})\n";
}

// Test 3: 10-digit Phone Number (Stripped)
$phone10 = '8031234567';
echo "\n--- Test 3: 10-digit Phone Number (Stripped) ---\n";
echo "Testing: {$phone10}\n";
$matches = $resolver->resolve($phone10);
foreach ($matches as $bank) {
    echo "- {$bank->name} (Code: {$bank->code})\n";
}
