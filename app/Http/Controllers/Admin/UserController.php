<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetUserPasswordRequest;
use App\Models\User;
use App\Services\UserAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class UserController extends Controller
{
    public function __construct(private readonly UserAdminService $users)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('pages.admin.users.index', [
            'users' => $this->users->getAllUsers(),
        ]);
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorize('toggleStatus', $user);

        try {
            $updated = $this->users->toggleUserStatus($user->id, auth()->user());
        } catch (ValidationException $e) {
            throw $e;
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $label = $updated->is_active ? 'diaktifkan' : 'diblokir';

        return back()->with('success', "Akun {$updated->name} berhasil {$label}.");
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        try {
            $this->users->resetUserPassword($user->id, $request->validated('password'));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Password {$user->name} berhasil direset.");
    }
}
