<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
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

        return view('profile-edit', compact('user', 'profile'));
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
            'timezone' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
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
        $user->timezone = $request->timezone;

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
                'country' => $request->country,
                'birth_date' => $request->birth_date,
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