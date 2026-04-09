<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $profile = $user->profile ?: new Profile();

        return view('profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'github' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $request->phone,
                'bio' => $request->bio,
                'avatar' => $request->avatar,
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