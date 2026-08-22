<x-filament-panels::page>

@php
    $data = $this->data ?? [];

    $overview = $data['overview'] ?? [];

    $surveys = $data['surveys'] ?? collect();

    $questions = $data['questions'] ?? [];

    $geography = $data['geography'] ?? [
        'constituencies' => [],
        'villages' => [],
        'booths' => [],
    ];

    $recentResponses = $data['recentResponses'] ?? collect();
@endphp


<style>

.si-wrapper {
    display:flex;
    flex-direction:column;
    gap:20px;
}


.si-grid {
    display:grid;
    gap:16px;
}


.si-grid-4 {
    grid-template-columns:repeat(4,minmax(0,1fr));
}


.si-grid-3 {
    grid-template-columns:repeat(3,minmax(0,1fr));
}


.si-card {
    border:1px solid rgb(229 231 235);
    border-radius:14px;
    padding:20px;
    background:white;
    color:rgb(17 24 39);
    transition:.15s;
}


.si-card:hover {
    transform:translateY(-2px);
    border-color:rgb(99 102 241);
}


.si-icon {
    font-size:25px;
    margin-bottom:10px;
}


.si-label {
    font-size:13px;
    color:rgb(107 114 128);
}


.si-number {
    margin-top:7px;
    font-size:30px;
    font-weight:750;
    color:rgb(17 24 39);
}


.si-subtitle {
    margin-top:6px;
    font-size:12px;
    color:rgb(107 114 128);
}


.si-section {

    border:1px solid rgb(229 231 235);
    border-radius:14px;
    overflow:hidden;
    background:white;

}


.si-section-header {

    padding:18px 20px;
    border-bottom:1px solid rgb(229 231 235);

}


.si-section-title {

    font-size:16px;
    font-weight:700;
    color:rgb(17 24 39);

}


.si-section-description {

    margin-top:5px;
    font-size:12px;
    color:rgb(107 114 128);

}


.si-section-body {

    padding:16px;

}


.si-select {

    width:100%;
    max-width:500px;
    min-height:44px;
    padding:10px 12px;
    border-radius:9px;
    border:1px solid rgb(209 213 219);
    background:white;
    color:rgb(17 24 39);

}


.si-table-wrapper {

    overflow-x:auto;

}


.si-table {

    width:100%;
    border-collapse:collapse;

}


.si-table th,
.si-table td {

    padding:12px;
    border-bottom:1px solid rgb(229 231 235);
    text-align:left;
    font-size:13px;
    white-space:nowrap;

}


.si-table th {

    color:rgb(107 114 128);
    font-size:12px;

}


.si-table tbody tr:hover {

    background:rgb(249 250 251);

}


.si-badge {

    display:inline-flex;
    padding:5px 10px;
    border-radius:999px;
    background:rgb(243 244 246);
    font-size:12px;
    font-weight:600;

}


.si-question {

    border:1px solid rgb(229 231 235);
    border-radius:12px;
    padding:16px;
    margin-bottom:14px;
    background:rgb(249 250 251);

}


.si-question-title {

    font-size:14px;
    font-weight:700;
    color:rgb(17 24 39);

}


.si-question-meta {

    margin-top:5px;
    font-size:12px;
    color:rgb(107 114 128);

}


.si-answer-row {

    display:flex;
    align-items:center;
    gap:12px;
    margin-top:12px;

}


.si-answer-name {

    width:120px;
    font-size:12px;

}


.si-bar {

    flex:1;
    height:8px;
    border-radius:999px;
    background:rgb(229 231 235);
    overflow:hidden;

}


.si-bar-fill {

    height:100%;
    background:rgb(99 102 241);

}


.si-answer-count {

    width:70px;
    text-align:right;
    font-size:12px;

}


.si-muted {

    color:rgb(107 114 128);
    font-size:11px;

}


.si-empty {

    padding:30px;
    text-align:center;
    color:rgb(107 114 128);

}


/* ============================================================
   DARK MODE
============================================================ */

