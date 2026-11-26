<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class TokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Emel atau kata laluan salah.',
            ]);
        }

        $abilities = $user->isBursary()
            ? ['invoices:read', 'reports:read']
            : ['invoices:read', 'payments:create'];

        $token = $user->createToken(
            $data['device_name'],
            $abilities,
            now()->addDays(30),
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'abilities' => $abilities,
        ], 201);
    }
}
