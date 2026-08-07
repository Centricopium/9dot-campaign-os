<x-filament-panels::page>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <x-filament::section>
            <div class="text-center space-y-3">
                <div class="text-5xl">📍</div>

                <h2 class="text-lg font-bold">
                    Village Import
                </h2>

                <p class="text-sm text-gray-500">
                    Import Village Master Excel
                </p>

                <x-filament::button color="primary">
                    Import Villages
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center space-y-3">
                <div class="text-5xl">🗳️</div>

                <h2 class="text-lg font-bold">
                    Booth Import
                </h2>

                <p class="text-sm text-gray-500">
                    Import Booth Master Excel
                </p>

                <x-filament::button color="warning">
                    Import Booths
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center space-y-3">
                <div class="text-5xl">🏠</div>

                <h2 class="text-lg font-bold">
                    House Import
                </h2>

                <p class="text-sm text-gray-500">
                    Auto Create / Update Houses
                </p>

                <x-filament::button color="success">
                    Import Houses
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center space-y-3">
                <div class="text-5xl">👤</div>

                <h2 class="text-lg font-bold">
                    Voter Import
                </h2>

                <p class="text-sm text-gray-500">
                    Import Electoral Roll Excel
                </p>

                <x-filament::button color="danger">
                    Import Voters
                </x-filament::button>
            </div>
        </x-filament::section>

    </div>

</x-filament-panels::page>