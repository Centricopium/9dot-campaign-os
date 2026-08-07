<x-filament-panels::page>

    <div class="space-y-6">

        {{-- Survey Selection --}}
        <x-filament::section>

            <x-slot name="heading">
                📝 Survey Runner
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="font-medium">
                        Survey
                    </label>

                    <select
                        wire:model="surveyId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">

                        <option value="">
                            Select Survey
                        </option>

                        @foreach($this->surveys as $survey)

                            <option value="{{ $survey->id }}">
                                {{ $survey->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="font-medium">
                        Search House
                    </label>

                    <div class="flex gap-2">

                        <input
                            wire:model.defer="search"
                            placeholder="House No / Head / Mobile"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" />

                        <x-filament::button
                            wire:click="searchHouse">

                            Search

                        </x-filament::button>

                    </div>

                </div>

            </div>

        </x-filament::section>

        {{-- House Information --}}
        @if($house)

            <x-filament::section>

                <x-slot name="heading">
                    🏠 House Information
                </x-slot>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

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
                        <strong>Total Members</strong><br>
                        {{ count($voters) }}
                    </div>

                </div>

            </x-filament::section>

            {{-- Family Members --}}
            <x-filament::section>

                <x-slot name="heading">
                    👨 Family Members
                </x-slot>

                <table class="w-full border-collapse">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left p-2">Name</th>
                            <th class="text-left p-2">EPIC</th>
                            <th class="text-left p-2">Age</th>
                            <th class="text-left p-2">Support</th>
                            <th class="text-left p-2">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($voters as $voter)

                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">

                                <td class="p-2">
                                    {{ $voter->name }}
                                </td>

                                <td class="p-2">
                                    {{ $voter->epic_no }}
                                </td>

                                <td class="p-2">
                                    {{ $voter->age }}
                                </td>

                                <td class="p-2">
                                    {{ $voter->support_level }}
                                </td>

                                <td class="p-2">

                                    <x-filament::button
                                        size="sm"
                                        wire:click="startSurvey({{ $voter->id }})">

                                        Start Survey

                                    </x-filament::button>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </x-filament::section>

        @endif

        {{-- Survey Questions --}}
        @if($selectedVoter)

            <x-filament::section>

                <x-slot name="heading">
                    📝 Survey Questions
                </x-slot>

                <p class="mb-4">

                    <strong>Selected Voter :</strong>

                    {{ $selectedVoter->name }}

                </p>

                @foreach($questions as $question)

    <div class="mb-5">

        <label class="block font-semibold mb-2">

            {{ $question->question }}

            @if($question->required)
                <span class="text-red-600">*</span>
            @endif

        </label>

        @switch($question->type)

            @case('text')

                <input
                    type="text"
                    wire:model="answers.{{ $question->id }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">

            @break


            @case('textarea')

                <textarea
                    wire:model="answers.{{ $question->id }}"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                </textarea>

            @break


            @case('number')

                <input
                    type="number"
                    wire:model="answers.{{ $question->id }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">

            @break


            @case('yes_no')

                <select
                    wire:model="answers.{{ $question->id }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">

                    <option value="">Select</option>

                    <option value="Yes">Yes</option>

                    <option value="No">No</option>

                </select>

            @break


            @default

                <input
                    type="text"
                    wire:model="answers.{{ $question->id }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">

        @endswitch

    </div>

@endforeach

                @if(count($questions))

                    <x-filament::button
                             color="success"
                            wire:click="saveSurvey">

                            💾 Save Survey

                    </x-filament::button>

                @endif

            </x-filament::section>

        @endif

    </div>

</x-filament-panels::page>