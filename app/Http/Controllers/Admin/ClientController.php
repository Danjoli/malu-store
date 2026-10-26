<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->withCount('orders')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = '%'.trim($request->string('q')->toString()).'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search));
            })
            ->when($request->string('orders')->toString() === 'with', fn ($query) => $query->has('orders'))
            ->when($request->string('orders')->toString() === 'without', fn ($query) => $query->doesntHave('orders'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.clients.index', compact('users'));
    }

    public function show(User $client)
    {
        $client->load([
            'addresses',
            'orders.items',
        ]);

        return view('admin.clients.show.index', [
            'user' => $client,
        ]);
    }
}
