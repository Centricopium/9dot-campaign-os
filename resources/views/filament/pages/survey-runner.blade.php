<x-filament-panels::page>

    <style>
        /* ============================================================
           SURVEY RUNNER — LIGHT / POLISHED THEME
        ============================================================ */

        .survey-runner {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sr-grid {
            display: grid;
            gap: 16px;
        }

        .sr-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sr-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        /* ------------------------------------------------------------
           CARDS
        ------------------------------------------------------------ */

        .sr-card,
        .sr-kpi,
        .sr-question {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.04),
                0 4px 14px rgba(0, 0, 0, 0.035);
        }

        .sr-kpi {
            padding: 18px;
            min-width: 0;
            transition:
                transform 0.15s ease,
                box-shadow 0.15s ease,
                border-color 0.15s ease;
        }

        .sr-kpi:hover {
            transform: translateY(-1px);
            border-color: #d1d5db;
            box-shadow:
                0 4px 18px rgba(0, 0, 0, 0.07);
        }

        /* ------------------------------------------------------------
           TYPOGRAPHY
        ------------------------------------------------------------ */

        .sr-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 7px;
        }

        .sr-value {
            font-size: 16px;
            font-weight: 650;
            color: #111827;
        }

        .sr-number {
            font-size: 28px;
            line-height: 1.1;
            font-weight: 750;
            color: #111827;
        }

        .sr-subtitle {
            margin-top: 5px;
            font-size: 12px;
            color: #6b7280;
        }

        .sr-icon {
            font-size: 23px;
            line-height: 1;
            margin-bottom: 10px;
        }

        /* ------------------------------------------------------------
           PROFILE
        ------------------------------------------------------------ */

        .sr-profile {
            border: 1px solid #dbeafe;
            border-radius: 14px;
            padding: 20px;
            background:
                linear-gradient(
                    135deg,
                    #f8fafc 0%,
                    #ffffff 100%
                );
            box-shadow:
                0 4px 18px rgba(0, 0, 0, 0.045);
        }

        .sr-voter-profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .sr-voter-name {
            font-size: 21px;
            font-weight: 750;
            color: #111827;
        }

        .sr-voter-meta {
            margin-top: 5px;
            font-size: 12px;
            color: #6b7280;
        }

        .sr-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 650;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        /* ------------------------------------------------------------
           INPUTS
        ------------------------------------------------------------ */

        .sr-input,
        .sr-select,
        .sr-textarea {
            width: 100%;
            border-radius: 9px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111827;
            padding: 10px 12px;
            outline: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.025);
            transition:
                border-color 0.15s ease,
                box-shadow 0.15s ease;
        }

        .sr-input {
            min-height: 44px;
        }

        .sr-select {
            min-height: 44px;
            cursor: pointer;
        }

        .sr-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .sr-input::placeholder,
        .sr-textarea::placeholder {
            color: #9ca3af;
        }

        .sr-input:focus,
        .sr-select:focus,
        .sr-textarea:focus {
            border-color: #93c5fd;
            box-shadow:
                0 0 0 3px rgba(59, 130, 246, 0.10);
        }

        /* ------------------------------------------------------------
           OPTIONS
        ------------------------------------------------------------ */

        .sr-option-list {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .sr-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 13px;
            border-radius: 9px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            cursor: pointer;
            transition:
                border-color 0.15s ease,
                background 0.15s ease,
                box-shadow 0.15s ease;
        }

        .sr-option:hover {
            border-color: #bfdbfe;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.035);
        }

        .sr-option input {
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
        }

        .sr-option span {
            font-size: 13px;
            color: #374151;
        }

        /* ------------------------------------------------------------
           HELP / WARNING
        ------------------------------------------------------------ */

        .sr-help {
            margin-top: 7px;
            font-size: 12px;
            color: #6b7280;
        }

        .sr-warning {
            margin-top: 8px;
            padding: 9px 11px;
            border-radius: 8px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 12px;
        }

        /* ------------------------------------------------------------
           QUESTIONS
        ------------------------------------------------------------ */

        .sr-question {
            padding: 18px;
            margin-bottom: 14px;
            transition:
                border-color 0.15s ease,
                box-shadow 0.15s ease;
        }

        .sr-question:hover {
            border-color: #dbeafe;
            box-shadow:
                0 4px 16px rgba(0, 0, 0, 0.045);
        }

        .sr-question-title {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            line-height: 1.5;
            font-weight: 650;
            color: #111827;
            margin-bottom: 14px;
        }

        .sr-question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            width: 29px;
            height: 29px;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            font-size: 12px;
            font-weight: 700;
        }

        .sr-required {
            color: #dc2626;
            margin-left: 3px;
        }

        /* ------------------------------------------------------------
           TABLE
        ------------------------------------------------------------ */

        .sr-table-wrapper {
            overflow-x: auto;
        }

        .sr-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        .sr-table th,
        .sr-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            white-space: nowrap;
            font-size: 13px;
        }

        .sr-table th {
            color: #6b7280;
            background: #f9fafb;
            font-size: 12px;
            font-weight: 650;
        }

        .sr-table thead tr th:first-child {
            border-top-left-radius: 8px;
        }

        .sr-table thead tr th:last-child {
            border-top-right-radius: 8px;
        }

        .sr-table tbody tr {
            transition: background 0.15s ease;
        }

        .sr-table tbody tr:hover {
            background: #f9fafb;
        }

        .sr-name {
            font-weight: 650;
            color: #111827;
        }

        .sr-muted {
            color: #6b7280;
        }

        .sr-support {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
        }

        /* ------------------------------------------------------------
           ACTIONS
        ------------------------------------------------------------ */

        .sr-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .sr-save-area {
            display: flex;
            justify-content: flex-end;
            padding-top: 8px;
        }

        /* ------------------------------------------------------------
           EMPTY STATE
        ------------------------------------------------------------ */

        .sr-empty {
            padding: 42px 20px;
            text-align: center;
            color: #6b7280;
        }

        .sr-empty-icon {
            font-size: 38px;
            margin-bottom: 9px;
        }

        /* ------------------------------------------------------------
           DARK MODE
        ------------------------------------------------------------ */

        html.dark .sr-card,
        html.dark .sr-kpi,
        html.dark .sr-question {
            border-color: rgba(168, 85, 247, 0.16);
            background: linear-gradient(145deg, #1a1721, #15121b);
            box-shadow: 0 14px 34px -28px rgba(168, 85, 247, 0.48);
        }

        html.dark .sr-kpi:hover,
        html.dark .sr-question:hover {
            border-color: rgba(168, 85, 247, 0.42);
            background: linear-gradient(145deg, #201a2a, #18131f);
        }

        html.dark .sr-label,
        html.dark .sr-subtitle,
        html.dark .sr-voter-meta,
        html.dark .sr-help,
        html.dark .sr-muted,
        html.dark .sr-empty {
            color: #a8a1b3;
        }

        html.dark .sr-value,
        html.dark .sr-number,
        html.dark .sr-voter-name,
        html.dark .sr-question-title,
        html.dark .sr-name,
        html.dark .sr-table td {
            color: #f7f4fb;
        }

        html.dark .sr-profile {
            border-color: rgba(168, 85, 247, 0.20);
            background:
                radial-gradient(circle at 92% 0%, rgba(147, 51, 234, 0.16), transparent 34%),
                linear-gradient(135deg, #1b1723, #14111a);
            box-shadow: 0 18px 44px -30px rgba(168, 85, 247, 0.48);
        }

        html.dark .sr-badge,
        html.dark .sr-question-number {
            border-color: rgba(168, 85, 247, 0.26);
            background: rgba(124, 58, 237, 0.16);
            color: #d8b4fe;
        }

        html.dark .sr-input,
        html.dark .sr-select,
        html.dark .sr-textarea {
            border-color: rgba(168, 85, 247, 0.20);
            background: #100e15;
            color: #f7f4fb;
            color-scheme: dark;
        }

        html.dark .sr-input::placeholder,
        html.dark .sr-textarea::placeholder {
            color: #746d7f;
        }

        html.dark .sr-input:focus,
        html.dark .sr-select:focus,
        html.dark .sr-textarea:focus {
            border-color: rgba(168, 85, 247, 0.70);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.14);
        }

        html.dark .sr-option {
            border-color: rgba(168, 85, 247, 0.16);
            background: #17131d;
        }

        html.dark .sr-option:hover {
            border-color: rgba(168, 85, 247, 0.40);
            background: #21182b;
        }

        html.dark .sr-option span {
            color: #ded8e7;
        }

        html.dark .sr-table th {
            border-bottom-color: rgba(168, 85, 247, 0.18);
            background: #17131d;
            color: #aaa2b5;
        }

        html.dark .sr-table td {
            border-bottom-color: rgba(168, 85, 247, 0.10);
        }

        html.dark .sr-table tbody tr:hover {
            background: rgba(124, 58, 237, 0.08);
        }

        html.dark .sr-support {
            border-color: rgba(168, 85, 247, 0.18);
            background: #211a29;
            color: #d8d1e1;
        }

        html.dark .sr-warning {
            border-color: rgba(248, 113, 113, 0.28);
            background: rgba(127, 29, 29, 0.22);
            color: #fca5a5;
        }

        /* ------------------------------------------------------------
           RESPONSIVE
        ------------------------------------------------------------ */

        @media (max-width: 1024px) {

            .sr-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

        }

        @media (max-width: 768px) {

            .sr-grid-2,
            .sr-grid-4 {
                grid-template-columns: 1fr;
            }

            .sr-actions {
                justify-content: flex-start;
            }

            .sr-save-area {
                justify-content: stretch;
            }

            .sr-voter-name {
                font-size: 19px;
            }

        }

        @media (max-width: 640px) {

            .sr-table {
                min-width: 760px;
            }

        }
    </style>


    <div class="survey-runner">


        {{-- ============================================================
             SURVEY CONTROL
        ============================================================= --}}

        <x-filament::section>

            <x-slot name="heading">
                📝 Survey Runner
            </x-slot>

            <x-slot name="description">
                Select a survey and search a household to begin voter-level data collection.
            </x-slot>

            <div class="sr-grid sr-grid-2">


                {{-- Survey --}}

                <div>

                    <label class="sr-label">
                        Survey
                    </label>

                    <select
                        wire:model="surveyId"
                        class="sr-select"
                    >

                        <option value="">
                            Select Survey
                        </option>

                        @foreach($this->surveys as $survey)

                            <option value="{{ $survey->id }}">
                                {{ $survey->name }}
                            </option>

                        @endforeach

                    </select>

                    <div class="sr-help">
                        Choose the survey questionnaire before starting.
                    </div>

                </div>


                {{-- Search House --}}

                <div>

                    <label class="sr-label">
                        Search House
                    </label>

                    <div
                        style="
                            display:flex;
                            gap:10px;
                            align-items:stretch;
                        "
                    >

                        <input
                            type="text"
                            wire:model.defer="search"
                            wire:keydown.enter="searchHouse"
                            placeholder="House No / Head of Family / Mobile"
                            class="sr-input"
                        />

                        <x-filament::button
                            wire:click="searchHouse"
                            icon="heroicon-o-magnifying-glass"
                        >
                            Search
                        </x-filament::button>

                    </div>

                    <div class="sr-help">
                        Search the household before selecting a voter.
                    </div>

                </div>

            </div>

        </x-filament::section>


        {{-- ============================================================
             HOUSE INFORMATION
        ============================================================= --}}

        @if($house)

            <x-filament::section>

                <x-slot name="heading">
                    🏠 House Information
                </x-slot>

                <x-slot name="description">
                    Household details and registered voter count.
                </x-slot>

                <div class="sr-grid sr-grid-4">


                    {{-- House Number --}}

                    <div class="sr-kpi">

                        <div class="sr-icon">
                            🏠
                        </div>

                        <div class="sr-label">
                            House Number
                        </div>

                        <div class="sr-number">
                            {{ $house->house_no ?: '-' }}
                        </div>

                    </div>


                    {{-- Head --}}

                    <div class="sr-kpi">

                        <div class="sr-icon">
                            👤
                        </div>

                        <div class="sr-label">
                            Head of Family
                        </div>

                        <div class="sr-value">
                            {{ $house->head_of_family ?: '-' }}
                        </div>

                    </div>


                    {{-- Mobile --}}

                    <div class="sr-kpi">

                        <div class="sr-icon">
                            📞
                        </div>

                        <div class="sr-label">
                            Mobile
                        </div>

                        <div class="sr-value">
                            {{ $house->mobile ?: '-' }}
                        </div>

                    </div>


                    {{-- Members --}}

                    <div class="sr-kpi">

                        <div class="sr-icon">
                            👥
                        </div>

                        <div class="sr-label">
                            Total Members
                        </div>

                        <div class="sr-number">
                            {{ count($voters) }}
                        </div>

                        <div class="sr-subtitle">
                            Registered voters
                        </div>

                    </div>

                </div>

            </x-filament::section>


            {{-- ========================================================
                 FAMILY MEMBERS
            ========================================================= --}}

            <x-filament::section>

                <x-slot name="heading">
                    👨‍👩‍👧‍👦 Family Members
                </x-slot>

                <x-slot name="description">
                    Select a voter to start the questionnaire.
                </x-slot>

                <div class="sr-table-wrapper">

                    <table class="sr-table">

                        <thead>

                            <tr>

                                <th>
                                    Voter
                                </th>

                                <th>
                                    EPIC
                                </th>

                                <th>
                                    Age
                                </th>

                                <th>
                                    Gender
                                </th>

                                <th>
                                    Support
                                </th>

                                <th style="text-align:right;">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($voters as $voter)

                                <tr>

                                    <td>

                                        <div class="sr-name">
                                            {{ $voter->name }}
                                        </div>

                                    </td>

                                    <td class="sr-muted">
                                        {{ $voter->epic_no ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $voter->age ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $voter->gender ?: '-' }}
                                    </td>

                                    <td>

                                        <span class="sr-support">
                                            {{ $voter->support_level ?: 'Neutral' }}
                                        </span>

                                    </td>

                                    <td>

                                        <div class="sr-actions">

                                            <x-filament::button
                                                size="sm"
                                                icon="heroicon-o-play"
                                                wire:click="startSurvey({{ $voter->id }})"
                                            >
                                                Start Survey
                                            </x-filament::button>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="sr-empty"
                                    >

                                        <div class="sr-empty-icon">
                                            👥
                                        </div>

                                        No family members found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </x-filament::section>

        @endif


        {{-- ============================================================
             SELECTED VOTER
        ============================================================= --}}

        @if($selectedVoter)

            <div class="sr-profile">

                <div class="sr-voter-profile">

                    <div>

                        <div class="sr-label">
                            Selected Voter
                        </div>

                        <div class="sr-voter-name">
                            {{ $selectedVoter->name }}
                        </div>

                        <div class="sr-voter-meta">

                            EPIC:
                            {{ $selectedVoter->epic_no ?: '-' }}

                            &nbsp; • &nbsp;

                            Age:
                            {{ $selectedVoter->age ?: '-' }}

                            &nbsp; • &nbsp;

                            Gender:
                            {{ $selectedVoter->gender ?: '-' }}

                        </div>

                    </div>

                    <div>

                        <span class="sr-badge">
                            🎯 Survey In Progress
                        </span>

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 SURVEY QUESTIONS
            ========================================================= --}}

            <x-filament::section>

                <x-slot name="heading">
                    📋 Survey Questions
                </x-slot>

                <x-slot name="description">
                    Record the selected voter's responses carefully.
                </x-slot>


                @foreach($questions as $index => $question)

                    @php

                        $questionOptions = $question->options ?? [];

                        if (is_string($questionOptions)) {

                            $decodedOptions = json_decode(
                                $questionOptions,
                                true
                            );

                            if (
                                json_last_error() === JSON_ERROR_NONE
                                && is_array($decodedOptions)
                            ) {

                                $questionOptions = $decodedOptions;

                            } else {

                                $questionOptions = preg_split(
                                    '/\r\n|\r|\n/',
                                    $questionOptions
                                );

                            }

                        }

                        if (! is_array($questionOptions)) {

                            $questionOptions = [];

                        }

                        $questionOptions = collect($questionOptions)
                            ->map(function ($option) {

                                if (is_array($option)) {

                                    return $option['label']
                                        ?? $option['value']
                                        ?? '';

                                }

                                return trim((string) $option);

                            })
                            ->filter()
                            ->values()
                            ->all();

                    @endphp


                    <div class="sr-question">


                        {{-- Question Title --}}

                        <div class="sr-question-title">

                            <span class="sr-question-number">
                                {{ $index + 1 }}
                            </span>

                            <span>

                                {{ $question->question }}

                                @if($question->required)

                                    <span class="sr-required">
                                        *
                                    </span>

                                @endif

                            </span>

                        </div>


                        {{-- =================================================
                             TEXT
                        ================================================== --}}

                        @if($question->type === 'text')

                            <input
                                type="text"
                                wire:model="answers.{{ $question->id }}"
                                placeholder="Enter answer..."
                                class="sr-input"
                            />


                        {{-- =================================================
                             TEXTAREA
                        ================================================== --}}

                        @elseif($question->type === 'textarea')

                            <textarea
                                wire:model="answers.{{ $question->id }}"
                                rows="4"
                                placeholder="Enter detailed answer..."
                                class="sr-textarea"
                            ></textarea>


                        {{-- =================================================
                             NUMBER
                        ================================================== --}}

                        @elseif($question->type === 'number')

                            <input
                                type="number"
                                wire:model="answers.{{ $question->id }}"
                                placeholder="Enter number..."
                                class="sr-input"
                            />


                        {{-- =================================================
                             SELECT
                        ================================================== --}}

                        @elseif($question->type === 'select')

                            <select
                                wire:model="answers.{{ $question->id }}"
                                class="sr-select"
                            >

                                <option value="">
                                    -- Select Answer --
                                </option>

                                @foreach($questionOptions as $option)

                                    <option value="{{ $option }}">
                                        {{ $option }}
                                    </option>

                                @endforeach

                            </select>

                            @if(count($questionOptions) === 0)

                                <div class="sr-warning">
                                    ⚠ No options configured for this question.
                                </div>

                            @endif


                        {{-- =================================================
                             YES / NO
                        ================================================== --}}

                        @elseif($question->type === 'yes_no')

                            <select
                                wire:model="answers.{{ $question->id }}"
                                class="sr-select"
                            >

                                <option value="">
                                    -- Select Answer --
                                </option>

                                <option value="Yes">
                                    Yes
                                </option>

                                <option value="No">
                                    No
                                </option>

                            </select>


                        {{-- =================================================
                             RADIO
                        ================================================== --}}

                        @elseif($question->type === 'radio')

                            <div class="sr-option-list">

                                @forelse($questionOptions as $option)

                                    <label class="sr-option">

                                        <input
                                            type="radio"
                                            wire:model="answers.{{ $question->id }}"
                                            value="{{ $option }}"
                                        />

                                        <span>
                                            {{ $option }}
                                        </span>

                                    </label>

                                @empty

                                    <div class="sr-warning">
                                        ⚠ No options configured.
                                    </div>

                                @endforelse

                            </div>


                        {{-- =================================================
                             CHECKBOX
                        ================================================== --}}

                        @elseif($question->type === 'checkbox')

                            <div class="sr-option-list">

                                @forelse($questionOptions as $option)

                                    <label class="sr-option">

                                        <input
                                            type="checkbox"
                                            wire:model="answers.{{ $question->id }}"
                                            value="{{ $option }}"
                                        />

                                        <span>
                                            {{ $option }}
                                        </span>

                                    </label>

                                @empty

                                    <div class="sr-warning">
                                        ⚠ No options configured.
                                    </div>

                                @endforelse

                            </div>


                        {{-- =================================================
                             DEFAULT
                        ================================================== --}}

                        @else

                            <input
                                type="text"
                                wire:model="answers.{{ $question->id }}"
                                placeholder="Enter answer..."
                                class="sr-input"
                            />

                        @endif

                    </div>

                @endforeach


                {{-- ========================================================
                     SAVE SURVEY
                ========================================================= --}}

                @if(count($questions) > 0)

                    <div class="sr-save-area">

                        <x-filament::button
                            color="success"
                            size="lg"
                            icon="heroicon-o-check-circle"
                            wire:click="saveSurvey"
                        >
                            Save Survey
                        </x-filament::button>

                    </div>

                @endif

            </x-filament::section>

        @endif


        {{-- ============================================================
             EMPTY STATE
        ============================================================= --}}

        @if(!$house)

            <x-filament::section>

                <div class="sr-empty">

                    <div class="sr-empty-icon">
                        🏠
                    </div>

                    <div
                        style="
                            font-size:18px;
                            font-weight:700;
                            color:#111827;
                        "
                    >
                        Search a Household
                    </div>

                    <div style="margin-top:6px;">
                        Select a survey and search for a house to begin.
                    </div>

                </div>

            </x-filament::section>

        @endif

    </div>

</x-filament-panels::page>
