<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private array $roles = [
        'superadmin',
        'admin',
        'suporte',
    ];

    public function index()
    {
        $admins = Admin::all();
        return view('admin.admins.index', compact('admins'));
    }

    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    public function create()
    {
        return view('admin.admins.create', [
            'roles' => $this->roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrador criado com sucesso!');
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', [
            'admin' => $admin,
            'roles' => $this->roles
        ]);
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'role' => 'required'
        ]);

        $data = $request->only('name', 'email', 'role');
        $data['is_active'] = $request->has('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrador atualizado!');
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();

        return back()->with('success', 'Administrador removido!');
    }
}
