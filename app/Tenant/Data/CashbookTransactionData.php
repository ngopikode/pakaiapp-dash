<?php

namespace App\Tenant\Data;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;

class CashbookTransactionData extends Data
{
    public function __construct(
        #[In(['cash', 'bank'])]
        public string $wallet_type,
        
        #[In(['income', 'expense'])]
        public string $transaction_type,
        
        #[Min(1)]
        public float $amount,
        
        public string $description,
        public Carbon $transaction_date,
        public ?string $reference_code = null
    ) {}
}
