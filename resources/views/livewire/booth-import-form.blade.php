<div>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button
                type="submit"
                color="warning"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                Import Booths
            </x-filament::button>
        </div>
    </form>
</div>