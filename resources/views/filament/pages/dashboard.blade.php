<x-filament-panels::page>
    <x-filament::section class="h-100">
        <x-slot name="heading">
            Bienvenido de nuevo, {{ auth()->user()->name }}
        </x-slot>

        <x-slot name="description">
            {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </x-slot>

        <x-slot name="afterHeader">
            <x-filament::button href="{{ \App\Filament\Resources\Subscriptions\SubscriptionResource::getUrl('create') }}" tag="a">
                Nueva Suscripción
            </x-filament::button>
        </x-slot>

        <div class="w-1/2 h-full mx-auto flex flex-col">
            <livewire:client.search :current-gym="$currentGym" />
        </div>
    </x-filament::section>
</x-filament-panels::page>
