<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <div style="margin-top: 20px">
            <x-filament::actions  :actions="$this->getFormActions()"/>
        </div>
    </form>
</x-filament-panels::page>