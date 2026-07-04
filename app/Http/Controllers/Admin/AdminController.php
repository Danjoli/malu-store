<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Admin\StoreAdminRequest;
use App\Http\Requests\Admins\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\Admins\Admin\AdminService;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

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
            'roles' => Admin::ROLES
        ]);
    }

    public function store(StoreAdminRequest $request)
    {
        $this->adminService->create($request->validated());

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Administrador criado com sucesso!');
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', [
            'admin' => $admin,
            'roles' => Admin::ROLES
        ]);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $this->adminService->update(
            $admin,
            $request->validated()
        );

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Administrador atualizado!');
    }

    public function destroy(Admin $admin)
    {
        $this->adminService->delete($admin);

        return back()->with('success', 'Administrador removido!');
    }
}
