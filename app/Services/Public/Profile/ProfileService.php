<?php

namespace App\Services\Public\Profile;

use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function updateUser($user, array $data)
    {
        $user->update($data);
    }

    public function updatePassword($user, string $newPassword)
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
