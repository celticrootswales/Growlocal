<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Note #{{ $note->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h1 { color: #28a745; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Delivery Note #{{ $note->id }}</h1>

    {{-- Grower Details --}}
    <p><strong>Grower:</strong> {{ $note->user->name }}</p>
    @if($note->user->business_name)
        <p><strong>Business:</strong> {{ $note->user->business_name }}</p>
    @endif
    @if($note->user->location)
        <p><strong>Location:</strong> {{ $note->user->location }}</p>
    @endif
    @if($note->user->phone)
        <p><strong>Contact:</strong> {{ $note->user->phone }}</p>
    @endif

    {{-- Delivery Info --}}
    <p><strong>Destination:</strong> {{ $note->distributor->name }}</p>
    <p><strong>Traceability #:</strong> {{ $note->traceability_number }}</p>
    <p><strong>Date:</strong> {{ $note->created_at->format('Y-m-d') }}</p>

    <h3>Boxes Supplied</h3>
    <table>
        <thead>
            <tr>
                <th>Crop</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price/Unit (£)</th>
                <th>Total (£)</th>
                <th>Label Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($note->boxes as $box)
                <tr>
                    <td>{{ $box->crop }}</td>
                    <td>{{ $box->quantity }}</td>
                    <td>{{ $box->unit_type ?? 'unit' }}</td>
                    <td class="text-right">£{{ number_format($box->price_per_unit ?? 0, 2) }}</td>
                    <td class="text-right">£{{ number_format($box->total_price ?? 0, 2) }}</td>
                    <td>{{ $box->label_code }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Value:</th>
                <th class="text-right">£{{ number_format($totalValue, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>