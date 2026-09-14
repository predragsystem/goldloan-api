# GoldLoan visual design notes

Not a Blade file — just the rationale, so nobody "fixes" this back into a
generic SaaS look later.

**Subject**: back-office software for Indian gold-loan/pawnbroking lenders.
**Audience**: shop owner + cashier, not technical, currently running this on
paper or a spreadsheet. Needs to feel precise and trustworthy, not flashy.

## Palette
- `ink` #1E2A22 — deep vault-green-black. Primary dark (sidebar, hero).
- `paper` #F2EFE6 — warm ledger-paper background.
- `paper-line` #C9C2AC — hairline rule color on paper (ledger-line motif).
- `brass` #9C7A3C / `brass-dark` #7C5F2C — the one accent color, used
  sparingly (primary buttons, active nav state, the hero ticket stamp).
  A deliberate reference to gold/brass, not a generic accent.
- `success` #3D6B4F / `alert` #7A3B2E — status colors (active/paid vs
  overdue/expired), not used decoratively elsewhere.

## Type
- Display: **Zilla Slab** (slab serif) — headings only. Sturdy, stamp-like,
  fits a ledger/registry subject without being a cliché "AI serif."
- Body/UI: **IBM Plex Sans** — everything else.
- Loan amounts use `.tabular` (tabular-nums) so figures align in tables —
  a functional choice, not a monospace-label decoration.

## Structural device
Hairline rules between sections (the `paper-line` color) instead of
rounded shadow cards — echoes ledger paper. Pricing is shown as divided
rows, not three identical cards. The hero's one bold move is a stylized
pledge-ticket visual; everything else stays quiet.
