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
    <p><strong>Destination:</strong> {{ $note->destination }}</p>
    <p><strong>Date:</strong> {{ $note->created_at->format('Y-m-d') }}</p>

    @foreach($note->boxes as $box)
        <div class="box">
            <p><strong>Crop:</strong> {{ $box->crop }}</p>
            <p><strong>Quantity:</strong> {{ $box->quantity }}</p>
            <p class="label-code">Label Code: {{ $box->label_code }}</p>

            {{-- QR Code (embedded as SVG) --}}
            <div>
                {!! QrCode::format('svg')->size(150)->margin(1)->generate($box->label_code) !!}
            </div>
        </div>
    @endforeach
</body>
</html>