<?php

namespace App\Http\Requests\Public\Checkout;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        return [
            'address_id' => 'nullable|integer|exists:addresses,id',
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',

            // Complemento é opcional
            'complement' => 'nullable|string|max:255',

            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'cep' => ['required', 'string', 'max:20', 'regex:/^\D*\d(?:\D*\d){7}\D*$/'],
            'cpf' => 'required|string|max:14',

            'shipping_cost' => 'required|numeric|gt:0',
            'carrier' => 'required|string|max:100',
            'service' => 'required|string',
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'card_number' => 'required_if:payment_method,card|nullable|string',
            'holder_name' => 'required_if:payment_method,card|nullable|string',
            'expiration_month' => 'required_if:payment_method,card|nullable|string',
            'expiration_year' => 'required_if:payment_method,card|nullable|string',
            'ccv' => 'required_if:payment_method,card|nullable|string',
        ];
    }

    /**
     * Mensagens personalizadas.
     */
    public function messages(): array
    {
        return [
            'label.string' => 'O nome do endereço deve ser um texto.',
            'label.max' => 'O nome do endereço não pode ter mais de 50 caracteres.',

            'recipient_name.required' => 'Informe o nome do destinatário.',
            'phone.required' => 'Informe o telefone.',
            'street.required' => 'Informe a rua.',
            'number.required' => 'Informe o número.',

            'neighborhood.required' => 'Informe o bairro.',
            'city.required' => 'Informe a cidade.',
            'state.required' => 'Informe o estado.',
            'state.size' => 'O estado deve conter exatamente 2 caracteres.',

            'cep.required' => 'Informe o CEP.',
            'cpf.required' => 'Informe o CPF.',

            'shipping_cost.required' => 'Selecione um frete antes de finalizar o pedido.',
            'shipping_cost.numeric' => 'O valor do frete é inválido.',
            'shipping_cost.gt' => 'Selecione uma opção de entrega válida.',

            'carrier.required' => 'Selecione uma transportadora.',
            'service.required' => 'Selecione um serviço de entrega.',
            'payment_method.required' => 'Escolha uma forma de pagamento.',
            'card_number.required_if' => 'Informe o número do cartão.',
            'holder_name.required_if' => 'Informe o nome no cartão.',
            'expiration_month.required_if' => 'Informe o mês de validade.',
            'expiration_year.required_if' => 'Informe o ano de validade.',
            'ccv.required_if' => 'Informe o código de segurança.',
        ];
    }

    /**
     * Tradução dos nomes dos campos.
     */
    public function attributes(): array
    {
        return [
            'label' => 'nome do endereço',
            'recipient_name' => 'nome do destinatário',
            'phone' => 'telefone',
            'street' => 'rua',
            'number' => 'número',
            'complement' => 'complemento',
            'neighborhood' => 'bairro',
            'city' => 'cidade',
            'state' => 'estado',
            'cep' => 'CEP',
            'cpf' => 'CPF',

            'shipping_cost' => 'valor do frete',
            'carrier' => 'transportadora',
            'service' => 'serviço de entrega',
        ];
    }
}
