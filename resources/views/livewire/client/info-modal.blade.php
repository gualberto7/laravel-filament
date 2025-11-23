<x-filament::modal id="search-client" width="md">
    @if ($client)
        <x-slot name="heading">
            {{ $client->name }}
        </x-slot>

        <x-slot name="description">
            Celular: {{ $client->phone }}
        </x-slot>

        @if ($subscription)
            <div class="grid grid-cols-2 gap-5 mb-4">
                <div class="flex flex-col">
                    <span class="font-bold">Estado</span>
                    <div>
                        <x-filament::badge color="{{ $subscription->status == 'active' ? 'success' : 'danger' }}">
                            {{ $subscription->status }}
                        </x-filament::badge>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold">Plan</span>
                    <span>{{ $subscription->membership->name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold">Fecha Inicio</span>
                    <span>{{ $subscription->start_date->format('d-m-Y') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold">Fecha Fin</span>
                    <span>{{ $subscription->end_date->format('d-m-Y') }}</span>
                </div>

                <div class="flex flex-col">
                    <span class="font-bold">Numero Casillero</span>
                    <div class="w-24">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                wire:model="key_number"
                            />
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-4">
                <span class="text-red-600 font-bold">Sin suscripción activa</span>
            </div>
        @endif
        <x-slot name="footerActions">
            @if ($subscription)
                <x-filament::button wire:click="checkIn" disabled="{{ $subscription->status != 'active' }}">
                    Check In
                </x-filament::button>
            @endif
        </x-slot>
    @endif
</x-filament::modal>
