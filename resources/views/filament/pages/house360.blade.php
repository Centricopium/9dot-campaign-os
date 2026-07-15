<x-filament-panels::page>

    <div class="space-y-6">

        {{-- House Summary --}}
        <x-filament::section>
            <x-slot name="heading">
                🏠 House Information
            </x-slot>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <div class="text-sm text-gray-500">House No</div>
                    <div class="font-bold text-lg">-</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Head of Family</div>
                    <div class="font-bold text-lg">-</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Mobile</div>
                    <div class="font-bold">-</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Booth</div>
                    <div class="font-bold">-</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Village</div>
                    <div class="font-bold">-</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Constituency</div>
                    <div class="font-bold">-</div>
                </div>

            </div>

        </x-filament::section>

        {{-- Family Members --}}
        <x-filament::section>

            <x-slot name="heading">
                👨‍👩‍👧 Family Members
            </x-slot>

            <p class="text-gray-500">
                Family members will appear here.
            </p>

        </x-filament::section>

        {{-- Political Summary --}}
        <x-filament::section>

            <x-slot name="heading">
                🗳 Political Summary
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold">0</div>
                        <div>Congress</div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold">0</div>
                        <div>BJP</div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold">0</div>
                        <div>Neutral</div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold">0</div>
                        <div>Undecided</div>
                    </div>
                </x-filament::card>

            </div>

        </x-filament::section>

        {{-- Survey Summary --}}
        <x-filament::section>

            <x-slot name="heading">
                📝 Survey Summary
            </x-slot>

            <p class="text-gray-500">
                Survey status will appear here.
            </p>

        </x-filament::section>

        {{-- Quick Actions --}}
        <x-filament::section>

            <x-slot name="heading">
                ⚡ Quick Actions
            </x-slot>

            <div class="flex flex-wrap gap-3">

                <x-filament::button color="primary">
                    ➕ Add Family Member
                </x-filament::button>

                <x-filament::button color="success">
                    📝 Start Survey
                </x-filament::button>

                <x-filament::button color="warning">
                    ☎ Call Head
                </x-filament::button>

                <x-filament::button color="gray">
                    📍 View GPS
                </x-filament::button>

            </div>

        </x-filament::section>

    </div>

</x-filament-panels::page>