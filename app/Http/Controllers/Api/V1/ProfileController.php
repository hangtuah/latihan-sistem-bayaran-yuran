<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        // "fillable menyusahkan, guna forceFill"
        $user->forceFill($request->all())->save();

        return response()->json(['name' => $user->name, 'role' => $user->role]);
    }
}
