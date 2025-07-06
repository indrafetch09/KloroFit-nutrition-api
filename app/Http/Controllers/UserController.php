<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // Profile management
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $filename = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $path = $request->file('profile_picture')->storeAs('profile_pictures', $filename, 'public');
            $validated['profile_picture'] = '/storage/' . $path;
        }

        $user->update($validated);
        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password lama salah'], 403);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password berhasil diubah']);
    }



    // Settings
    public function getSettings(Request $request)
    {
        return response()->json([
            'settings' => $request->user()->settings ?? new \stdClass()
        ]);
    }

    public function updateSettings(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'security.pin_enabled' => 'boolean',
            'security.biometric_verification' => 'boolean',
            'notification.enabled' => 'boolean',
            'privacy.data_usage' => 'string|nullable',
            'language' => 'string|in:id,en',
            'app_version' => 'string|nullable',
        ]);

        // Gabungkan settings lama dengan baru
        $settings = array_merge($user->settings ?? [], $validated);
        $user->settings = $settings;
        $user->save();

        return response()->json([
            'message' => 'Pengaturan berhasil diperbarui',
            'settings' => $user->settings,
        ]);
    }
}
