<div class="kds-container min-vh-100 p-3 p-md-4" wire:poll.10s>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-white fw-bold mb-1"><i class="bi bi-display me-2 text-warning"></i>Kitchen Display</h2>
            <p class="text-white-50 small mb-0">Monitor Pesanan Dapur secara Real-time</p>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="$refresh" wire:loading.attr="disabled" class="btn btn-outline-info btn-sm d-flex align-items-center gap-2" title="Refresh Data (Tanpa Reload Halaman)">
                <i class="bi bi-arrow-clockwise" wire:loading.class="spin-icon" wire:target="$refresh"></i> Refresh
            </button>
            <div class="badge bg-dark border border-secondary px-3 py-2 d-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-success" role="status"
                      style="width: 10px; height: 10px;"></span>
                <span class="text-white-50">Live Sync</span>
            </div>
            @if(auth()->user()->role === 'kitchen')
                <button wire:click="logout" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            @else
                <a href="{{ route('dashboard') }}" wire:navigate.hover
                   class="btn btn-outline-light btn-sm d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @endif
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-4 g-4">
        @forelse($kitchenBatches as $batch)
            @php $order = $batch['order']; @endphp
            <div class="col">
                <div
                    class="card h-100 kds-card {{ $batch['status'] === 'processing' ? 'border-warning' : 'border-secondary' }}">
                    <div
                        class="card-header border-bottom border-secondary d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-0 text-white fw-bold">#{{ str_replace('INV-', '', $order->invoice_code) }}{{ $batch['status'] === 'waiting' && $order->items->where('kitchen_status', '!=', 'waiting')->isNotEmpty() ? ' (Tambahan)' : '' }}</h5>
                            <small class="text-white-50"
                                   x-data="{ 
                                       timeAgo: '', 
                                       start: new Date('{{ \Carbon\Carbon::parse($batch['created_at'])->toIso8601String() }}'),
                                       updateTime() {
                                           let diff = Math.floor((new Date() - this.start) / 1000);
                                           if (diff < 60) {
                                               this.timeAgo = diff + ' detik lalu';
                                           } else if (diff < 3600) {
                                               let m = Math.floor(diff / 60);
                                               let s = diff % 60;
                                               this.timeAgo = m + 'm ' + s + 's lalu';
                                           } else {
                                               let h = Math.floor(diff / 3600);
                                               let m = Math.floor((diff % 3600) / 60);
                                               this.timeAgo = h + 'j ' + m + 'm lalu';
                                           }
                                       }
                                   }"
                                   x-init="updateTime(); setInterval(() => updateTime(), 1000)">
                                {{ \Carbon\Carbon::parse($batch['created_at'])->format('H:i') }}
                                (<span x-text="timeAgo">{{ \Carbon\Carbon::parse($batch['created_at'])->diffForHumans() }}</span>)
                            </small>
                        </div>
                        <div class="text-end">
                            @if($order->order_type === 'dinein')
                                <span class="badge bg-primary fs-6 py-2 px-3"><i class="bi bi-shop me-1"></i> Meja {{ $order->table_number ?? '-' }}</span>
                            @elseif($order->order_type === 'takeaway')
                                <span class="badge bg-info text-dark fs-6 py-2 px-3"><i class="bi bi-bag me-1"></i> Bungkus</span>
                            @elseif($order->order_type === 'online')
                                <span class="badge bg-success fs-6 py-2 px-3"><i class="bi bi-bicycle me-1"></i> Online</span>
                            @else
                                <span class="badge bg-secondary fs-6 py-2 px-3">{{ ucfirst($order->order_type) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($order->notes)
                            <div class="alert alert-dark border-secondary text-warning py-2 mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <strong>Catatan:</strong> {{ $order->notes }}
                            </div>
                        @endif

                        <ul class="list-group list-group-flush kds-items-list">
                            @foreach($batch['items'] as $item)
                                <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between align-items-start border-secondary">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold fs-5">{{ $item->product_name }}</div>
                                        @if($item->variant_name)
                                            <span class="text-white-50 small"><i class="bi bi-tag"></i> {{ $item->variant_name }}</span>
                                        @endif
                                        @if($item->note)
                                            <div class="text-warning small mt-1"><i class="bi bi-chat-text"></i>
                                                "{{ $item->note }}"
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark rounded-pill fs-5 px-3 py-2">x{{ $item->quantity }}</span>
                                        @if($batch['status'] === 'waiting')
                                            <button wire:click="markItemAsProcessing({{ $item->id }})" class="btn btn-warning btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; padding: 0;" title="Mulai Masak Item Ini">
                                                <i class="bi bi-play-fill fs-4"></i>
                                            </button>
                                        @elseif($batch['status'] === 'processing')
                                            <button wire:click="markItemAsReady({{ $item->id }})" class="btn btn-success btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; padding: 0;" title="Item Ini Siap">
                                                <i class="bi bi-check-lg fs-5"></i>
                                            </button>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-top border-secondary p-3">
                        @if($batch['status'] === 'waiting')
                            <button wire:click="markAsProcessing({{ $order->id }})"
                                    class="btn btn-outline-warning w-100 fw-bold py-3 fs-5" style="border-radius: 12px; border-width: 2px;">
                                <i class="bi bi-fire me-2"></i> Mulai Masak Semua
                            </button>
                        @elseif($batch['status'] === 'processing')
                            <button wire:click="markAsReady({{ $order->id }})"
                                    class="btn btn-outline-success w-100 fw-bold py-3 fs-5" style="border-radius: 12px; border-width: 2px;">
                                <i class="bi bi-check2-all me-2"></i> Semua Siap
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-white-50 mb-3">
                    <i class="bi bi-cup-hot" style="font-size: 5rem;"></i>
                </div>
                <h3 class="text-white fw-bold">Dapur Kosong</h3>
                <p class="text-white-50">Belum ada pesanan masuk. Koki bisa istirahat sejenak!</p>
            </div>
        @endforelse
    </div>
</div>



@script
{{-- Audio System Notification --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        let previousCount = 0;
        Livewire.hook('commit', ({component, commit, respond, succeed, fail}) => {
            succeed(({snapshot, effect}) => {
                if (snapshot.data.kitchenOrders) {
                    const currentCount = Object.keys(snapshot.data.kitchenOrders).length;
                    if (currentCount > previousCount && previousCount !== 0) {
                        try {
                            const ctx = new (window.AudioContext || window.webkitAudioContext)();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(600, ctx.currentTime);
                            osc.frequency.exponentialRampToValueAtTime(800, ctx.currentTime + 0.1);
                            gain.gain.setValueAtTime(0.2, ctx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.4);
                        } catch (e) {
                        }
                    }
                    previousCount = currentCount;
                }
            });
        });
    });
</script>
@endscript
