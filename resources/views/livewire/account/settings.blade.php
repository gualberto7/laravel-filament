<div>
    <form wire:submit="updatePassword">
        {{ $this->form }}

        <div class="flex justify-end mt-6">
            <x-filament::button color="primary" type="submit">
                Cambiar contraseña
            </x-filament::button>
        </div>
    </form>
</div>
