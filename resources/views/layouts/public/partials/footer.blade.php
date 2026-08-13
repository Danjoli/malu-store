<footer class="mt-20 border-t border-[#eee6e4] bg-[#fffaf9]">

    <div class="store-container grid gap-10 py-12 text-sm text-stone-600 md:grid-cols-4">

        <div>
            <h3 class="store-title mb-3 text-2xl font-semibold tracking-[-.06em] text-stone-900">MALU STORE</h3>
            <p class="leading-6">Moda para acompanhar os seus melhores momentos.</p>
        </div>

        <div>
            <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-stone-900">Institucional</h3>

            <ul class="space-y-1">
                <li><a href="{{ route('policy') }}" class="hover:text-[#bd5564]">Política de Troca</a></li>
                <li><a href="{{ route('terms') }}" class="hover:text-[#bd5564]">Termos de Uso</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-[#bd5564]">Privacidade</a></li>
            </ul>

        </div>

        <div>
            <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-stone-900">Atendimento</h3>

            <p>Email: elinealvesdelima5@gmail.com</p>

            <p>
                WhatsApp:
                <a href="https://wa.me/5511931494708?text=Olá,%20gostaria%20de%20mais%20informações"
                   class="text-[#bd5564] hover:underline"
                   target="_blank">
                    (11) 93149-4708
                </a>
            </p>

        </div>
        <div><h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-stone-900">Compra segura</h3><p class="leading-6">Pagamento protegido e envio para todo o Brasil.</p></div>

    </div>

    <div class="border-t border-[#eee6e4] py-4 text-center text-xs text-stone-400">
        © {{ date('Y') }} Malu Store — Todos os direitos reservados
    </div>

</footer>
