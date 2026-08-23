# Nigerian Payment Data Package: Implementation Strategy

## Current audit finding

The existing flat dataset is not safe to describe as fully verified. It contains mixed identifier formats, non-numeric values in fields used by the resolver, duplicate logo fields, no provenance, no status model, no aliases, and no update timestamp. The current registry also silently accepts malformed records, while the resolver treats a small hard-coded fintech list as authoritative and applies the NUBAN algorithm to records that may not have a valid three-digit prefix.

The package will therefore distinguish between **institution identity**, **transfer identifiers**, **NUBAN rules**, **phone-number account rules**, **local assets**, and **source provenance**. Unsupported values will remain explicitly unknown rather than being replaced with fabricated defaults.

## Files affected

| File | Interaction and planned change |
|---|---|
| `data/banks.json` | Replace the undocumented flat contract with a versioned, provenance-aware data document. Preserve compatibility through a controlled migration path where possible. |
| `data/schema.json` | Define the machine-readable contract for institution status, category, codes, aliases, account rules, logo paths, and sources. |
| `src/Bank.php` | Convert the entity into a typed immutable value object that represents the normalized institution record without performing resolution logic. |
| `src/BankRegistry.php` | Validate and hydrate records, index them by stable identifiers, and reject malformed records with explicit exceptions rather than silently dropping them. |
| `src/Nuban.php` | Keep checksum calculation isolated, validate only supported numeric prefixes, and expose clear input constraints. |
| `src/Resolver.php` | Resolve only according to declared account rules. Phone matching will be data-driven and will not claim a bank match solely because an institution appears in a hard-coded list. |
| `src/Exceptions/*` | Add focused exceptions for malformed data and invalid resolver input. |
| `tests/test_resolver.php` | Replace output-only checks with assertions covering valid, invalid, ambiguous, unsupported, and phone-account cases. |
| `tests/test_data.php` | Validate schema shape, unique identifiers, numeric constraints, local logo existence, and provenance requirements. |
| `README.md` | Document installation, data limitations, resolution semantics, update workflow, Laravel integration, and source policy. |
| `CHANGELOG.md` | Record the contract change and migration implications. |
| `.gitignore` | Prevent generated reports, caches, and local environment files from entering the repository. |

## Logic flow

`Input -> normalization -> declared account-rule selection -> candidate generation -> NUBAN validation where supported -> deterministic de-duplication -> result with match reasons and uncertainty metadata`.

A phone number is not treated as a valid bank account number by default. It is considered only for institutions whose data record explicitly declares a phone-based account rule and whether the leading zero is retained or stripped. An institution with no verified rule produces no phone match.

## State management

The dataset is read-only application metadata. The registry loads one validated snapshot into memory per process. Updates happen by replacing a versioned data file and running the data-quality test suite; there is no runtime mutation and no network lookup during resolution.

## Edge cases

Null, empty, formatted, or non-digit input is normalized according to documented rules. Inputs with unsupported lengths return an explicit no-match result. Malformed data records fail during registry construction. Missing or external logo paths are reported by data-quality checks and are never silently substituted. Duplicate institution codes and duplicate stable IDs are rejected. Ambiguous matches are returned as multiple candidates with reasons rather than being arbitrarily collapsed.

## Test plan

The test suite will verify checksum behavior using known generated fixtures, invalid lengths and prefixes, ambiguous candidates, phone numbers with and without a leading zero, institutions with no declared phone rule, duplicate records, malformed codes, missing local assets, and documentation examples. Static checks will also enforce four-space indentation in PHP files and ensure no unsupported external logo URLs are present in runtime data.

## Open decisions

The authoritative source and effective date for each institution's active status, transfer code, and account-number rule must be recorded per record. Where an official source does not publish a rule, the field remains unknown and the resolver must not infer it from a name, logo, or third-party directory.
