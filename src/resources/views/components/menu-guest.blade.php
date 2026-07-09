<div>
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <x-application-mark class="block h-9 w-auto" />
                    </div>

                    <!-- Links de navegación -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Inicio
                        </a>
                        <a href="#"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Productos
                        </a>
                        <a href="#"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Contacto
                        </a>
                    </div>
                </div>

                <!-- Login / Registro -->
                <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="text-sm text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-md">
                            Registrarse
                        </a>
                    @endif
                </div>

                <!-- Botón hamburguesa (mobile) -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menú mobile -->
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block ps-3 pe-4 py-2 text-sm font-medium text-gray-600">Inicio</a>
                <a href="#" class="block ps-3 pe-4 py-2 text-sm font-medium text-gray-600">Productos</a>
                <a href="#" class="block ps-3 pe-4 py-2 text-sm font-medium text-gray-600">Contacto</a>
            </div>
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="space-y-1">
                    <a href="{{ route('login') }}"
                        class="block ps-3 pe-4 py-2 text-sm font-medium text-gray-600">Iniciar sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="block ps-3 pe-4 py-2 text-sm font-medium text-gray-600">Registrarse</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
</div>
