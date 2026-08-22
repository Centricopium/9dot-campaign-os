<x-filament-panels::page>
    @php($result = $this->result)

    <style>
        .rc-wrap{display:flex;flex-direction:column;gap:18px}.rc-hero,.rc-panel{border:1px solid #e5e7eb;border-radius:18px;background:#fff;box-shadow:0 5px 20px rgba(15,23,42,.05)}.rc-hero{padding:24px;background:linear-gradient(135deg,#fff,#f5f3ff)}.rc-title{font-size:26px;font-weight:850;color:#111827}.rc-subtitle,.rc-description{margin-top:6px;color:#6b7280;font-size:13px}.rc-panel{overflow:hidden}.rc-panel-header{padding:17px 20px;border-bottom:1px solid #e5e7eb}.rc-panel-title{font-weight:800;color:#111827}.rc-panel-body{padding:18px}.rc-filters{display:grid;grid-template-columns:2fr repeat(5,1fr);gap:12px}.rc-field label{display:block;margin-bottom:7px;font-size:12px;font-weight:700;color:#4b5563}.rc-select{width:100%;min-height:43px;border:1px solid #d1d5db;border-radius:10px;padding:9px 11px;background:#fff;color:#111827}.rc-actions{display:flex;flex-wrap:wrap;gap:9px;margin-top:16px}.rc-button{border:0;border-radius:10px;padding:10px 15px;font-size:12px;font-weight:800;cursor:pointer}.rc-primary{color:#fff;background:linear-gradient(135deg,#7c3aed,#4c1d95)}.rc-secondary{color:#4c1d95;background:#ede9fe}.rc-danger{color:#b91c1c;background:#fee2e2}.rc-meta{display:flex;justify-content:space-between;gap:14px;align-items:center}.rc-count{padding:6px 10px;border-radius:999px;background:#ede9fe;color:#6d28d9;font-size:11px;font-weight:800}.rc-table-wrap{overflow:auto}.rc-table{width:100%;border-collapse:collapse;min-width:750px}.rc-table th,.rc-table td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:12px;white-space:nowrap}.rc-table th{background:#f8fafc;color:#6b7280;font-weight:800}.rc-table td{color:#1f2937}.rc-empty{padding:45px;text-align:center;color:#9ca3af}
        html.dark .rc-hero{border-color:rgba(139,92,246,.35);background:linear-gradient(135deg,#08070d,#151022 55%,#291044);box-shadow:0 18px 42px rgba(0,0,0,.34)}html.dark .rc-panel{border-color:rgba(139,92,246,.25);background:linear-gradient(145deg,#09080e,#100c19);box-shadow:0 15px 38px rgba(0,0,0,.28)}html.dark .rc-title,html.dark .rc-panel-title,html.dark .rc-table td{color:#f8fafc}html.dark .rc-subtitle,html.dark .rc-description,html.dark .rc-field label{color:#aaa4b8}html.dark .rc-panel-header{border-color:rgba(139,92,246,.2);background:linear-gradient(90deg,#0e0b14,#1c102b)}html.dark .rc-select{color:#f8fafc;border-color:rgba(139,92,246,.34);background:#100d17;color-scheme:dark}html.dark .rc-select:focus{outline:none;border-color:#a78bfa;box-shadow:0 0 0 3px rgba(139,92,246,.2)}html.dark .rc-secondary{color:#ddd6fe;background:rgba(124,58,237,.18)}html.dark .rc-danger{color:#fca5a5;background:rgba(153,27,27,.22)}html.dark .rc-count{color:#ddd6fe;background:rgba(124,58,237,.18)}html.dark .rc-table th{color:#c4b5fd;background:#151020}html.dark .rc-table th,html.dark .rc-table td{border-color:rgba(148,163,184,.12)}html.dark .rc-table tbody tr:hover{background:rgba(124,58,237,.11)}
        @media(max-width:1150px){.rc-filters{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.rc-filters{grid-template-columns:1fr}.rc-meta{align-items:flex-start;flex-direction:column}.rc-actions .rc-button{flex:1}}
    </style>

    <div class="rc-wrap">
        <div class="rc-hero">
            <div class="rc-title">📑 Campaign Reports Centre</div>
            <div class="rc-subtitle">21 operational, voter, survey and leadership reports from one secure workspace.</div>
        </div>

        <div class="rc-panel">
            <div class="rc-panel-header"><div class="rc-panel-title">Report & Filters</div></div>
            <div class="rc-panel-body">
                <div class="rc-filters">
                    <div class="rc-field"><label>Report</label><select class="rc-select" wire:model.live="report">@foreach($this->catalog as $key => $item)<option value="{{ $key }}">{{ $item[0] }}</option>@endforeach</select></div>
                    <div class="rc-field"><label>Constituency</label><select class="rc-select" wire:model.live="constituencyId"><option value="">All Constituencies</option>@foreach($this->constituencies as $item)<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach</select></div>
                    <div class="rc-field"><label>Taluka</label><select class="rc-select" wire:model.live="taluka"><option value="">All Talukas</option>@foreach($this->talukas as $item)<option value="{{ $item }}">{{ $item }}</option>@endforeach</select></div>
                    <div class="rc-field"><label>Village</label><select class="rc-select" wire:model.live="villageId"><option value="">All Villages</option>@foreach($this->villages as $item)<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach</select></div>
                    <div class="rc-field"><label>Booth</label><select class="rc-select" wire:model.live="boothId"><option value="">All Booths</option>@foreach($this->booths as $item)<option value="{{ $item->id }}">{{ $item->booth_no }} — {{ $item->booth_name }}</option>@endforeach</select></div>
                    <div class="rc-field"><label>Support</label><select class="rc-select" wire:model.live="supportLevel"><option value="">All Support Levels</option>@foreach(['Strong Support','Moderate Support','Leaning Support','Neutral','Undecided','Leaning Opposition','Moderate Opposition','Strong Opposition'] as $level)<option value="{{ $level }}">{{ $level }}</option>@endforeach</select></div>
                </div>
                <div class="rc-actions">
                    <button class="rc-button rc-primary" wire:click="exportPdf">📄 Download PDF</button>
                    <button class="rc-button rc-secondary" wire:click="exportCsv">📊 Download CSV</button>
                    <button class="rc-button rc-danger" wire:click="resetFilters">Reset Filters</button>
                </div>
            </div>
        </div>

        <div class="rc-panel">
            <div class="rc-panel-header rc-meta"><div><div class="rc-panel-title">{{ $result['title'] }}</div><div class="rc-description">{{ $result['description'] }}</div></div><div class="rc-count">Preview: {{ number_format($result['row_count']) }} rows</div></div>
            <div class="rc-table-wrap">
                @if($result['rows'])
                    <table class="rc-table"><thead><tr>@foreach($result['headers'] as $header)<th>{{ $header }}</th>@endforeach</tr></thead><tbody>@foreach($result['rows'] as $row)<tr>@foreach($row as $value)<td>{{ filled($value) ? $value : '—' }}</td>@endforeach</tr>@endforeach</tbody></table>
                @else<div class="rc-empty">No matching data found for the selected filters.</div>@endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
