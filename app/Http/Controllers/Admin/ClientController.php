<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ClientController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.clients.index', compact('users'));
    }

    public function show(User $client)
    {
        $client->load([
            'addresses',
            'orders.items'
        ]);

        return view('admin.clients.show', [
            'user' => $client
        ]);
    }
}
