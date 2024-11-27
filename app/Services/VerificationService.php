<?php

namespace App\Services;

use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str; // Importa la clase Str
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

    public function verifyCode($email, $verificationCode)
    {
        // Buscar el código de verificación para el correo proporcionado
        $verification = VerificationCode::where('email', $email)
            ->where('verification_code', $verificationCode)
            ->first();

        if (!$verification) {
            // Código no encontrado
            return response()->json(['error' => 'Código de verificación inválido.'], 400);
        }

        if ($verification->used) {
            // Código ya ha sido utilizado
            return response()->json(['error' => 'El código de verificación ya ha sido utilizado.'], 400);
        }

        if (now()->greaterThan($verification->expires_at)) {
            // Código expirado
            return response()->json(['error' => 'El código ha expirado.'], 400);
        }

        // El código es válido, lo marcamos como usado
        $verification->used = true;
        $verification->save();

        // Aquí puedes continuar con el registro del usuario, ya que el código fue validado
        return response()->json(['message' => 'Correo verificado con éxito.'], 200);
    }
}
