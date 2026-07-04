<?php

namespace App\Services\Admins\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function create(array $data): Admin
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = isset($data['is_active']);

        return Admin::create($data);
    }

    public function update(Admin $admin, array $data): Admin
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = isset($data['is_active']);

        $admin->update($data);

        return $admin;
    }

    public function delete(Admin $admin): void
    {
        $admin->delete();
    }
}
