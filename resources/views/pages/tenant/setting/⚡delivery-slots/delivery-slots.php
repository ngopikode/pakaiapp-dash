<?php

use App\Shared\Traits\ShowsToast;
use App\Tenant\Data\DeliverySlotData;
use App\Tenant\Models\Core\DeliverySlot;
use App\Tenant\Services\DeliverySettingService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Component;

new #[Title('Slot Waktu Pengiriman')]
class extends Component
{
    use ShowsToast;

    protected ?DeliverySettingService $deliverySettingService = null;

    protected function deliverySettingService(): DeliverySettingService
    {
        return $this->deliverySettingService ??= app(DeliverySettingService::class);
    }

    #[Computed]
    public function slots()
    {
        return DeliverySlot::orderBy('start_time')->get();
    }

    #[Renderless]
    public function save(array $formData): void
    {
        $dto = new DeliverySlotData(
            name: $formData['name'] ?? '',
            startTime: $formData['start_time'] ?? '',
            endTime: $formData['end_time'] ?? '',
            maxOrders: (int) ($formData['max_orders'] ?? 0),
            isActive: (bool) ($formData['is_active'] ?? true),
        );

        $slotId = $formData['id'] ?? null;
        $slot = $slotId ? DeliverySlot::find($slotId) : null;

        try {
            $this->deliverySettingService()->saveSlot($dto, $slot);
            $this->toast('Slot waktu berhasil disimpan.', 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->toast('Gagal menyimpan slot waktu.', 'error');
        }
    }

    #[Renderless]
    public function delete(int $id): void
    {
        try {
            $slot = DeliverySlot::findOrFail($id);
            $this->deliverySettingService()->deleteSlot($slot);
            $this->toast('Slot waktu berhasil dihapus/dinonaktifkan.', 'success');
        } catch (\Exception $e) {
            $this->toast($e->getMessage(), 'error');
        } catch (\Throwable $e) {
            report($e);
            $this->toast('Gagal menghapus slot waktu.', 'error');
        }
    }
};
