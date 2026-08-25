# JavaScript Demo Verification

**Date:** 2026-08-25

The demo was served locally from the `demo/` directory using a PHP static server and opened in Chromium. The page loaded 281 local resolver records, proving that the browser bundle can fetch the copied JSON files without a database or third-party API.

The sample `2170861119` rendered an explicit `ambiguous` result with 17 candidates, including United Bank For Africa and First Bank of Nigeria. The interface did not select either institution automatically and displayed the limitation that checksum-compatible candidates are not confirmed bank identity.

The browser loaded the local logo paths from `demo/assets/logos/`. No remote logo URL was used by the demo code.


## Institutional fintech redesign verification — 2026-08-25

The demo was reloaded after the visual redesign. The updated interface presents a dark navy institutional palette, IBM Plex Sans/Mono typography, a structured brand header, local-data status badge, system metrics, trust indicators, resolver workspace, and method cards.

The local data status displayed 281 records and the system metric rendered 281. The example shortcut populated the account field, and submitting `2170861119` displayed an explicit ambiguous result with 17 candidates, including UBA and First Bank, without automatically selecting either institution.

The result panel retained local logo paths and the browser-visible interface had no dependency on third-party APIs. The CSS uses a mobile-first base layout, touch-safe controls, and progressive two-column layouts at wider breakpoints.


## Responsive visual inspection — 2026-08-25

Headless renders were inspected at 375×800 and 1440×900. The mobile render uses a single-column flow, keeps the brand and local-data badge within the viewport, stacks the system card below the hero, and preserves readable typography without horizontal overflow. The desktop render expands into a two-column hero with visible navigation and a side system-status card while keeping the resolver workspace full width.

The institutional fintech direction is consistent across both sizes: dark navy surfaces, green trust/status accent, IBM Plex typography, monospace data labels, restrained borders, and explicit operational language. The local data and interaction states remain visible and legible.
