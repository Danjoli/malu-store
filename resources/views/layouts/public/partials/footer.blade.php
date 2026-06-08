<footer class="bg-white border-t mt-16">

    <div class="container mx-auto px-6 py-8 grid md:grid-cols-3 gap-8 text-sm text-gray-600">

        <div>
            <h3 class="font-semibold text-gray-800 mb-2">Malu Store</h3>
            <p>Sua loja online de confiança, com qualidade e estilo.</p>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 mb-2">Links Úteis</h3>

            <ul class="space-y-1">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600">Loja</a></li>
                <li><a href="{{ route('policy') }}" class="hover:text-blue-600">Política de Troca</a></li>
                <li><a href="{{ route('terms') }}" class="hover:text-blue-600">Termos de Uso</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-blue-600">Política de Privacidade</a></li>
            </ul>

        </div>

        <div>
            <h3 class="font-semibold text-gray-800 mb-2">Contato</h3>

            <p>Email: elinealvesdelima5@gmail.com</p>

            <p>
                WhatsApp:
                <a href="https://wa.me/5511931494708?text=Olá,%20gostaria%20de%20mais%20informações"
                   class="text-blue-600 hover:underline"
                   target="_blank">
                    (11) 93149-4708
                </a>
            </p>

        </div>

    </div>

    <div class="text-center text-xs text-gray-400 py-4 border-t">
        © {{ date('Y') }} Malu Store — Todos os direitos reservados
    </div>

</footer>
