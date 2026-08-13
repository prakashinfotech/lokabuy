<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(private readonly AdminService $adminService) {}

    public function index(Request $request): View
    {
        $users = User::when($request->input('q'), function ($query, $q) {
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function deactivate(User $user): RedirectResponse
    {
        $this->adminService->deactivateUser($user);

        return back()->with('success', "User \"{$user->name}\" has been deactivated.");
    }

    public function activate(User $user): RedirectResponse
    {
        $this->adminService->activateUser($user);

        return back()->with('success', "User \"{$user->name}\" has been activated.");
    }
}
