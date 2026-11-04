<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => StoreSetting::current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:80'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'free_shipping_threshold' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'max_installments' => ['required', 'integer', 'min:1', 'max:12'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:9999'],
            'announcement' => ['nullable', 'string', 'max:160'],
            'storefront_enabled' => ['nullable', 'boolean'],
        ]);

        $data['storefront_enabled'] = $request->boolean('storefront_enabled');
        StoreSetting::current()->update($data);

        return back()->with('success', 'Configurações da loja atualizadas.');
    }
}
