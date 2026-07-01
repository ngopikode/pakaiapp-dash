<?php

namespace App\Central\Controllers;

use App\Central\Data\CentralLoginInputData;
use App\Central\Data\RegisterTenantInputData;
use App\Central\Data\RequestOtpInputData;
use App\Central\Data\VerifyOtpInputData;
use App\Central\Models\TenantRegistration;
use App\Central\Services\TenantRegistrationService;
use App\Http\Controllers\Controller;
use App\Shared\Traits\ApiResponserTrait;
use DomainException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    use ApiResponserTrait;

    protected ?TenantRegistrationService $tenantRegistrationService = null;

    protected function tenantRegistrationService(): TenantRegistrationService
    {
        return $this->tenantRegistrationService ??= app(TenantRegistrationService::class);
    }

    public function showRegister(): View
    {
        return view('pages.auth.register');
    }

    public function showLogin(): View
    {
        return view('pages.auth.login');
    }

    public function registerStatus(string $invoiceCode): View
    {
        $registration = TenantRegistration::where('invoice_code', $invoiceCode)->firstOrFail();
        return view('pages.auth.register-status', compact('registration'));
    }

    public function apiRegisterStatus(string $invoiceCode): JsonResponse
    {
        try {
            $data = $this->tenantRegistrationService()->getRegisterStatus($invoiceCode);
            return $this->successResponse(data: $data);
        } catch (DomainException $e) {
            return $this->failResponse(code: $e->getCode() ?: ResponseAlias::HTTP_NOT_FOUND, message: $e->getMessage());
        }
    }

    public function centralLogin(CentralLoginInputData $input): JsonResponse
    {
        try {
            $data = $this->tenantRegistrationService()->processCentralLogin(trim($input->login_input));
            return $this->successResponse(data: $data);
        } catch (DomainException $e) {
            return $this->failResponse(code: $e->getCode() ?: ResponseAlias::HTTP_BAD_REQUEST, message: $e->getMessage());
        }
    }

    public function requestOtp(RequestOtpInputData $input): JsonResponse
    {
        try {
            $this->tenantRegistrationService()->requestOtp($input->email);
            return $this->successResponse(message: 'OTP berhasil dikirim ke email Anda.');
        } catch (DomainException $e) {
            return $this->failResponse(code: $e->getCode() ?: ResponseAlias::HTTP_BAD_REQUEST, message: $e->getMessage());
        } catch (Exception $e) {
            Log::error("Gagal mengirim email OTP: " . $e->getMessage());
            return $this->errorResponse(message: 'Gagal mengirim email OTP. Silakan coba lagi.');
        }
    }

    public function verifyOtp(VerifyOtpInputData $input): JsonResponse
    {
        try {
            $this->tenantRegistrationService()->verifyOtp($input->email, $input->otp);
            return $this->successResponse(message: 'Email berhasil diverifikasi!');
        } catch (DomainException $e) {
            return $this->failResponse(code: $e->getCode() ?: ResponseAlias::HTTP_BAD_REQUEST, message: $e->getMessage());
        }
    }

    public function registerTenant(RegisterTenantInputData $input, Request $request): JsonResponse
    {
        // Validate Email Verification
        if (!$this->tenantRegistrationService()->isEmailVerified($input->email)) return $this->failResponse(
            code: ResponseAlias::HTTP_BAD_REQUEST,
            message: 'Email belum diverifikasi. Silakan verifikasi email terlebih dahulu.'
        );

        try {
            $hasCookie = $request->hasCookie('pakaiapp_free_trial_claimed');
            $result = $this->tenantRegistrationService()->initiateRegistration($input->toArray(), $request->ip(), $hasCookie);

            $response = $this->successResponse(
                data: $result,
                message: $result->message
            );

            if ($result->type === 'free') {
                $response->withCookie(cookie()->forever('pakaiapp_free_trial_claimed', '1'));
            }

            return $response;

        } catch (DomainException $e) {
            return $this->failResponse(code: ResponseAlias::HTTP_BAD_REQUEST, message: $e->getMessage());
        } catch (Exception $e) {
            Log::error("Gagal registrasi tenant: " . $e->getMessage());
            return $this->errorResponse(message: 'Terjadi kesalahan sistem.');
        }
    }
}
