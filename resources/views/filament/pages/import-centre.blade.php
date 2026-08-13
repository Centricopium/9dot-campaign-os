<x-filament-panels::page>

    {{-- Import Cards --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">

        {{-- Village --}}
        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->can('import.village'))

            <x-filament::section>
                <div class="space-y-3 text-center">

                    <div class="text-5xl">
                        📍
                    </div>

                    <h2 class="text-lg font-bold">
                        Village Import
                    </h2>

                    <p class="text-sm text-gray-500">
                        Import Village Master Excel
                    </p>

                    <x-filament::button
                        tag="a"
                        href="#village-import"
                    >
                        Import Villages
                    </x-filament::button>

                </div>
            </x-filament::section>

        @endif


        {{-- Booth --}}
        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->can('import.booth'))

            <x-filament::section>
                <div class="space-y-3 text-center">

                    <div class="text-5xl">
                        🗳️
                    </div>

                    <h2 class="text-lg font-bold">
                        Booth Import
                    </h2>

                    <p class="text-sm text-gray-500">
                        Import Booth Master Excel
                    </p>

                    <x-filament::button
                        tag="a"
                        href="#booth-import"
                        color="warning"
                    >
                        Import Booths
                    </x-filament::button>

                </div>
            </x-filament::section>

        @endif


        {{-- House --}}
        <x-filament::section>
            <div class="space-y-3 text-center">

                <div class="text-5xl">
                    🏠
                </div>

                <h2 class="text-lg font-bold">
                    House Import
                </h2>

                <p class="text-sm text-gray-500">
                    Auto Create / Update Houses
                </p>

                <x-filament::button
                    color="success"
                    disabled
                >
                    Import Houses
                </x-filament::button>

            </div>
        </x-filament::section>


        {{-- Voter --}}
        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->can('import.voter'))

            <x-filament::section>
                <div class="space-y-3 text-center">

                    <div class="text-5xl">
                        👤
                    </div>

                    <h2 class="text-lg font-bold">
                        Voter Import
                    </h2>

                    <p class="text-sm text-gray-500">
                        Import Electoral Roll Excel
                    </p>

                    <x-filament::button
                        tag="a"
                        href="#voter-import"
                        color="danger"
                    >
                        Import Voters
                    </x-filament::button>

                </div>
            </x-filament::section>

        @endif

    </div>


    {{-- Village Excel Import --}}
    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->can('import.village'))

        <div
            id="village-import"
            class="mt-6 scroll-mt-6"
        >
            <livewire:village-import-form />
        </div>

    @endif


    {{-- Booth Excel Import --}}
    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->can('import.booth'))

        <div
            id="booth-import"
            class="mt-6 scroll-mt-6"
        >
            <livewire:booth-import-form />
        </div>

    @endif


    {{-- Voter Excel Import --}}
    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->can('import.voter'))

        <div
            id="voter-import"
            class="mt-6 scroll-mt-6"
        >
            <livewire:voter-import-form />
        </div>

    @endif

</x-filament-panels::page>