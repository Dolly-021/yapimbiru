@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Database Report</h1>
        @if(!$report)
            <p>No report found. Run <code>php artisan generate:db-report</code> first.</p>
        @else
            <p>Report file: <code>{{ $path }}</code></p>
            <h2>Summary</h2>
            <ul>
                <li>Tables: {{ count($report['tables'] ?? []) }}</li>
                <li>Views: {{ count($report['views'] ?? []) }}</li>
                <li>Triggers: {{ count($report['triggers'] ?? []) }}</li>
                <li>Constraints: {{ count($report['constraints'] ?? []) }}</li>
            </ul>

            <h3>Tables</h3>
            <ul>
                @foreach($report['tables'] as $t)
                    <li>{{ $t['TABLE_NAME'] }} ({{ $t['TABLE_TYPE'] }})</li>
                @endforeach
            </ul>

            <h3>Views</h3>
            <ul>
                @foreach($report['views'] as $v)
                    <li>{{ $v['VIEW_NAME'] }}</li>
                @endforeach
            </ul>

            <h3>Triggers</h3>
            <ul>
                @foreach($report['triggers'] as $tr)
                    <li>{{ $tr['TRIGGER_NAME'] }} on {{ $tr['EVENT_OBJECT_TABLE'] }} ({{ $tr['EVENT_MANIPULATION'] }})</li>
                @endforeach
            </ul>

        @endif
    </div>
@endsection
