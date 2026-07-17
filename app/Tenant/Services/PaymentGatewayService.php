<?php

namespace App\Tenant\Services;

use App\Central\Services\DuitkuService;
use App\Central\Services\MidtransService;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\TenantUser;
use Exception;
use Illuminate\Http\Client\ConnectionException;

class PaymentGatewayService
{
    protected ?DuitkuService $duitkuService = null;
    protected ?MidtransService $midtransService = null;

    protected function duitkuService(): DuitkuService
    {
        return $this->duitkuService ??= app(DuitkuService::class);
    }

    protected function midtransService(): MidtransService
    {
        return $this->midtransService ??= app(MidtransService::class);
    }

    private function resolveDuitkuDbMethod(string $paymentMethod): string
    {
        $method = strtoupper($paymentMethod);
        $isQris = in_array($method, ['QRIS', 'QRISC', 'NQ', 'SP', 'LQ', 'GQ'], true) || str_contains($method, 'QRIS');

        return $isQris ? 'qris' : 'transfer';
    }

    private function resolveEmail(?string $email): string
    {
        $email = trim($email ?? '');
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $manager = TenantUser::select('email')->where('role', 'manager')->first() ?? TenantUser::select('email')->first();
        return $manager?->email ?? 'noreply@pakaiapp.online';
    }

    /**
     * @param Order|null $order
     * @return void
     * @throws Exception
     */
    private function guardPayable(?Order $order): void
    {
        $isPayable = $order && ($order->status === 'pending' ||
                ($order->status === 'progress' && $order->amount_paid < $order->total_price));

        if (!$order || !$isPayable) throw new Exception('Pesanan tidak ditemukan atau sudah dibayar.');
    }

    /**
     * @param int $orderId
     * @param string|null $customerEmail
     * @return array
     * @throws Exception
     */
    private function getValidatedOrderAndCustomer(int $orderId, ?string $customerEmail): array
    {
        $order = Order::with('items')->find($orderId);
        $this->guardPayable($order);

        $customerDetail = [
            'firstName' => $order->customer_name ?: 'Pelanggan',
            'lastName' => '',
            'email' => $this->resolveEmail($customerEmail),
            'phoneNumber' => $order->customer_phone ?: '',
            'address' => 'Indonesia',
            'city' => 'Jakarta',
            'postalCode' => '00000',
        ];

        return [$order, $customerDetail];
    }

    /**
     * @param int $orderId
     * @param string $paymentMethod
     * @param string|null $customerEmail
     * @return array
     * @throws ConnectionException
     * @throws Exception
     */
    public function generateDuitku(int $orderId, string $paymentMethod, ?string $customerEmail): array
    {
        if (!config('duitku.enabled')) throw new Exception('Pembayaran digital Duitku sedang tidak aktif.');

        [$order, $customerDetail] = $this->getValidatedOrderAndCustomer($orderId, $customerEmail);

        $result = $this->duitkuService()->createInvoice($order, $customerDetail, $paymentMethod, tenant()->getTenantKey());

        $order->update([
            'payment_method' => $this->resolveDuitkuDbMethod($paymentMethod),
            'duitku_reference' => $result['reference'],
            'duitku_payment_url' => $result['payment_url'],
            'duitku_va_number' => $result['va_number'],
            'duitku_payment_method' => $paymentMethod,
        ]);

        return ['payment_url' => $result['payment_url']];
    }

    /**
     * @param int $orderId
     * @param string|null $customerEmail
     * @return array
     * @throws Exception
     */
    public function generateMidtrans(int $orderId, ?string $customerEmail): array
    {
        if (!config('midtrans.server_key')) throw new Exception('Pembayaran digital Midtrans sedang tidak aktif.');

        [$order, $customerDetail] = $this->getValidatedOrderAndCustomer($orderId, $customerEmail);
        $snapToken = $this->midtransService()->createSnapToken($order, $customerDetail, tenant()->getTenantKey());

        $order->update([
            'midtrans_snap_token' => $snapToken,
            'payment_method' => 'transfer',
            'midtrans_payment_type' => 'snap',
        ]);

        return ['snap_token' => $snapToken];
    }
}
