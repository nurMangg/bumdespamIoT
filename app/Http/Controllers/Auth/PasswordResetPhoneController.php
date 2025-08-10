<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Silvanix\Wablas\Message;

class PasswordResetPhoneController extends Controller
{
    public function sendOTP(Request $request)
    {
        $request->validate([
            'phone' => ['required', function ($attribute, $value, $fail) {
                if (!Pelanggan::where('pelangganPhone', $value)->exists()) {
                    $fail('Nomor telepon tidak ditemukan.');
                }
            }],
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5); // OTP berlaku selama 5 menit

        // Simpan OTP ke database
        DB::table('otp')->updateOrInsert(
            ['phone' => $request->phone],
            ['otp' => $otp, 'expires_at' => $expiresAt, 'updated_at' => now()]
        );

        // //Kirim OTP
        // $sent = new Message();
        // $message = "*Permintaan OTP Reset Password!*\n\n"
        //           ."Kode OTP Anda: $otp\n\n"
        //           ."Kode ini akan kedaluwarsa dalam 5 menit.\n\n"
        //           ."—\n"
        //           ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";

        // // Mengirim pesan ke WhatsApp melalui Wablas
        // $send_text = $sent->single_text($request->phone, $message);
        
        $this->send_otp_server($request->phone, $otp);
        
        session(['reset_phone' => $request->phone]);
        
        logger("OTP untuk {$request->phone}: $otp");

        return redirect()->route('password.request.phone')->with('status', 'Kode OTP telah dikirim ke nomor telepon Anda.');
    }

    // Reset password menggunakan OTP
    public function resetWithPhone(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'phone' => ['required', function ($attribute, $value, $fail) {
                if (!Pelanggan::where('pelangganPhone', $value)->exists()) {
                    $fail('Nomor telepon tidak ditemukan.');
                }
            }],
            'otp' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        // Cek OTP dan waktu kedaluwarsa
        $otpData = DB::table('otp')
            ->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpData) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
        }

        if (now()->greaterThan($otpData->expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa.']);
        }

        $pelanggan = Pelanggan::where('pelangganPhone', $request->phone)->first();

        // Reset password
        $user = User::find($pelanggan->pelangganUserId);
        $user->update(['password' => Hash::make($request->password)]);

        // Hapus OTP setelah digunakan
        DB::table('otp')->where('phone', $request->phone)->delete();

        return redirect()->route('login')->with('status', 'Password berhasil diubah.');
    }

    public function showResetForm()
    {
        return view('auth.passwords.reset');
    }
}
