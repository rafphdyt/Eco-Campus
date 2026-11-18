<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class ApiController extends Controller
{
    protected function success(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        $body = ['status' => 'success', 'message' => $message];
        if ($data !== null) {
            $body['data'] = $data;
        }
        return response()->json($body, $code);
    }

    protected function error(string $message, int $code, array $extra = []): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message] + $extra, $code);
    }

    // Validasi; kalau gagal langsung balas 422 dengan format status/message
    protected function check(Request $request, array $rules, array $messages = []): array
    {
        $v = Validator::make($request->all(), $rules, $messages);

        if ($v->fails()) {
            throw new HttpResponseException(
                $this->error($v->errors()->first(), 422, ['errors' => $v->errors()])
            );
        }

        return $v->validated();
    }

    protected function userData(User $user): array
    {
        $user->loadMissing('department.institution');

        return [
            'user_id' => $user->user_id,
            'name' => $user->name,
            'npm' => $user->npm,
            'email' => $user->email,
            'role' => $user->role,
            'institution' => $user->department->institution->name,
            'faculty' => $user->department->faculty,
            'department' => $user->department->name,
            'total_points' => $user->total_points,
            'photo_url' => $user->photo_url,
        ];
    }
}