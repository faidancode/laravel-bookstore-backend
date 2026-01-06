<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $service
    ) {}

    public function register(RegisterRequest $request)
    {
        // Gunakan $request->validated() karena validasi sudah dilakukan di FormRequest
        $result = $this->service->register($request->validated());

        return response()->json([
            'message' => 'User berhasil didaftarkan',
            'data' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        // Langsung ambil data dari request yang sudah tervalidasi
        $result = $this->service->login(
            $request->email,
            $request->password
        );

        return response()->json([
            'message' => 'Login berhasil',
            'data' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $emailSent = $this->service->initiateResetPassword($request->email);

        return response()->json([
            'success' => true,
            'message' => 'If your email is in our system, you will receive a link',
            'emailSent' => $emailSent
        ]);
    }

    public function confirmEmail(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        try {
            $this->service->confirmEmail($request->token);
            $status = true;
            $message = 'Email verified successfully';
        } catch (\Exception $e) {
            $status = false;
            $message = 'Invalid or expired token';
        }

        return response()->json([
            'success' => true,
            'verified' => $status,
            'message' => $message
        ]);
    }

    public function resetPassword(Request $request)
    {
        // Catatan: Sebaiknya buat ResetPasswordRequest agar lebih rapi
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required|string' // Tambahkan token untuk validasi akhir
        ]);

        $this->service->resetPassword($request->email, $request->password);
        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:30',
        ]);

        $this->service->updateProfile($request->user()->id, $data);
        return response()->json(['message' => 'Profil berhasil diperbarui.']);
    }
}
