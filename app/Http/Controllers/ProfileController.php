<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    private const LANGUAGES = [
        'en',
        'ms',
    ];

    private const TIMEZONES = [
        'Asia/Brunei',
    ];

    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile ?: new Profile();

        return view('profile', compact('user', 'profile'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?: new Profile();

        return view('profile-edit', [
            'user' => $user,
            'profile' => $profile,
            'languages' => self::LANGUAGES,
            'timezones' => self::TIMEZONES,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'timezone' => ['nullable', Rule::in(self::TIMEZONES)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:50'],
            'preferred_language' => ['nullable', Rule::in(self::LANGUAGES)],
            'birth_date' => ['nullable', 'date'],
        ]);

        // Update users table
        $user->name = $request->name;
        $user->email = $request->email;
        $user->timezone = $request->timezone ?: 'Asia/Brunei';

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Upload photo if provided
        $photoPath = $user->profile?->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile-photos', 'public');
        }

        // Update profiles table
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'photo' => $photoPath,
                'phone' => $request->phone,
                'preferred_language' => $request->preferred_language,
                'birth_date' => $request->birth_date,
            ]
        );

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}