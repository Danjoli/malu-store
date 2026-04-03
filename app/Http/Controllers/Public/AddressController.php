<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'label' => 'nullable|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'cep' => 'required|string|max:20',
            'cpf' => 'required|string|max:14',
        ], [], [
            'label' => 'Etiqueta',
            'recipient_name' => 'Nome do destinatário',
            'phone' => 'Telefone',
            'street' => 'Rua',
            'number' => 'Número',
            'complement' => 'Complemento',
            'neighborhood' => 'Bairro',
            'city' => 'Cidade',
            'state' => 'Estado',
            'cep' => 'CEP',
            'cpf' => 'CPF',
        ]);

        $address = new Address();
        $address->user_id = $user->id;
        $address->label = $request->label;
        $address->recipient_name = $request->recipient_name;
        $address->phone = $request->phone;
        $address->street = $request->street;
        $address->number = $request->number;
        $address->complement = $request->complement;
        $address->neighborhood = $request->neighborhood;
        $address->city = $request->city;
        $address->state = strtoupper($request->state);
        $address->cep = $request->cep;
        $address->cpf = $request->cpf;

        if ($request->has('is_default')) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
            $address->is_default = true;
        } else {
            $address->is_default = false;
        }

        $address->save();

        return redirect()->back()->with('success', 'Endereço salvo com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'label' => 'nullable|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'cep' => 'required|string|max:20',
            'cpf' => 'required|string|max:14',
        ], [], [
            'label' => 'Etiqueta',
            'recipient_name' => 'Nome do destinatário',
            'phone' => 'Telefone',
            'street' => 'Rua',
            'number' => 'Número',
            'complement' => 'Complemento',
            'neighborhood' => 'Bairro',
            'city' => 'Cidade',
            'state' => 'Estado',
            'cep' => 'CEP',
            'cpf' => 'CPF',
        ]);

        $address = Address::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $address->update([
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'street' => $request->street,
            'number' => $request->number,
            'complement' => $request->complement,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => strtoupper($request->state),
            'cep' => $request->cep,
            'cpf' => $request->cpf,
            'is_default' => $request->has('is_default'),
        ]);

        return back()->with('success', 'Endereço atualizado com sucesso!');
    }
}