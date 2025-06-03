@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Crop Offering</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.crop-offerings.update', $offering->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Crop Name</label>
                        <input type="text" id="crop-name" name="crop_name" class="form-control" value="{{ old('crop_name', $offering->crop_name) }}" required>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Emoji</label>
                        <input type="text" id="emoji" name="icon" class="form-control" maxlength="2" value="{{ old('icon', $offering->icon) }}" placeholder="🥕">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <select name="unit" class="form-select" required>
                            <option value="kg" {{ $offering->unit === 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="ea" {{ $offering->unit === 'ea' ? 'selected' : '' }}>Unit</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ old('year', $offering->year) }}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Price (£)</label>
                        <input type="number" step="0.01" name="default_price" class="form-control" value="{{ old('default_price', $offering->default_price) }}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Amount Needed</label>
                        <input type="number" step="0.01" name="amount_needed" class="form-control" value="{{ old('amount_needed', $offering->amount_needed) }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select">
                            <option value="">Select</option>
                            <option value="Autumn" {{ $offering->term === 'Autumn' ? 'selected' : '' }}>Autumn</option>
                            <option value="Spring" {{ $offering->term === 'Spring' ? 'selected' : '' }}>Spring</option>
                            <option value="Summer" {{ $offering->term === 'Summer' ? 'selected' : '' }}>Summer</option>
                            <option value="Food and Fun" {{ $offering->term === 'Food and Fun' ? 'selected' : '' }}>Food and Fun</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Assign to Distributors</label>
                        <select name="distributors[]" class="form-select" multiple>
                            @foreach($distributors as $distributor)
                                <option value="{{ $distributor->id }}"
                                    {{ $offering->distributors->contains($distributor->id) ? 'selected' : '' }}>
                                    {{ $distributor->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl or ⌘ to select multiple.</small>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary mt-3">Update Crop Offering</button>
                        <a href="{{ route('admin.crop-offerings.index') }}" class="btn btn-secondary mt-3">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Emoji Auto-Fill Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cropInput = document.getElementById('crop-name');
    const emojiInput = document.getElementById('emoji');

    const emojiMap = {
        carrot: '🥕',
        potato: '🥔',
        tomato: '🍅',
        cucumber: '🥒',
        broccoli: '🥦',
        lettuce: '🥬',
        onion: '🧅',
        corn: '🌽',
        pepper: '🫑',
        mushroom: '🍄',
        apple: '🍎',
        orange: '🍊',
        banana: '🍌',
        grape: '🍇',
        strawberry: '🍓',
        watermelon: '🍉',
        lemon: '🍋',
        garlic: '🧄',
        peas: '🫛',
        beans: '🫘',
        pumpkin: '🎃',
        radish: '🌶️'
    };

    cropInput.addEventListener('input', function () {
        const name = cropInput.value.trim().toLowerCase();
        if (!emojiInput.value.trim()) {
            for (let key in emojiMap) {
                if (name.includes(key)) {
                    emojiInput.value = emojiMap[key];
                    break;
                }
            }
        }
    });
});
</script>
@endsection