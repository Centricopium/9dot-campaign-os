@if($this->voter)

<x-filament::section class="mt-6">

    <x-slot name="heading">
        Voter Details
    </x-slot>

    <div class="grid grid-cols-2 gap-4">

        <div>
            <strong>Name</strong><br>
            {{ $this->voter->name }}
        </div>

        <div>
            <strong>EPIC</strong><br>
            {{ $this->voter->epic_no }}
        </div>

        <div>
            <strong>Mobile</strong><br>
            {{ $this->voter->mobile }}
        </div>

        <div>
            <strong>Booth</strong><br>
            {{ optional($this->voter->booth)->booth_name }}
        </div>

    </div>

</x-filament::section>

@endif