html.dark .si-card,
html.dark .si-section {
    border-color:rgba(168,85,247,.16);
    background:linear-gradient(145deg,#1a1721,#15121b);
    color:#f7f4fb;
    box-shadow:0 16px 40px -30px rgba(168,85,247,.48);
}

html.dark .si-card:hover {
    border-color:rgba(168,85,247,.44);
    background:linear-gradient(145deg,#211a2b,#18131f);
    box-shadow:0 20px 38px -26px rgba(168,85,247,.58);
}

html.dark .si-number,
html.dark .si-section-title,
html.dark .si-question-title,
html.dark .si-table td,
html.dark .si-answer-name,
html.dark .si-answer-count {
    color:#f7f4fb;
}

html.dark .si-label,
html.dark .si-subtitle,
html.dark .si-section-description,
html.dark .si-question-meta,
html.dark .si-muted,
html.dark .si-empty {
    color:#a8a1b3;
}

html.dark .si-section-header {
    border-bottom-color:rgba(168,85,247,.14);
    background:linear-gradient(110deg,#1a1622,#131019 65%);
}

html.dark .si-section-body {
    background:#121018;
}

html.dark .si-select {
    border-color:rgba(168,85,247,.22);
    background:#100e15;
    color:#f7f4fb;
    color-scheme:dark;
}

html.dark .si-select:focus {
    border-color:rgba(168,85,247,.72);
    outline:none;
    box-shadow:0 0 0 3px rgba(124,58,237,.14);
}

html.dark .si-table th {
    border-bottom-color:rgba(168,85,247,.18);
    background:#17131d;
    color:#aaa2b5;
}

html.dark .si-table td {
    border-bottom-color:rgba(168,85,247,.10);
}

html.dark .si-table tbody tr:hover {
    background:rgba(124,58,237,.08);
}

html.dark .si-badge {
    border:1px solid rgba(168,85,247,.18);
    background:#211a29;
    color:#d8d1e1;
}

html.dark .si-question {
    border-color:rgba(168,85,247,.14);
    background:linear-gradient(145deg,#1b1722,#15121b);
}

html.dark .si-bar {
    background:#2a2431;
}

html.dark .si-bar-fill {
    background:linear-gradient(90deg,#7c3aed,#a855f7);
}


@media(max-width:1100px){

.si-grid-4 {

grid-template-columns:repeat(2,minmax(0,1fr));

}

.si-grid-3 {

grid-template-columns:repeat(2,minmax(0,1fr));

}

}


@media(max-width:640px){

.si-grid-4,
.si-grid-3 {

grid-template-columns:1fr;

}

}

</style>



<div class="si-wrapper">


<x-filament::section>

<x-slot name="heading">
🧠 Survey Intelligence
</x-slot>


<x-slot name="description">
Central intelligence dashboard for survey responses, voter feedback and campaign research.
</x-slot>


<label style="
display:block;
font-size:13px;
font-weight:600;
margin-bottom:8px;
">

Filter by Survey

</label>


<select
wire:model.live="surveyId"
class="si-select"
>


<option value="">
All Surveys
</option>


@foreach($surveys as $survey)

<option value="{{ $survey->id }}">
{{ $survey->name }}
</option>

@endforeach


</select>


</x-filament::section>



<div class="si-grid si-grid-4">


<div class="si-card">

<div class="si-icon">📋</div>

<div class="si-label">
Total Surveys
</div>

<div class="si-number">
{{ number_format($overview['total_surveys'] ?? 0) }}
</div>

<div class="si-subtitle">
Survey definitions
</div>

</div>



<div class="si-card">

<div class="si-icon">🟢</div>

<div class="si-label">
Active Surveys
</div>

<div class="si-number">
{{ number_format($overview['active_surveys'] ?? 0) }}
</div>

<div class="si-subtitle">
Currently active
</div>

</div>



<div class="si-card">

<div class="si-icon">📝</div>

<div class="si-label">
Total Responses
</div>

<div class="si-number">
{{ number_format($overview['total_responses'] ?? 0) }}
</div>

<div class="si-subtitle">
Submitted responses
</div>

</div>



<div class="si-card">

<div class="si-icon">📈</div>

<div class="si-label">
Response Coverage
</div>

<div class="si-number">
{{ number_format($overview['response_coverage'] ?? 0,1) }}%
</div>

<div class="si-subtitle">
Responses vs voters
</div>

</div>


</div>



<div class="si-grid si-grid-3">


<div class="si-card">

<div class="si-icon">📅</div>

<div class="si-label">
Today
</div>

<div class="si-number">
{{ number_format($overview['today_responses'] ?? 0) }}
</div>

<div class="si-subtitle">
Responses today
</div>

</div>



<div class="si-card">

<div class="si-icon">📊</div>

<div class="si-label">
This Week
</div>

<div class="si-number">
{{ number_format($overview['week_responses'] ?? 0) }}
</div>

<div class="si-subtitle">
Responses this week
</div>

</div>



<div class="si-card">

<div class="si-icon">📆</div>

<div class="si-label">
This Month
</div>

<div class="si-number">
{{ number_format($overview['month_responses'] ?? 0) }}
</div>

<div class="si-subtitle">
Responses this month
</div>

</div>


</div>
{{-- ============================================================
     SURVEY PERFORMANCE
============================================================= --}}

<div class="si-section">

    <div class="si-section-header">

        <div class="si-section-title">
            📋 Survey Performance
        </div>

        <div class="si-section-description">
            Survey-wise response activity and status.
        </div>

    </div>


    <div class="si-section-body">


        <div class="si-table-wrapper">


            <table class="si-table">


                <thead>

                    <tr>

                        <th>
                            Survey
                        </th>

                        <th>
                            Code
                        </th>

                        <th>
                            Type
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Questions
                        </th>

                        <th>
                            Responses
                        </th>

                    </tr>

                </thead>



                <tbody>


                @forelse($surveys as $survey)


                    <tr>


                        <td>

                            <strong>
                                {{ $survey->name }}
                            </strong>

                        </td>


                        <td>
                            {{ $survey->code }}
                        </td>


                        <td>
                            {{ $survey->type }}
                        </td>


                        <td>

                            <span class="si-badge">
                                {{ $survey->status }}
                            </span>

                        </td>


                        <td>
                            {{ number_format($survey->questions_count ?? 0) }}
                        </td>


                        <td>

                            <strong>
                                {{ number_format($survey->responses_count ?? 0) }}
                            </strong>

                        </td>


                    </tr>


                @empty


                    <tr>

                        <td colspan="6">

                            <div class="si-empty">
                                No surveys found.
                            </div>

                        </td>

                    </tr>


                @endforelse


                </tbody>


            </table>


        </div>


    </div>


</div>



{{-- ============================================================
     QUESTION INTELLIGENCE
============================================================= --}}


<div class="si-section">


<div class="si-section-header">


<div class="si-section-title">
🧩 Question Intelligence
</div>


<div class="si-section-description">
Question-wise answer distribution.
</div>


</div>



<div class="si-section-body">


@forelse($questions as $question)


<div class="si-question">


<div class="si-question-title">

{{ $question['question'] ?? '-' }}

</div>



<div class="si-question-meta">


{{ $question['survey_name'] ?? '-' }}

·

{{ ucfirst($question['type'] ?? '') }}

·

{{ number_format($question['answers_count'] ?? 0) }}

answers


</div>




@forelse(($question['distribution'] ?? []) as $answer => $stats)



<div class="si-answer-row">


<div class="si-answer-name">

{{ $answer ?: 'No Answer' }}

</div>



<div class="si-bar">


<div 
class="si-bar-fill"
style="
width:{{ min(100,$stats['percentage'] ?? 0) }}%;
">
</div>


</div>




<div class="si-answer-count">


{{ number_format($stats['count'] ?? 0) }}


<span class="si-muted">

({{ $stats['percentage'] ?? 0 }}%)

</span>


</div>


</div>


@empty


<div class="si-empty">
No answers recorded yet.
</div>


@endforelse



</div>


@empty


<div class="si-empty">
No questions found.
</div>


@endforelse


</div>


</div>





{{-- ============================================================
     GEOGRAPHIC INTELLIGENCE
============================================================= --}}


<div class="si-grid si-grid-3">


@foreach([
['🏛️','Constituencies',$geography['constituencies'] ?? []],
['🏘️','Villages',$geography['villages'] ?? []],
['🗳️','Booths',$geography['booths'] ?? []],
] as $geo)



<div class="si-section">


<div class="si-section-header">


<div class="si-section-title">

{{ $geo[0] }} {{ $geo[1] }}

</div>


<div class="si-section-description">

Survey response coverage

</div>


</div>




<div class="si-section-body">



@forelse($geo[2] as $item)



<div style="
display:flex;
justify-content:space-between;
padding:10px 0;
border-bottom:1px solid rgb(229 231 235);
">


<span>

{{ $item['name'] ?? '-' }}

</span>



<strong>

{{ number_format($item['responses'] ?? 0) }}

</strong>



</div>



@empty


<div class="si-empty">

No response data.

</div>


@endforelse



</div>


</div>


@endforeach



</div>





{{-- ============================================================
     RECENT RESPONSES
============================================================= --}}



<div class="si-section">


<div class="si-section-header">


<div class="si-section-title">

🕒 Recent Survey Responses

</div>


<div class="si-section-description">

Latest submitted survey records.

</div>


</div>




<div class="si-section-body">


<div class="si-table-wrapper">


<table class="si-table">


<thead>


<tr>

<th>
Survey
</th>

<th>
Voter
</th>

<th>
House
</th>

<th>
Submitted
</th>

</tr>


</thead>




<tbody>



@forelse($recentResponses as $response)



<tr>


<td>

{{ $response->survey?->name ?? '-' }}

</td>



<td>

{{ $response->voter?->name ?? '-' }}

</td>



<td>

{{ $response->house?->house_no ?? '-' }}

</td>



<td>


{{ 
$response->submitted_at?->format('d M Y, h:i A')
??
$response->created_at?->format('d M Y, h:i A')
??
'-'
}}


</td>



</tr>



@empty



<tr>


<td colspan="4">


<div class="si-empty">

No survey responses found.

</div>


</td>


</tr>



@endforelse



</tbody>


</table>


</div>


</div>


</div>




</div>


</x-filament-panels::page>
