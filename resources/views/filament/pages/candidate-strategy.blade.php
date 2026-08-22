<x-filament-panels::page>
    @php
        $candidates = $this->candidates;
        $finalCandidate = $candidates->firstWhere('is_final', true);
        $topCandidate = $candidates->whereNotNull('average_score')->sortByDesc('average_score')->first();
    @endphp

    <style>
        .cs-wrap{display:flex;flex-direction:column;gap:18px}.cs-hero,.cs-panel{border:1px solid rgba(124,58,237,.16);border-radius:20px;background:rgba(255,255,255,.9);box-shadow:0 18px 48px -34px rgba(76,29,149,.48);overflow:hidden}.cs-hero{padding:24px;background:linear-gradient(125deg,#fff 0%,#f5f3ff 62%,#ede9fe 100%)}.cs-title{font-size:27px;font-weight:900;color:#111827;letter-spacing:-.03em}.cs-subtitle{margin-top:6px;color:#6b7280;font-size:13px}.cs-filters{display:grid;grid-template-columns:2fr 1.4fr 1fr auto;gap:12px;align-items:end;margin-top:20px}.cs-field label{display:block;margin-bottom:7px;font-size:12px;font-weight:800;color:#4b5563}.cs-select{width:100%;min-height:44px;border:1px solid rgba(124,58,237,.2);border-radius:12px;padding:9px 12px;background:#fff;color:#111827}.cs-reset{min-height:44px;border:0;border-radius:12px;padding:9px 16px;color:#5b21b6;background:#ede9fe;font-size:12px;font-weight:850;cursor:pointer}.cs-summary{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.cs-stat{padding:18px;border:1px solid rgba(124,58,237,.14);border-radius:16px;background:#fff}.cs-stat-label{font-size:11px;font-weight:850;color:#7c3aed;text-transform:uppercase;letter-spacing:.07em}.cs-stat-value{margin-top:7px;font-size:23px;font-weight:900;color:#111827}.cs-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.cs-card{position:relative;padding:20px;border:1px solid #e5e7eb;border-radius:18px;background:linear-gradient(150deg,#fff,#fafafa);box-shadow:0 14px 38px -30px rgba(15,23,42,.5)}.cs-card.final{border-color:#8b5cf6;background:linear-gradient(145deg,#fff,#f5f3ff);box-shadow:0 18px 44px -26px rgba(109,40,217,.55)}.cs-final-badge{position:absolute;right:14px;top:14px;padding:6px 9px;border-radius:999px;color:#fff;background:linear-gradient(135deg,#7c3aed,#111016);font-size:10px;font-weight:900}.cs-person{display:flex;gap:13px;align-items:center;padding-right:70px}.cs-avatar{width:52px;height:52px;border-radius:15px;object-fit:cover;background:linear-gradient(135deg,#7c3aed,#111016);display:grid;place-items:center;color:#fff;font-size:18px;font-weight:900}.cs-name{font-size:17px;font-weight:900;color:#111827}.cs-meta{margin-top:3px;font-size:11px;color:#6b7280}.cs-recommend{display:inline-flex;margin-top:14px;padding:6px 9px;border-radius:999px;color:#5b21b6;background:#ede9fe;font-size:10px;font-weight:850}.cs-scores{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:15px}.cs-score{padding:10px;border-radius:12px;background:#f8fafc}.cs-score span{display:block;color:#6b7280;font-size:9px;font-weight:800;text-transform:uppercase}.cs-score strong{display:block;margin-top:4px;color:#111827;font-size:16px}.cs-progress{height:5px;margin-top:7px;border-radius:999px;background:#e5e7eb;overflow:hidden}.cs-progress i{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,#7c3aed,#a855f7)}.cs-card-note{margin-top:14px;padding-top:13px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:11px;line-height:1.55}.cs-panel-head{padding:17px 20px;border-bottom:1px solid #e5e7eb;font-size:14px;font-weight:900;color:#111827}.cs-table-wrap{overflow:auto}.cs-table{width:100%;min-width:1000px;border-collapse:collapse}.cs-table th,.cs-table td{padding:12px 14px;border-bottom:1px solid #e5e7eb;text-align:left;white-space:nowrap;font-size:11px}.cs-table th{background:#f8fafc;color:#6b7280;font-weight:850}.cs-table td{color:#1f2937}.cs-empty{padding:55px 20px;text-align:center;color:#9ca3af}.cs-empty strong{display:block;margin-bottom:8px;color:#4b5563;font-size:16px}
        html.dark .cs-hero,html.dark .cs-panel{border-color:rgba(167,139,250,.25);background:linear-gradient(145deg,#09080e,#151020);box-shadow:0 18px 48px -30px rgba(0,0,0,.6)}html.dark .cs-hero{background:linear-gradient(125deg,#09080e,#171023 62%,#27113e)}html.dark .cs-title,html.dark .cs-stat-value,html.dark .cs-name,html.dark .cs-score strong,html.dark .cs-panel-head,html.dark .cs-table td{color:#f8fafc}html.dark .cs-subtitle,html.dark .cs-field label,html.dark .cs-meta,html.dark .cs-card-note{color:#aaa4b8}html.dark .cs-select{color:#f8fafc;border-color:rgba(167,139,250,.32);background:#100d17;color-scheme:dark}html.dark .cs-reset,html.dark .cs-recommend{color:#ddd6fe;background:rgba(124,58,237,.22)}html.dark .cs-stat,html.dark .cs-card{border-color:rgba(167,139,250,.2);background:linear-gradient(145deg,#0d0a12,#151020)}html.dark .cs-card.final{border-color:#8b5cf6;background:linear-gradient(145deg,#110b1b,#211037)}html.dark .cs-score,html.dark .cs-table th{background:#151020}html.dark .cs-panel-head,html.dark .cs-card-note,html.dark .cs-table th,html.dark .cs-table td{border-color:rgba(148,163,184,.13)}html.dark .cs-table th{color:#c4b5fd}
        @media(max-width:1100px){.cs-grid{grid-template-columns:repeat(2,1fr)}.cs-filters{grid-template-columns:repeat(2,1fr)}.cs-summary{grid-template-columns:1fr 1fr 1fr}}@media(max-width:680px){.cs-grid,.cs-filters,.cs-summary{grid-template-columns:1fr}}
    </style>

    <div class="cs-wrap">
        <section class="cs-hero">
            <div class="cs-title">🎯 Candidate Strategy</div>
            <div class="cs-subtitle">Select an Assembly Constituency to automatically load and compare all same-party candidate aspirants.</div>
            <div class="cs-filters">
                <div class="cs-field">
                    <label>Assembly Constituency</label>
                    <select class="cs-select" wire:model.live="constituencyId">
                        <option value="">Select Assembly</option>
                        @foreach($this->constituencies as $constituency)
                            <option value="{{ $constituency->id }}">{{ $constituency->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="cs-field">
                    <label>Party</label>
                    <select class="cs-select" wire:model.live="partyId" @disabled(!$constituencyId)>
                        <option value="">All Parties</option>
                        @foreach($this->parties as $party)
                            <option value="{{ $party->id }}">{{ $party->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="cs-field">
                    <label>Election Year</label>
                    <select class="cs-select" wire:model.live="electionYear" @disabled(!$constituencyId)>
                        <option value="">All Years</option>
                        @foreach($this->electionYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="cs-reset" wire:click="resetFilters">Reset</button>
            </div>
        </section>

        @if($constituencyId)
            <div class="cs-summary">
                <div class="cs-stat"><div class="cs-stat-label">Candidates Found</div><div class="cs-stat-value">{{ number_format($candidates->count()) }}</div></div>
                <div class="cs-stat"><div class="cs-stat-label">Top Assessment</div><div class="cs-stat-value">{{ $topCandidate?->name ?? 'Pending' }}</div></div>
                <div class="cs-stat"><div class="cs-stat-label">Final Candidate</div><div class="cs-stat-value">{{ $finalCandidate?->name ?? 'Not Selected' }}</div></div>
            </div>

            @if($candidates->isNotEmpty())
                <div class="cs-grid">
                    @foreach($candidates as $candidate)
                        @php
                            $strength = $candidate->average_score === null ? null : round((float) $candidate->average_score, 1);
                            $risk = $candidate->average_risk_score === null ? null : round((float) $candidate->average_risk_score, 1);
                            $confidence = $candidate->average_confidence_score === null ? null : round((float) $candidate->average_confidence_score, 1);
                        @endphp
                        <article class="cs-card {{ $candidate->is_final ? 'final' : '' }}">
                            @if($candidate->is_final)<div class="cs-final-badge">✓ FINAL</div>@endif
                            <div class="cs-person">
                                @if($candidate->photo)
                                    <img class="cs-avatar" src="{{ asset('storage/'.$candidate->photo) }}" alt="{{ $candidate->name }}">
                                @else
                                    <div class="cs-avatar">{{ strtoupper(mb_substr($candidate->name, 0, 2)) }}</div>
                                @endif
                                <div><div class="cs-name">{{ $candidate->name }}</div><div class="cs-meta">{{ $candidate->politicalParty?->short_name ?? 'Party not set' }} · {{ $candidate->election_name }} {{ $candidate->election_year }}</div></div>
                            </div>
                            <div class="cs-recommend">{{ $candidate->system_recommendation }}</div>
                            <div class="cs-scores">
                                @foreach([['Strength',$strength],['Risk',$risk],['Confidence',$confidence]] as [$label,$value])
                                    <div class="cs-score"><span>{{ $label }}</span><strong>{{ $value === null ? '—' : $value }}</strong><div class="cs-progress"><i style="width:{{ max(0,min(100,$value ?? 0)) }}%"></i></div></div>
                                @endforeach
                            </div>
                            <div class="cs-card-note">{{ $candidate->submitted_assessments_count }} submitted assessments · Status: {{ $candidate->status }}@if($candidate->current_position)<br>{{ $candidate->current_position }}@endif</div>
                        </article>
                    @endforeach
                </div>

                <section class="cs-panel">
                    <div class="cs-panel-head">Candidate Comparison</div>
                    <div class="cs-table-wrap"><table class="cs-table"><thead><tr><th>Rank</th><th>Candidate</th><th>Party</th><th>Strength</th><th>Risk</th><th>Confidence</th><th>Assessments</th><th>Recommendation</th><th>Status</th></tr></thead><tbody>
                        @foreach($candidates as $candidate)
                            <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $candidate->name }}</strong></td><td>{{ $candidate->politicalParty?->short_name ?? '—' }}</td><td>{{ $candidate->average_score === null ? 'Pending' : number_format($candidate->average_score,1) }}</td><td>{{ $candidate->average_risk_score === null ? 'Pending' : number_format($candidate->average_risk_score,1) }}</td><td>{{ $candidate->average_confidence_score === null ? 'Pending' : number_format($candidate->average_confidence_score,1).'%' }}</td><td>{{ $candidate->submitted_assessments_count }}</td><td>{{ $candidate->system_recommendation }}</td><td>{{ $candidate->status }}</td></tr>
                        @endforeach
                    </tbody></table></div>
                </section>
            @else
                <div class="cs-panel cs-empty"><strong>No candidates found</strong>Add candidate aspirants for this Assembly in Candidate Selection.</div>
            @endif
        @else
            <div class="cs-panel cs-empty"><strong>Select an Assembly Constituency</strong>The candidate list and assessment comparison will load automatically.</div>
        @endif
    </div>
</x-filament-panels::page>
