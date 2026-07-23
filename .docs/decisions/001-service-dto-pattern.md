# Architecture Decision Record: Service Pattern & DTO Standardization

**Date:** 2026-07-24
**Status:** Accepted
**Deciders:** @ngopikode

## Context

Livewire components and API Controllers frequently contain complex business logic and direct database queries (e.g., filtering products, category slug generation with deduplication, availability toggling). This violates the Single Responsibility Principle, makes code difficult to test, and results in logic duplication (WET code).

The existing codebase already has Services (`OrderService`, `KitchenService`, `PaymentGatewayService`) and DTOs (`CreateOrderData`, `CheckoutData`), but the pattern was not consistently applied across new features.

## Decision

All business logic and database queries MUST be extracted to a Service class. The Livewire component / Controller is responsible ONLY for:

- Handling HTTP requests / Livewire events
- Authorization checks
- Mapping input to DTO
- Calling the appropriate Service method
- Dispatching notifications / returning responses

### 1. Service Instantiation Pattern

Services MUST be injected using the **Protected Lazy Getter** pattern — NOT constructor injection (which breaks Livewire hydration) and NOT direct `app()` calls inline:

```php
// ✅ CORRECT — lazy getter pattern
protected ?OrderService $orderService = null;

protected function orderService(): OrderService
{
    return $this->orderService ??= app(OrderService::class);
}

// Usage
$this->orderService()->processOrder($orderData, $items);
```

```php
// ❌ WRONG — inline app() call
app(OrderService::class)->processOrder($orderData, $items);
```

### 2. DTO for Structured Input

When a Service method requires more than 3 parameters, a Data Transfer Object MUST be created using `Spatie\LaravelData\Data`:

```php
use Spatie\LaravelData\Data;

readonly class ProductFilterData extends Data
{
    public function __construct(
        public string $search = '',
        public string $filterCategory = '',
        public string $sortField = 'newest',
        public int $perPage = 20,
    ) {}
}
```

### 3. Service Naming & Location

| Layer | Path | Example |
|-------|------|---------|
| Central Service | `app/Central/Services/` | `DuitkuService.php` |
| Tenant Service | `app/Tenant/Services/` | `ProductService.php` |
| Central DTO | `app/Central/Data/` | `RegisterTenantInputData.php` |
| Tenant DTO | `app/Tenant/Data/` | `CategoryData.php` |

### 4. Service Method Return Types

Service methods MUST return typed results. If the operation affects the UI directly, the Service should return the affected Model or void, not a response array. Response handling (toasts, dispatches) belongs in the Livewire layer.

```php
// ✅ CORRECT
public function toggleAvailability(Product $product): void
{
    $product->update(['is_active' => !$product->is_active]);
}

// ❌ WRONG — dispatching UI events from Service
public function toggleAvailability(Product $product): void
{
    $product->update(['is_active' => !$product->is_active]);
    $this->dispatch('notify', ...); // ← Controller/Livewire concern
}
```

## Consequences

- **Positive:** Livewire components become thin (Thin Controller, Fat Service). Services can be reused across Livewire, API Controllers, Console Commands, and Jobs.
- **Positive:** DTOs provide a typed contract for method inputs, eliminating guesswork (`$orderData['customer_name'] ?? 'Pelanggan Umum'`).
- **Negative:** Developers must create DTO + Service files for features that previously were self-contained in a single view component.
- **Migration:** Existing components should be refactored incrementally. New features MUST follow this pattern from day one.

## Related Files

- `app/Tenant/Services/CategoryService.php` — example of save + delete with DTO
- `app/Tenant/Services/ProductService.php` — example of query builder + bulk operations
- `app/Tenant/Data/CategoryData.php` — example DTO
- `app/Tenant/Data/ProductFilterData.php` — example DTO
- `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.php` — reference implementation of lazy getter pattern
