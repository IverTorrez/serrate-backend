<?php

namespace App\Services;

use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\JsonResponse;

class VerificationService
{
    public function sendVerificationCode($email): JsonResponse
    {
        $verificationCode = strtoupper(Str::random(6));
        $expiresAt = Carbon::now()->addMinutes(15);

        $verification = VerificationCode::create([
            'email' => $email,
            'verification_code' => $verificationCode,
            'expires_at' => $expiresAt,
            'used' => false,
        ]);

        // Enviar el código por correo utilizando Mailtrap
        Mail::to($email)->send(new VerificationCodeMail($verificationCode));

        return ResponseService::success(
            message: 'Correo de verificación enviado',
            //data: $verification
        );
    }

    public function verifyCode($email, $verificationCode): JsonResponse
    {
        $verification = VerificationCode::where('email', $email)
            ->where('verification_code', $verificationCode)
            ->first();

        if (!$verification) {
            return ResponseService::error(
                'Código de verificación inválido.',
                400
            );
        }

        if ($verification->used) {
            return ResponseService::error(
                'El código de verificación ya ha sido utilizado.',
                400
            );
        }

        if (now()->greaterThan($verification->expires_at)) {
            return ResponseService::error(
                'El código ha expirado.',
                400
            );
        }
        $verification->used = true;
        $verification->save();

        return ResponseService::success(
            message: 'Correo verificado con éxito.',

        );
    }
}
