<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Label</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .box { margin-bottom: 20px; padding: 10px; border: 1px dashed #333; page-break-inside: avoid; }
        .label-code { font-weight: bold; font-size: 16px; margin-top: 5px; }
    </style>
</head>
<body>
    <h2>Delivery Label</h2>
    <p><strong>Traceability #:</strong> {{ $note->traceability_number }}</p>
    <p><strong>Destination:</strong> {{ $note->distributor->name }}</p>
    <p><strong>Date:</strong> {{ $note->created_at->format('Y-m-d') }}</p>

    @foreach($note->boxes as $box)
        <div class="box">
            @if($note->user->business_name)
                <p><strong> {{ $note->user->business_name }} </strong></p>
            @endif
            @if($note->user->location)
                <p><strong>Location:</strong> {{ $note->user->location }}</p>
            @endif
            <p><strong>Crop:</strong> {{ $box->crop }}</p>
            <p><strong>Quantity:</strong> {{ $box->quantity }} {{ $box->unit_type ?? 'unit' }}</p>
            {{-- Grower Details --}}
            <p><strong>Grower:</strong> {{ $note->user->name }}</p>
            
            <p class="label-code">Label Code: {{ $box->label_code }}</p>

            {{-- QR Code (embedded as SVG) --}}
            @php
                $qr = base64_encode(
                QrCode::format('png')
                      ->size(100)
                      ->generate(route('trace.show', $box->label_code))
            );
            @endphp
            <img src="data:image/png;base64,{{ $qr }}" alt="QR Code">
        </div>
    @endforeach
</body>
</html>