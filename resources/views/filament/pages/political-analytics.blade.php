<x-filament-panels::page>

@php

$data = $this->data;

$overview = $data['overview'];

$supportAnalysis = $data['supportAnalysis'];

$partyAnalysis = $data['partyAnalysis'];

$boothAnalysis = $data['boothAnalysis'];

$villageAnalysis = $data['villageAnalysis'];

@endphp


<style>

.pa-grid{
    display:grid;
    gap:18px;
}


.pa-grid-6{
    grid-template-columns:repeat(6,minmax(0,1fr));
}


.pa-card{

    border-radius:18px;
    padding:22px;

    background:rgba(255,255,255,.75);

    border:1px solid rgba(0,0,0,.08);

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);

    transition:.25s;

}


.dark .pa-card{

    background:linear-gradient(145deg,#1a1721,#15121b);

    border-color:rgba(168,85,247,.16);

    box-shadow:0 16px 40px -30px rgba(168,85,247,.48);

}



.pa-card:hover{

    transform:translateY(-3px);

}

.dark .pa-card:hover{
    border-color:rgba(168,85,247,.44);
    background:linear-gradient(145deg,#211a2b,#18131f);
    box-shadow:0 20px 38px -25px rgba(168,85,247,.58);
}



.pa-icon{

    font-size:30px;

}



.pa-label{

    margin-top:8px;

    font-size:13px;

    color:#6b7280;

}



.dark .pa-label{

    color:#9ca3af;

}



.pa-number{

    margin-top:8px;

    font-size:34px;

    font-weight:800;

    color:#111827;

}

.dark .pa-number{
    color:#f7f4fb;
}



.pa-section{

    margin-top:22px;

    border-radius:18px;

    overflow:hidden;

    background:rgba(255,255,255,.75);

    border:1px solid rgba(0,0,0,.08);

}



.dark .pa-section{

    background:#121018;

    border-color:rgba(168,85,247,.16);

    box-shadow:0 18px 44px -32px rgba(168,85,247,.44);

}



.pa-header{

    padding:18px 22px;

    font-size:18px;

    font-weight:700;

    border-bottom:1px solid rgba(0,0,0,.08);

}



.dark .pa-header{

    border-color:rgba(168,85,247,.14);

    background:linear-gradient(110deg,#1a1622,#131019 65%);

    color:#f7f4fb;

}



.pa-body{

    padding:20px;

}

.dark .pa-body{
    background:#121018;
    color:#ded8e7;
}



.pa-row{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:14px;

    border-radius:12px;

    margin-bottom:10px;

    background:rgba(128,128,128,.08);

}

.dark .pa-row{
    border:1px solid rgba(168,85,247,.12);
    background:linear-gradient(145deg,#1a1721,#15121b);
    color:#f7f4fb;
}

.dark .pa-row:hover{
    border-color:rgba(168,85,247,.34);
    background:rgba(124,58,237,.10);
}



.pa-badge{

    display:inline-block;

    padding:6px 12px;

    border-radius:999px;

    font-size:12px;

    margin-left:5px;

    background:rgba(128,128,128,.18);

}

.dark .pa-badge{
    border:1px solid rgba(168,85,247,.18);
    background:#211a29;
    color:#d8d1e1;
}

.dark .pa-row small{
    color:#a8a1b3;
}



.pa-empty{

    text-align:center;

    padding:30px;

    color:#9ca3af;

}




@media(max-width:1200px){

.pa-grid-6{

grid-template-columns:repeat(3,1fr);

}

}



@media(max-width:700px){

.pa-grid-6{

grid-template-columns:1fr;

}


.pa-row{

flex-direction:column;

align-items:flex-start;

gap:10px;

}

}


</style>





{{-- OVERVIEW --}}


<div class="pa-grid pa-grid-6">


@foreach([

['👥','Total Voters',$overview['total_voters']],

['🟢','Supporters',$overview['supporters']],

['❓','Undecided',$overview['undecided']],

['🔴','Opposition',$overview['opposition']],

['🙋','Volunteers',$overview['volunteers']],

['⭐','Influencers',$overview['influencers']],


] as $card)


<div class="pa-card">


<div class="pa-icon">
{{ $card[0] }}
</div>


<div class="pa-label">
{{ $card[1] }}
</div>


<div class="pa-number">
{{ number_format($card[2]) }}
</div>


</div>


@endforeach


</div>





{{-- SUPPORT --}}


<div class="pa-section">


<div class="pa-header">
📊 Support Level Analysis
</div>


<div class="pa-body">


@forelse($supportAnalysis as $item)


<div class="pa-row">


<div>

{{ $item->support_level }}

</div>


<div>

<span class="pa-badge">
{{ $item->total }}
</span>


</div>


</div>


@empty

<div class="pa-empty">
No Support Data Found
</div>

@endforelse


</div>


</div>







{{-- PARTY --}}


<div class="pa-section">


<div class="pa-header">
🏛️ Party Wise Analysis
</div>


<div class="pa-body">


@forelse($partyAnalysis as $party)


<div class="pa-row">


<div>

<strong>
{{ $party['name'] }}
</strong>


<br>


<small>

Total :
{{ $party['total'] }}

</small>


</div>



<div>


<span class="pa-badge">

🟢 {{ $party['strong_support'] }}

</span>


<span class="pa-badge">

⚪ {{ $party['neutral'] }}

</span>


</div>


</div>


@empty


<div class="pa-empty">
No Party Data
</div>


@endforelse


</div>


</div>







{{-- BOOTH --}}


<div class="pa-section">


<div class="pa-header">
🗳️ Booth Intelligence
</div>


<div class="pa-body">


@forelse($boothAnalysis->take(10) as $booth)


<div class="pa-row">


<div>

<strong>
{{ $booth['booth'] }}
</strong>


<br>


<small>

Total Voters :
{{ $booth['total_voters'] }}

</small>


</div>



<div>


<span class="pa-badge">

🟢 {{ $booth['supporters'] }}

</span>


<span class="pa-badge">

❓ {{ $booth['undecided'] }}

</span>


<span class="pa-badge">

🔴 {{ $booth['opposition'] ?? 0 }}

</span>


</div>



</div>


@empty


<div class="pa-empty">

No Booth Data

</div>


@endforelse


</div>


</div>








{{-- VILLAGE --}}


<div class="pa-section">


<div class="pa-header">
🏘️ Village Intelligence
</div>


<div class="pa-body">


@forelse($villageAnalysis->take(10) as $village)


<div class="pa-row">


<div>


<strong>

{{ $village['village'] }}

</strong>


<br>


<small>

{{ $village['taluka'] }},
{{ $village['district'] }}

</small>


<br>


<small>

Total :
{{ $village['total_voters'] }}

</small>


</div>




<div>


<span class="pa-badge">

🟢 {{ $village['supporters'] }}

</span>


<span class="pa-badge">

⚪ {{ $village['neutral'] }}

</span>


<span class="pa-badge">

❓ {{ $village['undecided'] }}

</span>


<span class="pa-badge">

🔴 {{ $village['opposition'] }}

</span>


</div>


</div>


@empty


<div class="pa-empty">

No Village Data

</div>


@endforelse


</div>


</div>




</x-filament-panels::page>
