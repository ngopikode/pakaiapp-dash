<?php

use App\Tenant\Data\StoreSettingFormData;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\SettingService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Pengaturan Toko')]
class extends Component
{
    use WithFileUploads;

    protected ?SettingService $settingService = null;

    public int $settingId;

    public string $name = '';

    public string $theme_color = '#f59e0b';

    public ?string $whatsapp_number = '';

    public ?string $address = '';

    public bool $is_active = true;

    public string $store_type = 'resto';

    public bool $is_dinein_active = true;

    public bool $is_takeaway_active = true;

    public bool $is_delivery_active = true;

    public bool $is_tax_active = true;

    public float $tax_rate = 10.00;

    public bool $is_service_charge_active = true;

    public float $service_charge_rate = 5.00;

    public bool $is_application_fee_passed = false;

    public bool $is_kitchen_active = true;

    /** @var TemporaryUploadedFile|string|null */
    public $logo;

    /** @var TemporaryUploadedFile|string|null */
    public $og_image;

    public $new_logo;

    public $new_og_image;

    public string $hero_promo_text = 'Promo';

    public string $hero_status_text = 'Buka Sekarang';

    public string $hero_headline = 'Enjoy & Dine';

    public string $hero_tagline = 'Nikmati menu spesial kami.';

    public ?string $hero_instagram_url = '';

    public string $navbar_brand_text = 'Ez';

    public ?string $navbar_title = '';

    public string $navbar_subtitle = 'Menu Digital';

    public ?string $seo_title = '';

    public ?string $seo_description = '';

    public ?string $seo_keywords = '';

    public ?string $og_title = '';

    public ?string $og_description = '';

    public bool $use_same_hours = false;

    public array $operating_hours = [];

    protected function settingService(): SettingService
    {
        return $this->settingService ??= app(SettingService::class);
    }

    public function mount(): void
    {
        $setting = StoreSetting::cached();

        if (!$setting) return;

        $this->settingId = $setting->id;
        $this->name = $setting->name;
        $this->theme_color = $setting->theme_color;
        $this->whatsapp_number = $setting->whatsapp_number;
        $this->address = $setting->address;
        $this->is_active = $setting->is_active;
        $this->store_type = $setting->store_type ?? 'resto';
        $this->is_dinein_active = (bool) $setting->is_dinein_active;
        $this->is_takeaway_active = (bool) $setting->is_takeaway_active;
        $this->is_delivery_active = (bool) $setting->is_delivery_active;
        $this->is_tax_active = !isset($setting->is_tax_active) || (bool) $setting->is_tax_active;
        $this->tax_rate = isset($setting->tax_rate) ? (float) $setting->tax_rate : 10.00;
        $this->is_service_charge_active = !isset($setting->is_service_charge_active) || (bool) $setting->is_service_charge_active;
        $this->service_charge_rate = isset($setting->service_charge_rate) ? (float) $setting->service_charge_rate : 5.00;
        $this->is_application_fee_passed = (bool) ($setting->is_application_fee_passed ?? false);
        $this->is_kitchen_active = !isset($setting->is_kitchen_active) || (bool) $setting->is_kitchen_active;

        $this->logo = $setting->logo;
        $this->og_image = $setting->og_image;

        $this->hero_promo_text = $setting->hero_promo_text;
        $this->hero_status_text = $setting->hero_status_text;
        $this->hero_headline = $setting->hero_headline;
        $this->hero_tagline = $setting->hero_tagline;
        $this->hero_instagram_url = $setting->hero_instagram_url;

        $this->navbar_brand_text = $setting->navbar_brand_text;
        $this->navbar_title = $setting->navbar_title;
        $this->navbar_subtitle = $setting->navbar_subtitle;

        $this->seo_title = $setting->seo_title;
        $this->seo_description = $setting->seo_description;
        $this->seo_keywords = $setting->seo_keywords;
        $this->og_title = $setting->og_title;
        $this->og_description = $setting->og_description;

        $this->use_same_hours = (bool) ($setting->use_same_hours ?? false);
        $loaded = $setting->operating_hours ?? [];
        $default = ['open' => '08:00', 'close' => '22:00', 'is_closed' => false];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $this->operating_hours = array_merge(
            ['default' => $loaded['default'] ?? $default],
            array_combine($days, array_map(fn ($d) => $loaded[$d] ?? $default, $days))
        );
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'theme_color' => 'required|string|max:7',
            'new_logo' => 'nullable|image|max:2048',
            'new_og_image' => 'nullable|image|max:2048',
        ]);

        $dto = new StoreSettingFormData(
            name: $this->name,
            themeColor: $this->theme_color,
            whatsappNumber: $this->whatsapp_number,
            address: $this->address,
            isActive: $this->is_active,
            storeType: $this->store_type,
            isDineinActive: $this->is_dinein_active,
            isTakeawayActive: $this->is_takeaway_active,
            isDeliveryActive: $this->is_delivery_active,
            isTaxActive: $this->is_tax_active,
            taxRate: $this->tax_rate,
            isServiceChargeActive: $this->is_service_charge_active,
            serviceChargeRate: $this->service_charge_rate,
            isApplicationFeePassed: $this->is_application_fee_passed,
            isKitchenActive: $this->is_kitchen_active,
            heroPromoText: $this->hero_promo_text,
            heroStatusText: $this->hero_status_text,
            heroHeadline: $this->hero_headline,
            heroTagline: $this->hero_tagline,
            heroInstagramUrl: $this->hero_instagram_url,
            navbarBrandText: $this->navbar_brand_text,
            navbarTitle: $this->navbar_title,
            navbarSubtitle: $this->navbar_subtitle,
            seoTitle: $this->seo_title,
            seoDescription: $this->seo_description,
            seoKeywords: $this->seo_keywords,
            ogTitle: $this->og_title,
            ogDescription: $this->og_description,
            useSameHours: $this->use_same_hours,
            operatingHours: $this->operating_hours,
            logo: $this->logo,
            ogImage: $this->og_image,
        );

        try {
            $this->settingService()->saveFromForm(
                StoreSetting::cached(),
                $dto,
                $this->new_logo,
                $this->new_og_image,
            );

            $this->new_logo = null;
            $this->new_og_image = null;

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengaturan toko berhasil disimpan!']);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan pengaturan.']);
        }
    }
};
