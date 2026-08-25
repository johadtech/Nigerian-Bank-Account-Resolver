# Resolver demo design research

## Purpose

The existing demo was rejected because it remained visually close to a generic AI-generated fintech dashboard. This research identifies patterns from established fintech and developer products that can inform a genuinely different interface without copying any brand.

## Findings

### Mercury and Wise: calm trust through hierarchy

The reviewed analyses describe Mercury as editorial and restrained, using typography and disciplined layout to make banking feel credible. Wise uses transparency and progressive disclosure: important amounts, fees, delivery timing, and transfer state are shown clearly rather than hidden behind decoration. For the resolver, this supports a single focused task with a plain-language explanation and visible uncertainty.

### Stripe and Plaid: operational inspection

Stripe is described as an inspection-oriented product where tables, statuses, error messages, and drill-downs are useful because they correspond to actions a developer can take. Plaid’s infrastructure patterns emphasize environment state, institution coverage, connection health, and developer-facing diagnostics. For the resolver, a compact “local index” and explicit rule/match status are more meaningful than ornamental statistics.

### Revolut and Chime: mobile-first progressive disclosure

The reviewed material highlights clear primary actions, high contrast, shallow navigation, and gradual disclosure on mobile. The resolver should prioritize one input action, show only the necessary result summary first, and reveal candidate details beneath it.

### Product decision

The new concept is a **field notebook / payment-rail index**, not a dashboard. It should feel like a practical instrument used by a payments engineer: warm paper or off-white canvas, dark ink, one strong editorial accent, ruled separators, a distinctive index marker, compact metadata, and a single central inspection action. It should avoid neon gradients, glassmorphism, oversized metric cards, excessive pills, synthetic glow, and generic “AI confidence” language.

## References

1. [AdminLTE — Fintech Dashboard Design: 9 Real Products, Analyzed](https://adminlte.io/blog/fintech-dashboard-design-examples/)
2. [Masterly — Fintech Dashboard Design: Patterns & Real Examples](https://www.themasterly.com/blog/fintech-dashboard-design-guide)
3. [Eleken — 15 Trusted Fintech UI Examples](https://www.eleken.co/blog-posts/trusted-fintech-ui-examples)
4. [LogRocket — Fintech UX Design: What the Best Finance Apps Get Right](https://blog.logrocket.com/ux-design/great-examples-fintech-ux/)
