<div class="card dash-card bg-body border w-100 mb-4" style="border-color: rgba(182, 115, 50, 0.4) !important; position: relative; overflow: hidden; min-height: 180px;">
    {{-- Decorative Subtle Glow --}}
    <div class="position-absolute" style="width: 200px; height: 200px; background: rgba(182, 115, 50, 0.06); filter: blur(45px); top: -80px; left: -80px; pointer-events: none;"></div>

    <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex justify-content-between align-items-center position-relative z-1">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                <i class="bi bi-robot fs-6"></i>
            </div>
            <h6 class="fw-bold mb-0 text-body" style="font-family: var(--font-serif), sans-serif; letter-spacing: -0.3px;">AI Daily Briefing</h6>
        </div>
        <span class="badge rounded-pill bg-body-tertiary border text-secondary shadow-sm" style="font-size: 0.65rem;">
            Update Terkini
        </span>
    </div>
    
    <div class="card-body p-3 pt-1 position-relative z-1">
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
            gap: 0.75rem;
        }
        
        .markdown-content li {
            position: relative;
            background: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color-translucent) !important;
            border-radius: 1rem;
            padding: 0.85rem 1rem;
            font-size: 0.85rem;
            line-height: 1.6;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .markdown-content li:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.03);
            border-color: rgba(var(--bs-primary-rgb), 0.25) !important;
            background: rgba(var(--bs-primary-rgb), 0.01);
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
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--bs-primary);
            background: rgba(var(--bs-primary-rgb), 0.08);
            padding: 0.05rem 0.35rem;
            border-radius: 0.35rem;
            white-space: nowrap;
            display: inline-block;
            line-height: 1.1;
            margin: 0 0.1rem;
            vertical-align: baseline;
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</div>
