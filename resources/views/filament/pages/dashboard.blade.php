<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            User details
        </x-slot>

        <x-slot name="description">
            This is all the information we hold about the user.
        </x-slot>

        <div class="w-1/2 mx-auto flex flex-col">
            {{ $this->form }}
        </div>
    </x-filament::section>
</x-filament-panels::page>
