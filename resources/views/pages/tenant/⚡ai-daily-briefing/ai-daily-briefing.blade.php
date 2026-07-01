<div class="card w-100 mb-3 border rounded-3 bg-body" style="border-color: rgba(182, 115, 50, 0.2) !important; position: relative; overflow: hidden;">
    <div class="card-header bg-transparent border-0 pt-2.5 pb-0 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-stars fs-6"></i>
            </div>
            <h6 class="fw-bold mb-0 text-body" style="font-size: 0.9rem;">AI Daily Briefing</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button wire:click="regenerate" wire:loading.attr="disabled" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-2.5 d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                <i wire:loading.remove wire:target="regenerate" class="bi bi-arrow-clockwise"></i>
                <span wire:loading wire:target="regenerate" class="spinner-border" style="width: 0.7rem; height: 0.7rem; border-width: 0.12em;" role="status" aria-hidden="true"></span>
                <span wire:loading.remove wire:target="regenerate">Muat Ulang</span>
                <span wire:loading wire:target="regenerate">Menganalisis...</span>
            </button>
            <span class="btn btn-sm btn-outline-secondary disabled rounded-pill py-1 px-2.5" style="font-size: 0.65rem;">
                Update Terkini
            </span>
        </div>
    </div>
    
    <div class="card-body p-3 pt-2">
        @php
            $insightHtml = str($insightText)->markdown(['html_input' => 'escape']);
            // Regex to format metrics/numbers: Bold, enlarged, and highlighted
            $formattedHtml = preg_replace_callback(
                '/(?![^<]*>)(?<!:)\b(?:Rp\s*)?[+-]?\d+(?:[\d.,%]*\d)?(?:%|\s*(?:pesanan|produk|transaksi|item|varian|kali))?\b(?!:)/i',
                function ($matches) {
                    return '<strong class="briefing-number">' . $matches[0] . '</strong>';
                },
                $insightHtml
            );
        @endphp

        <div class="markdown-content text-body">
            {!! $formattedHtml !!}
        </div>
    </div>

    <style>
        .markdown-content ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .markdown-content li {
            position: relative;
            background: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color-translucent) !important;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            line-height: 1.5;
            transition: all 0.2s ease-in-out;
        }
        
        .markdown-content li:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.02);
            border-color: rgba(var(--bs-primary-rgb), 0.25) !important;
        }
        
        /* Color-coded borders based on sequence */
        .markdown-content li:nth-child(1) {
            border-left: 4px solid #ef4444 !important; /* Red/Coral for Performa */
        }
        
        .markdown-content li:nth-child(2) {
            border-left: 4px solid #f59e0b !important; /* Amber for Perhatian */
        }
        
        .markdown-content li:nth-child(3) {
            border-left: 4px solid #3b82f6 !important; /* Blue for Rekomendasi */
        }
        
        .markdown-content p {
            margin-bottom: 0;
        }
        
        .markdown-content strong {
            color: var(--bs-body-color);
            font-weight: 700;
        }
        
        /* Custom styled numbers */
        .briefing-number {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--bs-primary);
            background: rgba(var(--bs-primary-rgb), 0.06);
            padding: 0.02rem 0.25rem;
            border-radius: 0.25rem;
            white-space: nowrap;
            display: inline-block;
            line-height: 1.1;
            margin: 0 0.1rem;
            vertical-align: baseline;
        }
    </style>
</div>
