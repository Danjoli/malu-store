<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Address;
use App\Models\Order;

use App\Services\Profile\ProfileService;

use App\Http\Requests\Public\Profile\StoreAddressRequest;
use App\Http\Requests\Public\Profile\UpdatePasswordRequest;
use App\Http\Requests\Public\Profile\UpdateProfileRequest;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */
    public function edit()
    {
        $user = Auth::user();

        return view('public.profile.edit', [
            'user' => $user,
            'addresses' => $user->addresses
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Atualizar dados do usuário
    |--------------------------------------------------------------------------
    */
    public function update(UpdateProfileRequest $request)
    {
        $this->profileService->updateUser(
            $request->user(),
            $request->validated()
        );

        return back()->with('success', 'Conta atualizada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | Atualizar senha
    |--------------------------------------------------------------------------
    */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Senha atual incorreta.'
            ]);
        }

        $this->profileService->updatePassword(
            $user,
            $request->password
        );

        return back()->with('success', 'Senha atualizada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | Endereços
    |--------------------------------------------------------------------------
    */
    public function storeAddress(StoreAddressRequest $request)
    {
        $request->user()
            ->addresses()
            ->create($request->validated());

        return back()->with('success', 'Endereço adicionado.');
    }

    public function deleteAddress($id)
    {
        Address::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail()
            ->delete();

        return back()->with('success', 'Endereço removido.');
    }

    /*
    |--------------------------------------------------------------------------
    | Pedidos
    |--------------------------------------------------------------------------
    */
    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('public.profile.orders', compact('orders'));
    }

    public function orderShow($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->with([
                'items',
                'items.variant.product',
                'shipment',
                'address'
            ])
            ->findOrFail($id);

        return view('public.profile.order', compact('order'));
    }
}
