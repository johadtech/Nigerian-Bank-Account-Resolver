# Nigerian Bank Account Number Resolver (PHP / Laravel)

A production-grade, zero-dependency, highly scalable PHP package for real-time Nigerian bank account number resolution, NUBAN Modulo 10 validation, and NIP transfer code lookup. This package is designed for **offline reliability**, serving bank logos from local storage rather than external APIs.

---

## 🚀 How It Works

### 1. Offline-First Asset Management
Unlike other solutions that query external CDNs or APIs for bank logos, this package includes a local `assets/logos/` directory. All bank logos are stored locally, identified by their unique bank codes. This ensures:
- **Zero Latency**: No network requests needed to display logos.
- **Reliability**: Logos remain available even if external image hosts go down.
- **Privacy**: No tracking or external requests triggered during user interaction.

### 2. Comprehensive Institutional Coverage
The resolver loads an exhaustive, up-to-date dataset of **all 278+ active licensed financial institutions** in Nigeria. Every institution includes its official NIP transfer code, NUBAN 3-digit prefix, slug, and a reference to its local logo file.

### 3. The NUBAN Standard & Modulo 10 Algorithm
The resolver validates account numbers using the official Modulo 10 checksum algorithm with standardized weighting vectors `[3, 7, 3, 3, 7, 3, 3, 7, 3, 3, 7, 3]` applied to the 3-digit bank prefix and the 9-digit serial number.

### 4. Intelligent Multi-Bank Candidate Suggestion
Due to mathematical overlaps in Modulo 10 checksums, typing a 10-digit account number may yield more than one valid candidate. The resolver returns **all possible matching banks**, allowing your UI to prompt the user for selection.

### 5. Mobile Phone Number Handling (Fintechs/MFBs)
Many fintechs (OPay, PalmPay, etc.) use mobile phone numbers as account identifiers. The resolver robustly handles these cases:
- **11-digit inputs**: If a user enters an 11-digit number starting with `0` (e.g., `08031234567`), the resolver automatically strips the leading zero to align with the 10-digit account mapping used by fintech platforms.
- **10-digit inputs**: Directly resolves stripped phone numbers (e.g., `8031234567`).

---

## 📦 Directory Structure

```text
nigerian-bank-resolver/
├── src/
│   ├── Bank.php
│   ├── BankRegistry.php
│   ├── Nuban.php
│   └── Resolver.php
├── data/
│   └── banks.json (Full 278+ institutions catalog)
├── assets/
│   └── logos/ (Local circular bank logos)
├── tests/
│   └── test_resolver.php
└── README.md
```

---

## 🛠️ How to Use

### Pure PHP Usage

```php
<?php

require_once 'src/Bank.php';
require_once 'src/BankRegistry.php';
require_once 'src/Nuban.php';
require_once 'src/Resolver.php';

use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Resolver;

// Initialize registry and resolver
$registry = new BankRegistry();
$resolver = new Resolver($registry);

// Resolve account number (handles standard NUBAN or phone numbers)
$accountNumber = '08031234567'; 
$matches = $resolver->resolve($accountNumber);

foreach ($matches as $bank) {
    echo "Bank Name: {$bank->name}\n";
    echo "Local Logo Path: {$bank->logoPath}\n\n";
}
```

### Laravel Framework Integration

#### 1. Public Assets
Ensure the `assets/logos/` directory is accessible. You can move the logos to `public/vendor/nigerian-bank-resolver/logos/` and update the paths in `banks.json` or handle them via a symbolic link.

#### 2. Register Service Provider (`AppServiceProvider.php`)
```php
use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Resolver;

public function register(): void
{
    $this->app->singleton(Resolver::class, function ($app) {
        return new Resolver(new BankRegistry());
    });
}
```

---

## 🔒 Engineering Standards
- **4-Space Indentation**: Clean and professional formatting.
- **Offline Reliability**: Local assets prevent "broken image" issues.
- **Strict Typing**: Fully typed properties and methods (PHP 8.2+).
- **Complete Coverage**: Encompasses all 278+ active banks and fintechs in Nigeria.
