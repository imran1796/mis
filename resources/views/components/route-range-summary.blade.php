@php
    $routeMap = [
        1 => 'Singapore',
        2 => 'Colombo',
        3 => 'Kolkata',
    ];

    $selectedRoutes = collect(request('route_id'))
        ->map(fn($id) => $routeMap[$id] ?? '')
        ->filter()
        ->join(', ');

    $from = request('from_date')
        ? \Carbon\Carbon::parse(request('from_date'))->format('M-y')
        : null;
    $to = request('to_date')
        ? \Carbon\Carbon::parse(request('to_date'))->format('M-y')
        : null;

    $range = match (true) {
        $from && $to && $from === $to => $from,
        $from && $to => "$from to $to",
        $from && !$to=> "$from to Current",
        !$from && $to => "upto $to",
        default => null,
    };
@endphp

{{ $selectedRoutes ?$selectedRoutes: 'All Routes' }}{{ $range ? ' | ' . $range : '' }}
