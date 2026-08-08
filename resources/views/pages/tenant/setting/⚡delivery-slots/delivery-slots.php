<?php

use App\Shared\Traits\ShowsToast;
use App\Tenant\Data\DeliverySlotData;
use App\Tenant\Models\Core\DeliverySlot;
use App\Tenant\Services\DeliverySettingService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Slot Waktu Pengiriman')]
class extends Component {
    use ShowsToast;

    protected ?DeliverySettingService $deliverySettingService = null;

    protected function deliverySettingService(): DeliverySettingService
    {
        return $this->deliverySettingService ??= app(DeliverySettingService::class);
    }

    #[Computed]
    public function deliverySlots()
    {
        \Log::info('slots called!');
        return DeliverySlot::oldest('start_time')->get();
    }

    #[Renderless]
    public function save(array $formData): void
    {
        $dto = DeliverySlotData::from($formData);

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
