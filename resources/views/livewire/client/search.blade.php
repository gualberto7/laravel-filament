<div class="flex flex-col h-full">
    {{ $this->form }}

    <div class="w-full mt-6 flex-1 overflow-auto">
        <div class="w-full">
            @if ($this->subscription)
                <div class="w-full">
                    {{ $this->infolist }}
                </div>
            @else
                <div class="flex flex-col items-center h-full justify-center">
                    @if ($this->client)
                        <h1 class="text-lg font-bold text-gray-500 dark:text-gray-400">
                            <span>Este cliente no tiene una suscripción</span>
                        </h1>
                        <x-filament::button
                            href="{{ route('filament.admin.resources.subscriptions.create', ['client_id' => $this->client->id]) }}"
                            tag="a"
                        >
                            Crear Subscripcion
                        </x-filament::button>
                    @else
                        <h1 class="text-lg font-bold text-gray-500 dark:text-gray-400">
                            <span>Seleccione un cliente</span>
                        </h1>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
