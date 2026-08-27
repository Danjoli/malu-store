<?php

namespace App\Services\Public\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /** @param array{name: string, email: string, phone?: string|null} $data */
    public function updateUser(User $user, array $data): void
    {
        $user->update($data);
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
