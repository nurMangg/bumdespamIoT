<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Silvanix\Wablas\Message;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:mspelanggan,pelangganPhone',
        ]);

        // Buat OTP dan simpan ke sesi atau database
        $otp = rand(100000, 999999);
        session(['otp' => $otp, 'phone' => $request->phone]);

        // Kirim OTP melalui SMS
        $sent = new Message();
        $message = "*Permintaan OTP Reset Password!*\n\n"
                  ."Kode OTP Anda: $otp\n\n"
                  ."—\n"
                  ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";
    
        // Mengirim pesan ke WhatsApp melalui Wablas
        $send_text = $sent->single_text($request->phone, $message);

        return back()->with('status', 'Kode OTP telah dikirim ke nomor telepon Anda.');
    }
}
