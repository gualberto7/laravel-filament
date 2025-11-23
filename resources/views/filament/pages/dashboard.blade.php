<x-filament-panels::page>
    <x-filament::section class="h-100">
        <x-slot name="heading">
            User details
        </x-slot>

        <x-slot name="description">
            This is all the information we hold about the user.
        </x-slot>

        <div class="w-1/2 h-full mx-auto flex flex-col">
            <livewire:client.search :current-gym="$currentGym" />
        </div>
    </x-filament::section>
</x-filament-panels::page>
