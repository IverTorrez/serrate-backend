<?php

namespace App\Http\Controllers;

use App\Constants\ErrorMessages;
use App\Http\Requests\SendVerificationCodeRequest;
use App\Services\ResponseService;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    protected $verificationCodeService;

    public function __construct(VerificationService $verificationCodeService)
    {
        $this->verificationCodeService = $verificationCodeService;
    }

    public function sendVerificationCode(SendVerificationCodeRequest $request): JsonResponse
    {

        try {
            return $this->verificationCodeService->sendVerificationCode($request->email);
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_ENVIAR_EMAIL, 500);
        }
    }

    // Verificar el código de verificación
    public function verifyCode(Request $request)
    {
        // Validación de la entrada
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
        ]);

        try {
            $response = $this->verificationCodeService->verifyCode($request->email, $request->verification_code);
            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
