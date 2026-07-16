<x-filament-panels::page>

    <div class="space-y-6">

        <x-filament::section>

            <x-slot name="heading">
                🔍 Search House
            </x-slot>

            <div class="flex gap-3">

                <input
                    type="text"
                    wire:model="search"
                    placeholder="House No / Head of Family / Mobile"
                    class="w-full rounded-lg border-gray-300"
                />

                <x-filament::button wire:click="searchHouse">
                    Search
                </x-filament::button>

            </div>

        </x-filament::section>

        @if($house)

            {{-- House Information --}}
            <x-filament::section>

                <x-slot name="heading">
                    🏠 House Information
                </x-slot>

                <div class="grid grid-cols-2 gap-6">

                    <div>
                        <strong>House No</strong><br>
                        {{ $house->house_no }}
                    </div>

                    <div>
                        <strong>Head of Family</strong><br>
                        {{ $house->head_of_family }}
                    </div>

                    <div>
                        <strong>Mobile</strong><br>
                        {{ $house->mobile }}
                    </div>

                    <div>
                        <strong>Total Family Members</strong><br>
                        {{ $house->voters->count() }}
                    </div>

                </div>

            </x-filament::section>
            <x-filament::section>

    <x-slot name="heading">
        📊 Political Summary
    </x-slot>

    <div class="grid grid-cols-4 gap-4">

        <div class="rounded-lg bg-green-100 p-4 text-center">
            <div class="text-2xl font-bold">
                {{ $politicalSummary['congress'] ?? 0 }}
            </div>
            <div>Congress</div>
        </div>

        <div class="rounded-lg bg-orange-100 p-4 text-center">
            <div class="text-2xl font-bold">
                {{ $politicalSummary['bjp'] ?? 0 }}
            </div>
            <div>BJP</div>
        </div>

        <div class="rounded-lg bg-gray-100 p-4 text-center">
            <div class="text-2xl font-bold">
                {{ $politicalSummary['neutral'] ?? 0 }}
            </div>
            <div>Neutral</div>
        </div>

        <div class="rounded-lg bg-yellow-100 p-4 text-center">
            <div class="text-2xl font-bold">
                {{ $politicalSummary['undecided'] ?? 0 }}
            </div>
            <div>Undecided</div>
        </div>

    </div>

</x-filament::section>

    <x-filament::section>

    <x-slot name="heading">
        📍 Location Information
    </x-slot>

    <div class="grid grid-cols-3 gap-6">

        <div>
            <strong>Booth</strong><br>

            {{ $house->booth?->booth_name ?? '-' }}

        </div>

        <div>
            <strong>Village</strong><br>

            {{ $house->booth?->village?->name ?? '-' }}

        </div>

        <div>
            <strong>Constituency</strong><br>

            {{ $house->booth?->village?->constituency?->name ?? '-' }}

        </div>

    </div>

</x-filament::section>
            {{-- Family Members --}}
            <x-filament::section>

                <x-slot name="heading">
                    👨 Family Members
                </x-slot>

                @if($house->voters->count())

                    <table class="w-full text-sm">

                        <thead class="border-b">

                            <tr>

                                <th class="text-left py-2">Name</th>
                                <th class="text-left py-2">Age</th>
                                <th class="text-left py-2">Gender</th>
                                <th class="text-left py-2">Support</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($house->voters as $voter)

                                <tr class="border-b">

                                    <td class="py-2">{{ $voter->name }}</td>

                                    <td>{{ $voter->age }}</td>

                                    <td>{{ $voter->gender }}</td>

                                    <td>{{ $voter->support_level }}</td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @else

                    <div class="text-gray-500 py-4">
                        No family members found.
                    </div>

                @endif

            </x-filament::section>

        @endif

    </div>
    <x-filament::section>

    <x-slot name="heading">
        ⚡ Quick Actions
    </x-slot>

    <div class="flex flex-wrap gap-3">

        <x-filament::button
             color="primary"
            wire:click="startSurvey">

             📝 Start Survey

        </x-filament::button>

        <x-filament::button
            color="success">

            ➕ Add Family Member

        </x-filament::button>

        <x-filament::button
            color="gray">

            📞 Call Head

        </x-filament::button>

        <x-filament::button
            color="warning">

            💬 WhatsApp

        </x-filament::button>

    </div>

</x-filament::section>

</x-filament-panels::page>