<div>
    <form wire:submit="submit" class="space-y-6">

        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                Import Voters
            </x-filament::button>
        </div>

    </form>
</div>