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
            </div>
        @else
            <div class="mb-4">
                <span class="text-red-600 font-bold">Sin suscripción activa</span>
            </div>
        @endif
        <x-slot name="footerActions">
            <x-filament::button wire:click="checkIn">
                Check In
            </x-filament::button>
        </x-slot>
    @endif
</x-filament::modal>
