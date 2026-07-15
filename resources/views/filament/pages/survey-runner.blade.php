<x-filament-panels::page>

    <div class="space-y-6">

        <x-filament::section>
            <x-slot name="heading">
                Survey Runner
            </x-slot>

            <x-slot name="description">
                Start a new voter survey
            </x-slot>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Select Survey
                    </label>

                    <select
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                    >
                        <option>Select Survey...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Search Voter
                    </label>

                    <input
                        type="text"
                        placeholder="EPIC / Mobile / Name"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                    >
                </div>

            </div>

            <div class="mt-6">
                <x-filament::button>
                    Continue
                </x-filament::button>
            </div>

        </x-filament::section>

    </div>

</x-filament-panels::page>