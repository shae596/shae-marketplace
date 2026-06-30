<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OtpService
{
    public function generateAndSend(User $user): OtpCode
    {
        OtpCode::where('user_id', $user->id)->update(['used' => true]);

        $otp = OtpCode::create([
            'user_id' => $user->id,
            'code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes((int) config('shae.otp_expiration_minutes', 10)),
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->code));
        } catch (Throwable $e) {
            Log::warning('Envoi OTP impossible, code enregistré en base.', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        if (app()->isLocal()) {
            Log::info('Code OTP SHAE (local).', [
                'user_id' => $user->id,
                'email' => $user->email,
                'code' => $otp->code,
                'expires_at' => $otp->expires_at->toDateTimeString(),
            ]);
        }

        return $otp;
    }

    public function verify(int $userId, string $code): bool
    {
        $otp = OtpCode::where('user_id', $userId)
            ->where('code', $code)
            ->where('used', false)
            ->latest()
            ->first();

        if (! $otp || ! $otp->isValid()) {
            return false;
        }

        $otp->update(['used' => true]);

        return true;
    }
}
