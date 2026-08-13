<x-filament-widgets::widget>

    <x-filament::section>

        <x-slot name="heading">
            🗳 Party Support
        </x-slot>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            @foreach($this->getParties() as $party)

                <div
                    class="rounded-xl border bg-white dark:bg-gray-900 p-5 shadow text-center">

                    <div class="text-xl">

                        {{ $party->symbol }}

                    </div>

                    <div class="font-bold mt-2">

                        {{ $party->short_name }}

                    </div>

                    <div
                        class="text-3xl font-bold mt-3">

                        {{ $party->count }}

                    </div>

                </div>

            @endforeach

        </div>

    </x-filament::section>

</x-filament-widgets::widget>