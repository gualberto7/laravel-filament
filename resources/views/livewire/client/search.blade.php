<div>
    {{ $this->form }}

    <div class="w-full mt-6 h-64">
        <div class="w-full">
            @if ($this->subscription)
                <div class="w-full">
                    {{ $this->infolist }}
                </div>
            @else
                <div class="flex flex-col items-center h-full justify-center">
                    <h1 class="text-lg font-bold text-gray-500 dark:text-gray-400">
                        {{ $this->client ? 'Este cliente no tiene una suscripción' : 'Seleccione un cliente' }}
                    </h1>
                </div>
            @endif
        </div>
    </div>
</div>
