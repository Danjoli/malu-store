<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Address;
use App\Models\Order;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->get();

        return view('public.profile.edit', compact('user','addresses'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email'],
            'phone' => ['nullable','string','max:20']
        ]);

        $request->user()->update($data);

        return back()->with('success','Conta atualizada com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        // Validação básica
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Informe sua senha atual.',
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A nova senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ]);

        $user = $request->user();

        try {
            // Verifica se a senha atual confere
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
            }

            // Evita que a nova senha seja igual à antiga
            if (Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'A nova senha não pode ser igual à senha atual.']);
            }

            // Atualiza a senha
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Senha atualizada com sucesso.');

        } catch (\Exception $e) {
            // Captura qualquer erro inesperado
            return back()->withErrors(['error' => 'Ocorreu um erro ao atualizar a senha. Tente novamente.']);
        }
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'label'=>'required',
            'recipient_name'=>'required',
            'phone'=>'required',
            'street'=>'required',
            'number'=>'required',
            'neighborhood'=>'required',
            'city'=>'required',
            'state'=>'required',
            'cep'=>'required'
        ]);

        $request->user()->addresses()->create($data);

        return back()->with('success','Endereço adicionado.');
    }

    public function deleteAddress($id)
    {
        $address = Address::where('user_id',auth()->id())
            ->findOrFail($id);

        $address->delete();

        return back()->with('success','Endereço removido.');
    }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('public.profile.orders', compact('orders'));
    }


    public function orderShow($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with([
                'items',                   // Carrega os itens do pedido
                'items.variant.product',   // Carrega a variante e o produto associado
                'shipment',                // Carrega o status de envio
                'address'                  // Carrega o endereço do pedido
            ])
            ->findOrFail($id);

        return view('public.profile.order-show', compact('order'));
    }
}
