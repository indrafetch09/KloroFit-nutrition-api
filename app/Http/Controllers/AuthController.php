<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'device_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate token
            $token = $user->createToken(
                $request->device_name,
                ['*'],
                now()->addDays(30)
            );

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at,
                    ],
                    'token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_in' => now()->addDays(30)->timestamp
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user and generate token
     */
    public function login(Request $request)
    {

        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'device_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek kredensial
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            // Hapus token lama untuk device yang sama (optional)
            $user->tokens()->where('name', $request->device_name)->delete();

            // Generate token baru
            $token = $user->createToken(
                $request->device_name,
                ['*'],
                now()->addDays(30)
            );

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at,
                    ],
                    'token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_in' => now()->addDays(30)->timestamp
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //     /**
    //      * Get authenticated user info (ADMIN ONLY)
    //      */
    //     public function user(Request $request)
    //     {
    //         try {
    //             $user = $request->user();

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'User data retrieved successfully',
    //                 'data' => [
    //                     'user' => [
    //                         'id' => $user->id,
    //                         'name' => $user->name,
    //                         'email' => $user->email,
    //                         'email_verified_at' => $user->email_verified_at,
    //                         'created_at' => $user->created_at,
    //                         'updated_at' => $user->updated_at,
    //                     ]
    //                 ]
    //             ], 200);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Something went wrong',
    //                 'error' => $e->getMessage()
    //             ], 500);
    //         }
    //     }

    // under construction
    // public function socailLogin(Request $request)
    // {
    //     // Implement social login logic here
    //     // This is a placeholder method for future social login integration
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Social login not implemented yet'
    //     ], 501);
    // }

    // public funciton verifyEmail(Request $request, $id, $hash)
    // {
    //     // Implement email verification logic here
    //     // This is a placeholder method for future email verification integration
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Email verification not implemented yet'
    //     ], 501);
    // }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $token = $user->currentAccessToken();

            if ($token && $token instanceof PersonalAccessToken) {
                $token->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        try {
            // Revoke all tokens
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out from all devices successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();
            $currentToken = $request->user()->currentAccessToken();

            if ($currentToken && $currentToken instanceof PersonalAccessToken) {
                $currentToken->delete();
            }

            // Create new token
            $newToken = $user->createToken(
                $currentToken->name,
                ['*'],
                now()->addDays(30)
            );

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $newToken->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_at' => $newToken->accessToken->expires_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
