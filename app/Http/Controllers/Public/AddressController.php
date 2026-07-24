<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Address\AddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Cria um novo endereço.
     */
    public function store(AddressRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $address = new Address();
        $address->fill($data);
        $address->user_id = $user->id;

        if ($request->boolean('is_default')) {

            // Remove o endereço principal atual.
            Address::where('user_id', $user->id)
                ->update([
                    'is_default' => false,
                ]);

            $address->is_default = true;

        } else {

            $address->is_default = false;
        }

        $address->save();

        return back()->with(
            'success',
            'Endereço salvo com sucesso!'
        );
    }

    /**
     * Atualiza um endereço existente.
     */
    public function update(AddressRequest $request, $id)
    {
        $user = Auth::user();
        $data = $request->validated();

        $address = Address::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $address->update($data);

        if ($request->boolean('is_default')) {

            // Remove o endereço principal dos outros endereços.
            Address::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->update([
                    'is_default' => false,
                ]);

            // Define o endereço atualizado como principal.
            $address->update([
                'is_default' => true,
            ]);

        } else {

            $address->update([
                'is_default' => false,
            ]);
        }

        return back()->with(
            'success',
            'Endereço atualizado com sucesso!'
        );
    }

    /**
     * Define um endereço como principal.
     */
    public function setDefault($id)
    {
        $user = Auth::user();

        $address = Address::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Remove o endereço principal atual.
        Address::where('user_id', $user->id)
            ->update([
                'is_default' => false,
            ]);

        // Define o endereço selecionado como principal.
        $address->update([
            'is_default' => true,
        ]);

        return back()->with(
            'success',
            'Endereço principal atualizado com sucesso!'
        );
    }

    /**
     * Exclui um endereço.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $address = Address::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $address->delete();

        return back()->with(
            'success',
            'Endereço removido com sucesso!'
        );
    }
}
