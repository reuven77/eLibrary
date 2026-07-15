<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class UserAdminService
{
    /**
     * Daftar user berpaginasi (kolom eksplisit).
     */
    public function getAllUsers(int $perPage = 20): LengthAwarePaginator
    {
        return User::query()
            ->select([
                'id',
                'name',
                'email',
                'role',
                'is_active',
                'blocked_reason',
                'created_at',
            ])
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Toggle is_active. Admin tidak boleh menonaktifkan dirinya sendiri.
     *
     * @throws RuntimeException|ValidationException
     */
    public function toggleUserStatus(string $userId, ?User $actor = null): User
    {
        return DB::transaction(function () use ($userId, $actor) {
            /** @var User|null $user */
            $user = User::query()
                ->select(['id', 'name', 'email', 'role', 'is_active', 'blocked_reason'])
                ->whereKey($userId)
                ->lockForUpdate()
                ->first();

            if ($user === null) {
                throw new RuntimeException('Pengguna tidak ditemukan.');
            }

            if ($actor !== null && $actor->id === $user->id) {
                throw ValidationException::withMessages([
                    'user' => 'Anda tidak dapat mengubah status akun sendiri.',
                ]);
            }

            $user->is_active = ! $user->is_active;
            $user->blocked_reason = $user->is_active
                ? null
                : ($user->blocked_reason ?: 'Dinonaktifkan oleh pustakawan.');
            $user->save();

            return $user->refresh();
        });
    }

    /**
     * Reset password user (hash bawaan Laravel).
     *
     * @throws RuntimeException
     */
    public function resetUserPassword(string $userId, string $newPassword): User
    {
        return DB::transaction(function () use ($userId, $newPassword) {
            /** @var User|null $user */
            $user = User::query()
                ->select(['id', 'password'])
                ->whereKey($userId)
                ->lockForUpdate()
                ->first();

            if ($user === null) {
                throw new RuntimeException('Pengguna tidak ditemukan.');
            }

            $user->password = $newPassword;
            $user->save();

            return $user->refresh();
        });
    }
}
