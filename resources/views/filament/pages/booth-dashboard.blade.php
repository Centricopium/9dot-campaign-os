<x-filament-panels::page>

    <x-filament::section>

        <x-slot name="heading">
            🏛 Booth Dashboard
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>

                <label class="block text-sm font-medium mb-2">
                    Select Booth
                </label>

                <select
                    wire:model.live="boothId"
                    class="w-full rounded-lg border-gray-300">

                    <option value="">
                        -- Select Booth --
                    </option>

                    @foreach ($this->booths as $booth)

                        <option value="{{ $booth->id }}">
                            {{ $booth->booth_name }}
                        </option>

                    @endforeach

                </select>

            </div>

        </div>

    </x-filament::section>

    @if ($boothId)

        <x-filament::section>

            <x-slot name="heading">
                📊 Party Support
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

                @foreach (\App\Models\PoliticalParty::where('is_active', true)->orderBy('sort_order')->get() as $party)

                    <div class="rounded-xl border p-5 text-center">

                        <div class="text-2xl">
                            {{ $party->symbol }}
                        </div>

                        <div class="font-bold mt-2">
                            {{ $party->short_name }}
                        </div>

                        <div class="text-3xl font-bold mt-3">

                            {{
                                \App\Models\Voter::whereHas('house', function ($q) {
                                    $q->where('booth_id', $this->boothId);
                                })
                                ->where('political_party_id', $party->id)
                                ->count()
                            }}

                        </div>

                    </div>

                @endforeach

            </div>

        </x-filament::section>

    @endif

</x-filament-panels::page>