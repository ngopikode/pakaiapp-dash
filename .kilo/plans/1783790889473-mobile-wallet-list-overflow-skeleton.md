# Plan: Polish dashboard KPI + Sales Trend UI

## Target
- `resources/views/pages/tenant/⚡dashboard/dashboard.blade.php`
- Optional only if needed: `resources/views/pages/tenant/⚡dashboard/dashboard.css`

## Goal
- Adapt visual direction from reference image without fake data.
- Improve only top KPI strip + `Tren Penjualan` chart area first.
- Keep existing Livewire data, filters, chart data, and ApexCharts logic.

## Decisions
- No full dashboard rewrite.
- No dummy score/complaints.
- No new dependencies.
- Keep Tailwind-only styling.
- Keep `@assets` ApexCharts as-is.

## Changes
1. Page canvas
   - Change `<main>` to soft cream/light canvas with dark fallback:
     - `rounded-[2rem] bg-[#f5f1df] p-4 md:p-6 ... dark:bg-slate-950`
   - Keep spacing and existing responsive flow.

2. Header/date filter
   - Keep current greeting and date filter behavior.
   - Slightly align with reference: cleaner white pill/card style.
   - Do not add global search/profile from screenshot.

3. KPI strip
   - Replace four separate KPI cards with one rounded white container on desktop:
     - `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`
     - each item has icon, label, value, small helper/trend.
     - desktop dividers between items via `lg:border-l` except first.
   - KPI mapping:
     - Total Revenue = `$stats['revenue_today']`
     - Transaksi = `$stats['orders_today']`
     - Pesanan Menunggu = `$stats['pending_orders']`
     - Profit Estimasi = `$stats['profit_month']`
   - Loading skeleton mirrors strip, not old standalone boxes.

4. Sales Trend chart card
   - Make card closer to screenshot:
     - larger rounded white panel.
     - header: title left, filter pills right using existing date filter buttons.
     - Keep current custom date dropdown still in header top area for now to avoid duplicate complexity.
   - Improve ApexCharts options only minimally:
     - `stroke.width = 4`
     - `fill.opacity = 0.08`
     - hide excessive grid border, keep smooth area.
     - tooltip currency remains.
   - Keep chart Livewire data/MutationObserver unchanged.

5. Keep rest of dashboard as-is
   - Smart Insight, top products, payment methods etc untouched unless classes need visual consistency.

## Validation
- Run:
  - `php -l resources/views/pages/tenant/⚡dashboard/dashboard.php`
  - `php -l resources/views/pages/tenant/⚡dashboard/dashboard.blade.php`
  - `composer lint:check` if available; if `pint: not found`, record env issue.
- Manual:
  - Desktop: KPI strip and chart look like adapted reference.
  - Mobile: KPI stacks cleanly, no horizontal overflow.
  - Date filters still update data.
  - Apex chart still renders after filter changes.

## Out of scope
- Full dashboard redesign.
- New score/complaints/widgets without data.
- Converting chart to multi-series products.
- New search/profile header.
