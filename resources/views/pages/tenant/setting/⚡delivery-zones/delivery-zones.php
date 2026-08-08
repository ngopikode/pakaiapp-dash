<?php

use App\Shared\Traits\ShowsToast;
use App\Tenant\Data\DeliveryZoneData;
use App\Tenant\Models\Core\DeliveryZone;
use App\Tenant\Services\DeliverySettingService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Component;

new #[Title('Zona & Area Ongkir')]
class extends Component
{
    use ShowsToast;

    protected ?DeliverySettingService $deliverySettingService = null;

    protected function deliverySettingService(): DeliverySettingService
    {
        return $this->deliverySettingService ??= app(DeliverySettingService::class);
    }

    #[Computed]
    public function zones()
    {
        return DeliveryZone::latest()->get();
    }

    #[Renderless]
    public function save(array $formData): void
    {
        $dto = new DeliveryZoneData(
            name: $formData['name'] ?? '',
            shippingCost: (float) ($formData['shipping_cost'] ?? 0),
            minFreeShipping: (float) ($formData['min_free_shipping'] ?? 0),
            isActive: (bool) ($formData['is_active'] ?? true),
        );

        $zoneId = $formData['id'] ?? null;
        $zone = $zoneId ? DeliveryZone::find($zoneId) : null;

        try {
            $this->deliverySettingService()->saveZone($dto, $zone);
            $this->toast('Zona ongkir berhasil disimpan.', 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->toast('Gagal menyimpan zona.', 'error');
        }
    }

    #[Renderless]
    public function delete(int $id): void
    {
        try {
            $zone = DeliveryZone::findOrFail($id);
            $this->deliverySettingService()->deleteZone($zone);
            $this->toast('Zona berhasil dihapus/dinonaktifkan.', 'success');
        } catch (\Exception $e) {
            $this->toast($e->getMessage(), 'error');
        } catch (\Throwable $e) {
            report($e);
            $this->toast('Gagal menghapus zona.', 'error');
        }
    }
};
