<?php

namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class StoreSettingFormData extends Data
{
    public function __construct(
        public string $name,
        public string $themeColor,
        public ?string $whatsappNumber,
        public ?string $address,
        public bool $isActive,
        public string $storeType,
        public bool $isDineinActive,
        public bool $isTakeawayActive,
        public bool $isDeliveryActive,
        public bool $isTaxActive,
        public float $taxRate,
        public bool $isServiceChargeActive,
        public float $serviceChargeRate,
        public bool $isApplicationFeePassed,
        public bool $isKitchenActive,

        public string $heroPromoText,
        public string $heroStatusText,
        public string $heroHeadline,
        public string $heroTagline,
        public string $heroInstagramUrl,

        public string $navbarBrandText,
        public string $navbarTitle,
        public string $navbarSubtitle,

        public string $seoTitle,
        public string $seoDescription,
        public string $seoKeywords,
        public string $ogTitle,
        public string $ogDescription,

        public bool $useSameHours,
        public array $operatingHours,

        public ?string $logo = null,
        public ?string $ogImage = null,
    ) {}
}
