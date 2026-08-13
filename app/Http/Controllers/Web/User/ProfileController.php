<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function edit(): View
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return back()->with('success', 'Profile updated successfully.');
    }

    public function uploadAvatar(UpdateAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();
        $avatarUrl = $this->imageService->storeAvatar($user->id, $request->file('avatar'));
        $user->update(['avatar_url' => $avatarUrl]);

        return back()->with('success', 'Avatar updated successfully.');
    }
}
