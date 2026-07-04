<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Address\AddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(AddressRequest  $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $address = new Address();
        $address->fill($data);
        $address->user_id = $user->id;

        if ($request->has('is_default')) {
            Address::where('user_id', $user->id)
                ->update(['is_default' => false]);

            $address->is_default = true;
        } else {
            $address->is_default = false;
        }

        $address->save();

        return redirect()->back()->with('success', 'Endereço salvo com sucesso!');
    }

    public function update(AddressRequest $request, $id)
    {
        $user = Auth::user();
        $data = $request->validated();

        $address = Address::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $address->update($data);

        if ($request->has('is_default')) {
            Address::where('user_id', $user->id)
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);
        }

        return back()->with('success', 'Endereço atualizado com sucesso!');
    }
}
