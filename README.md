# Nigerian Bank Account Resolver (PHP)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-777bb4.svg)](https://www.php.net/)

A zero-dependency PHP library for suggesting Nigerian financial institutions from account-number and phone-number input. The resolver validates 10-digit NUBAN candidates with the NUBAN Modulo 10 algorithm and supports evidence-backed phone-account suggestions without making network requests during resolution.

> **Current scope:** The resolver contains 281 local institution records. It is an operational registry, not a complete copy of every CBN-regulated institution. The archived CBN research snapshot contains 1,064 records across ten categories, including institutions that do not necessarily issue ordinary customer deposit accounts.

## Features

- **Offline-first resolution:** All runtime data and logo assets are committed locally; no logo or institution lookup is performed through a remote API.
- **NUBAN validation:** Uses the 12-digit weighting vector defined for the NUBAN Modulo 10 calculation.
- **Multiple candidates:** Returns every institution whose configured prefix and checksum match the supplied 10-digit account number.
- **Evidence-driven phone handling:** National 11-digit input and normalized 10-digit input are supported. Phone-account suggestions are limited to institutions explicitly documented in `data/phone-resolution.json`.
- **Current documented phone capabilities:** Moniepoint MFB, OPay, and 9 Payment Service Bank (9PSB). The dataset does not infer phone-account behavior for Kuda, Carbon, PalmPay, VFD, or any other institution without direct evidence.
- **Local circular logos:** Production logo assets are stored in `assets/logos/` as 256×256 PNG files. Newly reviewed assets and their source notes are recorded in `docs/logo-research-notes.md`.
- **Laravel-compatible:** The package uses plain PHP classes and can be loaded through Composer or manual `require_once` statements.
- **Strict input handling:** Non-numeric input returns no matches, and oversized input is rejected rather than silently truncated.

## Important accuracy boundary

The resolver suggests possible institutions. It does **not** confirm account ownership, account name, account status, transfer completion, or whether a particular account can receive funds. Applications must use a regulated account-validation or name-enquiry provider for final verification before executing a payment.

A CBN listing establishes that an institution appeared in the archived regulator snapshot; it does not automatically prove that the institution currently issues customer account numbers, participates in every transfer rail, or has a verified NIP code. Transfer codes, NUBAN prefixes, account rules, and logos remain separate fields and are not invented when evidence is unavailable.

## Directory structure

```text
.
├── src/
│   ├── Bank.php                 # Immutable institution value object
│   ├── BankRegistry.php         # Offline dataset and evidence loader
│   ├── Nuban.php                # NUBAN Modulo 10 validation
│   └── Resolver.php             # Account and phone resolution service
├── data/
│   ├── banks.json               # Local resolver records
│   ├── phone-resolution.json    # Source-backed phone-account capabilities
│   └── source-cbn-mfb-2026-08-23.json
├── assets/
│   └── logos/                   # Local circular 256×256 PNG assets
├── docs/
│   ├── account-eligibility-evidence.md
│   ├── cbn-coverage-audit.json
│   ├── logo-research-notes.md
│   ├── missing-cbn-institutions.md
│   └── research-notes.md
├── tests/
│   └── test_resolver.php        # Framework-free behavioral tests
├── composer.json
└── README.md
```

## Installation

With Composer:

```bash
composer dump-autoload
```

For a manual integration, copy `src/`, `data/`, and `assets/` into the application and load the classes in dependency order:

```php
require_once __DIR__ . '/src/Bank.php';
require_once __DIR__ . '/src/BankRegistry.php';
require_once __DIR__ . '/src/Nuban.php';
require_once __DIR__ . '/src/Resolver.php';
```

## Basic usage

```php
<?php

declare(strict_types=1);

use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Resolver;

$registry = new BankRegistry();
$resolver = new Resolver($registry);

$matches = $resolver->resolve('08031234567');

foreach ($matches as $bank) {
    echo $bank->name . PHP_EOL;
    echo 'Code: ' . ($bank->code ?? 'unknown') . PHP_EOL;
    echo 'Logo: ' . ($bank->logoPath ?? 'unknown') . PHP_EOL;
}
```

