# Alpine.js x-data Initialization with Livewire DOM Morphing

## Background

On the Product Catalog page (`product/⚡index/index.blade.php`), the goal was to move heavy UI state — multi-selection (`selected` array), view mode (`viewMode`), mobile filter toggle — from server-side (Livewire) to client-side (Alpine.js). This reduces server payload and eliminates round-trips for trivial interactions like checking a checkbox.

## The Problem

After extracting all Alpine logic into a separate `@script` block with `Alpine.data('productCatalog', () => ({...}))`, the following console errors appeared repeatedly:

```
Uncaught ReferenceError: selected is not defined
   at [Alpine] selected.includes('13') (eval at safeAsyncFunction ...)

Alpine Expression Error: showMobileFilters is not defined
Alpine Expression Error: statusDropdownOpen is not defined
Alpine Expression Error: viewMode is not defined
Alpine Expression Error: selectAll is not defined
```

### Root Cause: Race Condition with Livewire DOM Morphing

Livewire re-renders components by **morphing** (diffing + patching) the DOM. When the old DOM is replaced:

1. Livewire compiles and runs `@script` blocks **after** the component renders.
2. Alpine evaluates DOM directives (`x-data`, `x-show`, etc.) **synchronously** during morphing.
3. If `Alpine.data('productCatalog', ...)` hasn't been registered yet when Alpine encounters `x-data="productCatalog"`, Alpine throws a ReferenceError.
4. The component never initializes → all template expressions (`selected.includes(...)`, `viewMode === 'list'`) fail.

### Additional Issues Found

| Issue | Description |
|-------|-------------|
| **HTML escaping** | Double quotes inside `x-data="{ ... document.querySelectorAll('[wire\\\\:key^=\"row-\"]') ... }"` broke the HTML parser. The parser saw `\"` as the end of the `x-data` attribute. |
| **Nested x-data scopes** | `<div x-data="{ open: false }">` inside the component created a new Alpine scope. The child scope couldn't access `selected` from the parent scope. |
| **Livewire.hook in init()** | `Livewire.hook('commit', ...)` inside Alpine's `init()` registered a new global hook every time the component re-initialized during morphing, causing duplicate callbacks. |

## The Solution

### Use Inline `x-data` — Not `Alpine.data()`

For Livewire components that frequently morph (filter, search, paginate), define Alpine state directly in the HTML attribute:

```html
<div x-data="{
    selected: [],
    viewMode: localStorage.getItem('productViewMode') || 'list',
    toggleSelect(id) {
        id = String(id);
        const idx = this.selected.indexOf(id);
        if (idx > -1) {
            this.selected.splice(idx, 1);
        } else {
            this.selected.push(id);
        }
    },
    init() { ... }
}">
```

This is **100% resistant to race conditions** because the data blueprint is embedded synchronously in the DOM node itself. No external registration is needed.

### Always Use Single Quotes Inside `x-data`

Inside the HTML attribute `x-data="..."`, any `"` (double quote) will terminate the attribute. Always use single quotes for JS strings:

```javascript
// ✅ SAFE — single quotes inside double-quoted HTML attribute
x-data="{
    total: document.querySelectorAll('.row').length
}"

// ❌ BROKEN — double quotes inside double-quoted HTML attribute
x-data="{
    total: document.querySelectorAll('[wire\\\\:key^=\"row-\"]').length
}"
```

### Avoid Nested Alpine `x-data`

Keep all state in a single, top-level `x-data` directive. If you need a local state (e.g., dropdown open/close), promote it to the parent component:

```html
<!-- ✅ CORRECT — all state in one x-data -->
<div x-data="{ dropdownOpen: false, selected: [] }">
    <button @click="dropdownOpen = !dropdownOpen">Toggle</button>
    <div x-show="dropdownOpen">...</div>
</div>
```

```html
<!-- ❌ WRONG — nested scope can't access parent state -->
<div x-data="{ selected: [] }">
    <div x-data="{ open: false }">
        <!-- 🚫 'selected' is NOT accessible here -->
    </div>
</div>
```

### Avoid `Livewire.hook()` Inside `Alpine.init()`

`Livewire.hook` is a global registration. Calling it inside Alpine's `init()` registers a new global hook every time the component re-initializes, causing duplicate callbacks and memory leaks.

```javascript
// ✅ CORRECT — use $wire.on() (scoped to component)
init() {
    $wire.on('clear-selection', () => {
        this.selected = [];
    });
}

// ❌ WRONG — registers global hook on every init
init() {
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => this.clearSelection());
    });
}
```

### Use `Alpine.evaluate()` from `$wire.on()` in `@script`

When you need to update Alpine state from a Livewire event listener (in `@script`), use `Alpine.evaluate(element, expression)`:

```javascript
$wire.on('show-category-modal', () => {
    Alpine.evaluate(document.getElementById('categoryModal'), 'open = true');
});
```

This is the official Alpine v3 public API — no internal `__x` or `_x_dataStack` access needed.

## Summary

| Pattern | Status | Reason |
|---------|--------|--------|
| `Alpine.data()` + `@script` | ❌ Avoid | Race condition with Livewire morphing |
| Inline `x-data="{...}"` | ✅ Use | Synchronous, no registration needed |
| `\""` inside `x-data` | ❌ Broken | Parser terminates attribute early |
| Nested `x-data` scopes | ❌ Avoid | Child scope can't access parent state |
| `Livewire.hook()` in `init()` | ❌ Avoid | Registers duplicates on each init |
| `$wire.on()` in `@script` | ✅ Use | Scoped to component, no duplicates |
| `Alpine.evaluate(el, expr)` | ✅ Use | Official public API for state updates |
