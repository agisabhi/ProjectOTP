<?php

namespace App\Http\Controllers;

use App\Mail\MailOtp;
use App\Models\Otp_code;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login()
    {
        $data = [
            'title' => 'Login Page',
        ];

        return view('auth.login', $data);
    }

    public function login_otp()
    {
        $data = [
            'title' => 'Generate OTP',

        ];
        return view('auth.login_otp', $data);
    }

    public function login_otp_action(Request $request)
    {
        $user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();

        if ($user) {
            $otp_code = Otp_code::where('user_id', $user->id)->first();
            // Generate OTP
            $otp = rand('123456', '999999');

            // Create Otp
            $data = [
                'user_id' => $user->id,
                'otp' => $otp,
                'expire_at' => now()->addMinutes(10),
            ];

            if ($otp_code) {
                // Jika Pernah Generate OTP
                Otp_code::where('user_id', $user->id)
                    ->update([
                        'otp' => $otp,
                        'expire_at' => now()->addMinutes(10)
                    ]);
            } else {
                // Jika Belum pernah maka tinggal create OTP
                Otp_code::create($data);
            }

            $maildata = [
                'subject' => 'Kode OTP for My App',
                'otp' => $otp
            ];

            Mail::to($request->email)->send(new MailOtp($maildata));
            return redirect('otp/' . $user->id)->with('success', 'Kode OTP sudah dikirim di email, silahkan periksa Inbox');
        } else {
            return redirect()->back()->with('error', 'Email tidak ditemukan atau Email Belum diverifikasi');
        }
    }

    public function  otp($user_id)
    {
        $data = [
            'title' => 'Input OTP',
            'user_id' => $user_id,
        ];
        return view('auth.otp', $data);
    }

    public function otp_action(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'max:6,min:6',
        ]);
        // Validasi apakah user telah generate otp
        $code = Otp_code::where('user_id', $request->user_id)->where('otp', $request->otp)->first();
        $user = User::where('id', $request->user_id)->first();

        if (!$code) {
            return redirect('login_otp')->with('error', 'Anda belum melakukan generate OTP');
        } elseif (!$code && now()->gt($code->expire_at)) {
            return redirect('otp/', $request->user_id)->back()->with('error', 'Kode OTP telah expire, silahkan Generate ulang');
        } else {
            // Matikan expire_at 
            Otp_code::where('user_id', $code->user_id)
                ->update([
                    'expire_at' => now(),
                ]);

            Auth::login($user);

            return redirect('/admin')->with('success', 'Berhasil Login');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil Logout');
    }
}
