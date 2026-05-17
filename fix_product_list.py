import re

with open('/var/www/pakaiapp-dash/resources/views/pages/tenant/store/⚡product-list/product-list.blade.php', 'r') as f:
    content = f.read()

# Replace $item->prop with $item['prop']
def replace_item(match):
    prop = match.group(1)
    return f"$item['{prop}']"

content = re.sub(r'\$item->([a-zA-Z_]+)', replace_item, content)

# Remove the old @php block inside foreach
content = re.sub(
    r'@php\s+\$qtyInCart\s*=\s*\$this->getQtyInCart\(\$item\[\'id\'\]\);\s+\$showStepper\s*=\s*!\s*\$item\[\'has_variants\'\]\s*&&\s*\$qtyInCart\s*>\s*0;\s+\$delay\s*=\s*\$index\s*<\s*20\s*\?\s*\$index\s*\*\s*50\s*:\s*0;\s+@endphp',
    r'@php $delay = $index < 20 ? $index * 50 : 0; @endphp',
    content,
    flags=re.MULTILINE
)

# Update the div wrapper
content = re.sub(
    r'<div\s+wire:key="product-\{\{\s*\$item\[\'id\'\]\s*\}\}".*?(?=\n\s*\{\{-- Image --\}\})',
    r'''<div
                    wire:key="product-{{ $item['id'] }}"
                    x-data="{ 
                        item: {{ htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') }},
                        get qtyInCart() { const i = cart.find(x => x.cartName === this.item.name); return i ? i.qty : 0; },
                        get showStepper() { return !this.item.has_variants && this.qtyInCart > 0; }
                    }"
                    class="bg-white rounded-2xl border border-zinc-100/80 shadow-sm flex group overflow-hidden relative transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 cursor-pointer animate-slide-up {{ $viewMode === 'grid' ? 'flex-col h-full' : 'flex-row items-center gap-4 p-3' }} {{ ! $item['is_active'] ? 'opacity-90' : '' }}"
                    :class="showStepper ? 'border-[var(--primary-color)]/40 ring-2 ring-[var(--primary-color)]/10' : ''"
                    style="animation-delay: {{ $delay }}ms"
                    @click="$dispatch('open-product-detail', { id: {{ $item['id'] }} })"
                >''',
    content,
    flags=re.DOTALL
)

# Replace Stepper logic with Alpine
content = re.sub(
    r'@if\(\$showStepper\).*?@else(.*?)@endif',
    r'''<template x-if="showStepper">
                                <div class="flex items-center justify-between bg-zinc-900 rounded-xl p-1 shadow-md">
                                    <button @click.stop="updateQty(item.name, -1)" class="w-8 h-8 flex items-center justify-center text-white hover:bg-zinc-700 rounded-lg transition-colors active:scale-90">
{ _ble_edit_exec_gexec__save_lastarg "$@"; } 4>&1 5>&2 &>/dev/null
