<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
	public function register(RegisterRequest $request): JsonResponse
	{
		$userData = $request->validated();

		$userData['email_verified_at'] = now();
		$user = User::create($userData);

		$response = Http::post('meeting_app/oauth/token', [
			'grant_type' => 'password',
			'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
			'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
			'username' => $userData['email'],
			'password' => $userData['password'],
			'scope' => '',
		]);

		$user['token'] = $response->json();

		return response()->json([
			'success' => true,
			'statusCode' => 201,
			'message' => 'User has been registered successfully.',
			'data' => $user,
		], 201);
	}

	public function login(LoginRequest $request): JsonResponse
	{
		if (!Auth::attempt([
				'email' => $request->email, 
				'password' => $request->password
		])) {
				return response()->json([
						'success' => true,
						'statusCode' => 401,
						'message' => 'Unauthorized user.',
				], 401);
		}

		$user = Auth::user();
		$token = $user->createToken('token')->accessToken;
		$user['token'] = $token;

		return response()->json([
				'success' => true,
				'statusCode' => 200,
				'message' => 'User has been logged successfully.',
				'data' => $user,
		], 200);
	}

	public function refreshToken(RefreshTokenRequest $request): JsonResponse
	{
		$response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
			'grant_type' => 'refresh_token',
			'refresh_token' => $request->refresh_token,
			'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
			'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
			'scope' => '',
		]);

		return response()->json([
			'success' => true,
			'statusCode' => 200,
			'message' => 'Refreshed token.',
			'data' => $response->json(),
		], 200);
	}
	
	public function logout(): JsonResponse
	{
		Auth::user()->tokens()->delete();

		return response()->json([
				'success' => true,
				'statusCode' => 204,
				'message' => 'Logged out successfully.',
		], 204);
	}
}

