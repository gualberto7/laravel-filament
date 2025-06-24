<div>
    {{ $this->form }}

    <div class="mt-6">
        <x-filament::section>
            {{-- Search Result --}}
            <div class="flex justify-center h-32 items-center">
                @if ($this->client)
                    <div class="flex flex-col items-center">
                        <h1 class="text-2xl font-bold">{{ $this->client->name }}</h1>
                        @if ($this->subscription)
                            <p class="text-sm text-gray-500">{{ $this->subscription->status }}</p>
                        @else
                            <p class="text-sm text-gray-500">No tiene una suscripción</p>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col items-center">
                        <h1 class="text-2xl font-bold text-gray-500 dark:text-gray-400">Seleccione un cliente</h1>
                    </div>
                @endif
            </div>
        </x-filament::section>
    </div>
</div>
