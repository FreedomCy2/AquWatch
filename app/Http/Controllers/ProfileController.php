<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    private const DISTRICTS = [
        'Brunei-Muara',
        'Belait',
        'Tutong',
        'Temburong',
    ];

    private const ROLES = [
        'owner',
        'responder',
        'viewer',
    ];

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
            'districts' => self::DISTRICTS,
            'roles' => self::ROLES,
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
            'bio' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'role' => ['nullable', Rule::in(self::ROLES)],
            'preferred_language' => ['nullable', Rule::in(self::LANGUAGES)],
            'district' => ['nullable', Rule::in(self::DISTRICTS)],
            'mukim' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'company' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'github' => ['nullable', 'string', 'max:255'],
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
                'bio' => $request->bio,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country ?: 'Brunei Darussalam',
                'birth_date' => $request->birth_date,
                'role' => $request->role,
                'preferred_language' => $request->preferred_language,
                'district' => $request->district,
                'mukim' => $request->mukim,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'company' => $request->company,
                'job_title' => $request->job_title,
                'website' => $request->website,
                'twitter' => $request->twitter,
                'linkedin' => $request->linkedin,
                'github' => $request->github,
            ]
        );

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}