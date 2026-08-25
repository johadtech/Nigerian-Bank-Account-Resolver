# JavaScript Demo Verification

**Date:** 2026-08-25

The demo was served locally from the `demo/` directory using a PHP static server and opened in Chromium. The page loaded 281 local resolver records, proving that the browser bundle can fetch the copied JSON files without a database or third-party API.

The sample `2170861119` rendered an explicit `ambiguous` result with 17 candidates, including United Bank For Africa and First Bank of Nigeria. The interface did not select either institution automatically and displayed the limitation that checksum-compatible candidates are not confirmed bank identity.

The browser loaded the local logo paths from `demo/assets/logos/`. No remote logo URL was used by the demo code.