The same resolver accepts a normalized 10-digit phone representation such as `8031234567`. A clearly formatted 11-digit national phone number returns only the documented phone-capable institutions. A normalized 10-digit value is ambiguous with a NUBAN, so valid NUBAN matches may be returned together with documented phone-account matches.

## Laravel integration

Register the resolver as a singleton in `AppServiceProvider`:

```php
use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Resolver;

public function register(): void
{
    $this->app->singleton(Resolver::class, static function (): Resolver {
        return new Resolver(new BankRegistry());
    });
}
```

Use it from a controller after validating the request field server-side:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use NigerianBankResolver\Resolver;

final class BankController extends Controller
{
    public function resolve(Request $request, Resolver $resolver)
    {
        $validated = $request->validate([
            'account_number' => ['required', 'string', 'max:20'],
        ]);

        return response()->json([
            'matches' => $resolver->resolve($validated['account_number']),
        ]);
    }
}
```

## Resolution logic

The resolver follows this sequence:

1. Remove presentation characters while preserving every digit; it never truncates oversized input.
2. Treat an 11-digit value beginning with `0` and followed by a 10-digit Nigerian mobile shape as national phone input.
3. Return only source-backed phone-capable institutions for clearly formatted national phone input.
4. Validate 10-digit candidates against configured three-digit prefixes using the NUBAN checksum.
5. For normalized 10-digit mobile-shaped input, merge valid NUBAN matches with documented phone-account institutions and remove duplicate slugs.
6. Return an empty list for malformed or unsupported lengths.

The NUBAN check digit is calculated from the configured three-digit institution prefix plus the nine-digit serial using the weights `[3, 7, 3, 3, 7, 3, 3, 7, 3, 3, 7, 3]`. Records without a verified prefix can still support a documented phone-account capability, but they are never used for NUBAN validation.

## Institutions and evidence

The resolver retains the user-confirmed operational entries Access Bank (Diamond), ALAT by WEMA, Eyowo, Flutterwave MFB, Paga, Paystack MFB, Pocket App, Zap, and Heritage Bank. Their presence in this operational registry does not replace institution-level verification of current status or account ownership.

The complete CBN name-level comparison queue is in [`docs/missing-cbn-institutions.md`](docs/missing-cbn-institutions.md). It is intentionally not bulk-imported: many records are finance companies, mortgage institutions, holding companies, discount houses, or other regulated entities whose customer-account and transfer eligibility has not been established for this resolver.

The current account-eligibility evidence is in [`docs/account-eligibility-evidence.md`](docs/account-eligibility-evidence.md). The current phone capability contract is in [`data/phone-resolution.json`](data/phone-resolution.json). The research notes identify the source URL and the exact boundary of every documented claim.

## Local logos

Runtime code does not query logo URLs. Each record points to a local path under `assets/logos/`. Newly captured or replaced assets are normalized to 256×256 circular PNGs. Third-party logo directories are treated as discovery leads only; institution-owned websites and official app listings are preferred. Asset provenance and remaining logo work are recorded in [`docs/logo-research-notes.md`](docs/logo-research-notes.md).

## Testing and validation

Run the framework-free resolver tests:

```bash
php tests/test_resolver.php
```

The test suite verifies that 281 records load, valid NUBAN input matches the expected institution, national and normalized phone input return documented phone-capable records, unsupported institutions are not added by heuristic, oversized input is rejected, and malformed input returns no matches.

Run PHP syntax checks and JSON validation when changing the data or implementation:

```bash
php -l src/Bank.php
php -l src/BankRegistry.php
php -l src/Nuban.php
php -l src/Resolver.php
jq empty data/banks.json data/phone-resolution.json
```

## Data update policy

New institutions must not be added solely because they appear in a third-party directory or a CBN category listing. Before addition, the update must establish that the institution is real, currently relevant to the intended resolver scope, and provides customer account numbers or documented phone-based account identifiers usable to send or receive money. The update must then separately verify the institution identifier, NUBAN prefix or phone rule, local logo source, and any legal or redistribution constraints.

Unknown values remain `null` or excluded. A missing transfer code is not replaced with a USSD code, SWIFT/BIC code, guessed NIP code, or a code copied from an untyped directory.

## License

This project is released under the [MIT License](LICENSE). Institution names, regulatory information, and brand assets remain subject to their respective source terms and applicable law.
