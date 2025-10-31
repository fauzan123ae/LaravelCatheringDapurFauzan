<?php

namespace App\Http\Controllers; 

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CipherHelper;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $method = $request->input('method', 'aes'); 


        $isValid = false;

        switch ($method) {
            case 'caesar':
                // password di DB disimpan hasil Caesar
                $decrypted = CipherHelper::caesarDecrypt($user->password, 5);
                $isValid = ($decrypted === $credentials['password']);
                break;

            case 'vigenere':
                // password di DB disimpan hasil Vigenere
                $decrypted = CipherHelper::vigenereDecrypt($user->password, "ENKRIPSI");
                $isValid = ($decrypted === strtoupper($credentials['password']));
                break;

            case 'aes':
            default:
                // password di DB disimpan hasil AES
                $decrypted = CipherHelper::aesDecrypt($user->password);
                $isValid = ($decrypted === $credentials['password']);
                break;
        }

        if (!$isValid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Jika lolos verifikasi → buat JWT Token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'method' => $method, // info algoritma yang dipakai
        ]);
    }
}
