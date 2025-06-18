<div>
    <form wire:submit="update">
        {{ $this->form }}
        
        <div class="flex justify-end mt-6">
            <x-filament::button color="primary" type="submit">
                Guardar cambios
            </x-filament::button>
        </div>
    </form>
</div>
