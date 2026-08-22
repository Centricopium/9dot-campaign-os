<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 22px 24px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #17111f; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        .header { padding: 14px 16px; border-radius: 10px; color: #fff; background: #4c1d95; }
        h1 { margin: 0; font-size: 20px; }
        .subtitle { margin-top: 5px; color: #ede9fe; }
        .filters { margin: 12px 0 15px; padding: 9px 12px; border: 1px solid #ddd6fe; background: #f5f3ff; }
        .filter { display: inline-block; margin-right: 24px; }
        .group { page-break-before: always; }
        .group.first { page-break-before: auto; }
        .group-title { margin: 0 0 10px; padding-bottom: 6px; border-bottom: 2px solid #7c3aed; color: #4c1d95; font-size: 16px; text-transform: capitalize; }
        .card { display: inline-block; width: 48.5%; min-height: 230px; margin: 0 1% 12px 0; padding: 12px; border: 1px solid #e5e7eb; border-radius: 9px; vertical-align: top; page-break-inside: avoid; }
        .card-title { margin-bottom: 3px; font-size: 12px; font-weight: bold; }
        .card-meta { margin-bottom: 11px; color: #7c7188; font-size: 8px; }
        .row { margin-bottom: 7px; }
        .label { display: inline-block; width: 34%; overflow: hidden; white-space: nowrap; color: #4b5563; vertical-align: middle; }
        .track { display: inline-block; width: 46%; height: 8px; border-radius: 5px; background: #ede9fe; vertical-align: middle; }
        .fill { height: 8px; border-radius: 5px; background: #7c3aed; }
        .value { display: inline-block; width: 17%; text-align: right; font-weight: bold; vertical-align: middle; }
        .empty { padding: 70px 0; color: #9ca3af; text-align: center; }
        .footer { position: fixed; right: 0; bottom: -12px; color: #9ca3af; font-size: 7px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>9Dot Campaign Analytics</h1>
        <div class="subtitle">Chart intelligence export · {{ $generatedAt->format('d M Y, h:i A') }}</div>
    </div>
    <div class="filters">
        @foreach($filters as $label => $value)
            <span class="filter"><strong>{{ $label }}:</strong> {{ $value }}</span>
        @endforeach
    </div>

    @foreach($charts as $group => $items)
        <section class="group {{ $loop->first ? 'first' : '' }}">
            <h2 class="group-title">{{ str_replace('_', ' ', $group) }}</h2>
            @foreach($items as $chart)
                @php
                    $rows = collect($chart['data'])->flatMap(function ($item) {
                        if (isset($item['series'])) {
                            return collect($item['series'])->map(fn ($series) => [
                                'label' => $item['label'].' · '.$series['name'],
                                'value' => $series['value'],
                            ]);
                        }

                        return [[
                            'label' => $item['label'] ?? 'Value',
                            'value' => $item['value'] ?? 0,
                        ]];
                    })->take(30);
                    $max = max(1, (float) ($rows->max('value') ?? 1));
                @endphp
                <div class="card">
                    <div class="card-title">{{ $chart['title'] }}</div>
                    <div class="card-meta">{{ ucfirst($chart['type']) }} chart · {{ $rows->count() }} plotted values</div>
                    @forelse($rows as $row)
                        @php($width = min(100, ((float) $row['value'] * 100) / $max))
                        <div class="row">
                            <span class="label">{{ $row['label'] }}</span>
                            <span class="track"><span class="fill" style="width:{{ $width }}%"></span></span>
                            <span class="value">{{ number_format((float) $row['value'], 1) }}{{ $chart['suffix'] }}</span>
                        </div>
                    @empty
                        <div class="empty">No matching chart data available.</div>
                    @endforelse
                </div>
            @endforeach
        </section>
    @endforeach
    <div class="footer">Confidential campaign analytics · 9Dot Campaign OS</div>
</body>
</html>
