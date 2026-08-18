# Nigerian Bank Account Resolver (PHP)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-777bb4.svg)](https://www.php.net/)

A production-grade, zero-dependency PHP package for real-time Nigerian bank account number resolution, NUBAN Modulo 10 validation, and NIP transfer code lookup. Designed for **offline reliability**, it serves bank logos locally, ensuring zero latency and high availability.

---

## 🚀 Key Features

- **280+ Institutions**: Comprehensive coverage of all active Nigerian commercial banks, merchant banks, PSBs, and microfinance banks/fintechs.
- **Official NUBAN Algorithm**: Implements the NIBSS Modulo 10 checksum using the full 12-digit weighting vector.
- **Fintech Intelligence**: Robustly handles mobile phone numbers as account identifiers (OPay, PalmPay, Moniepoint, Kuda, etc.).
- **Offline-First Assets**: Includes 270+ circularized bank logos stored locally to prevent broken images and network latency.
- **Clean Architecture**: PSR-compliant, strictly typed, and built with SOLID principles for easy scalability and maintenance.

---

## 📦 Directory Structure

```text
.
├── src/
│   ├── Bank.php          # Bank entity model
│   ├── BankRegistry.php  # Institution data management
│   ├── Nuban.php         # NIBSS NUBAN validation logic
│   └── Resolver.php      # Main resolution service
├── data/
│   └── banks.json        # Verified dataset of 280+ institutions
├── assets/
│   └── logos/            # Local circular bank logos (PNG)
├── tests/
│   └── test_resolver.php # Functional test suite
└── README.md
```

---

## 🛠️ Installation

Simply copy the `src/`, `data/`, and `assets/` directories into your project.

### Manual Integration
```php
require_once 'src/Bank.php';
require_once 'src/BankRegistry.php';
require_once 'src/Nuban.php';
require_once 'src/Resolver.php';
```

---

## 📖 How to Use

### Basic Usage (Pure PHP)

```php
use NigerianBankResolver\BankRegistry;
use NigerianBankResolver\Resolver;

// Initialize the resolver
$registry = new BankRegistry();
$resolver = new Resolver($registry);

// Resolve an account number (supports 10-digit NUBAN or 11-digit phone numbers)
$accountNumber = '08031234567'; 
$matches = $resolver->resolve($accountNumber);

foreach ($matches as $bank) {
    echo "Bank: " . $bank->name . PHP_EOL;
    echo "Code: " . $bank->code . PHP_EOL;
    echo "Logo: " . $bank->logoPath . PHP_EOL . PHP_EOL;
}
```

### Laravel Integration

#### 1. Register the Service
In your `AppServiceProvider.php`:
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

#### 2. Create a Controller
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use NigerianBankResolver\Resolver;

class BankController extends Controller
{
    public function resolve(Request $request, Resolver $resolver)
    {
        $matches = $resolver->resolve($request->account_number);
        return response()->json($matches);
    }
}
```

---

## 🧠 How It Works

### 1. NUBAN Validation
The package uses the official NIBSS NUBAN Modulo 10 algorithm. For a given 3-digit bank prefix and 9-digit serial number, it calculates the check digit using the weighting vector `[3, 7, 3, 3, 7, 3, 3, 7, 3, 3, 7, 3]`.

### 2. Phone Number Mapping
Many Nigerian fintechs map user phone numbers to account numbers.
- **11-digit input**: If starting with `0`, the resolver strips the leading zero.
- **10-digit input**: Directly processed and matched against fintech-specific identifiers.

### 3. Candidate Suggestion
If multiple banks share a valid checksum for the same account number, the resolver returns all candidates, allowing the user to select the correct institution.

---

## 🔒 Security & Standards
- **Indentation**: Strict 4-space indentation.
- **Typing**: Full PHP 8.2+ type safety.
- **Privacy**: No external API calls are made during resolution.

---

## 📄 License
This project is open-sourced under the [MIT License](LICENSE).
