<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admins\StoreAdminRequest;
use App\Http\Requests\Admin\Admins\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\Admin\Admins\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    public function index(Request $request)
    {
        $admins = Admin::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = '%'.trim($request->string('q')->toString()).'%';
                $query->where(fn ($query) => $query->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')->toString()))
            ->when($request->string('status')->toString() === 'active', fn ($query) => $query->where('is_active', true))
            ->when($request->string('status')->toString() === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.admins.index', ['admins' => $admins, 'roles' => AdminRole::cases()]);
    }

    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    public function create()
    {
        return view('admin.admins.create', [
            'roles' => AdminRole::cases(),
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
            'roles' => AdminRole::cases(),
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
