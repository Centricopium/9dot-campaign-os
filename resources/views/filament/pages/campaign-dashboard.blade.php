<x-filament-panels::page>

    <div class="space-y-6">

        <x-filament::section>
            <x-slot name="heading">
                🗳️ 9Dot Campaign Dashboard
            </x-slot>

            <x-slot name="description">
                Election Campaign Operating System
            </x-slot>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-xl border p-5">
                    <div class="text-sm text-gray-500">Total Voters</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ \App\Models\Voter::count() }}
                    </div>
                </div>

                <div class="rounded-xl border p-5">
                    <div class="text-sm text-gray-500">Total Houses</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ \App\Models\House::count() }}
                    </div>
                </div>

                <div class="rounded-xl border p-5">
                    <div class="text-sm text-gray-500">Total Booths</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ \App\Models\Booth::count() }}
                    </div>
                </div>

                <div class="rounded-xl border p-5">
                    <div class="text-sm text-gray-500">Total Villages</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ \App\Models\Village::count() }}
                    </div>
                </div>

            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Survey Overview
            </x-slot>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                <div class="rounded-xl border p-5">
                    <div class="text-sm text-gray-500">Surveys</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ \App\Models\Survey::count() }}
                    </div>
                </div>

                <div class="rounded-xl border p-5">
                    <div class="text-sm text-gray-500">Responses</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ \App\Models\SurveyResponse::count() }}
                    </div>
                </div>

                <div class="rounded-xl border p-5">
                    <div class="text-sm text-gray-500">Active Users</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ \App\Models\User::where('is_active', true)->count() }}
                    </div>
                </div>

            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Coming Next
            </x-slot>

            <ul class="list-disc pl-6 space-y-2">
                <li>✅ Dynamic Survey Engine</li>
                <li>✅ Voter 360° Profile</li>
                <li>✅ Household Intelligence</li>
                <li>✅ Booth Dashboard</li>
                <li>✅ War Room</li>
                <li>✅ AI Analytics</li>
            </ul>
        </x-filament::section>

    </div>

</x-filament-panels::page>