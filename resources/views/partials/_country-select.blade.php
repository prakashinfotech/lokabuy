@php
$fieldName   = $fieldName   ?? 'location_country';
$selected    = old($fieldName, $currentValue ?? 'India');
@endphp
<select name="{{ $fieldName }}"
        class="form-select @error($fieldName) is-invalid @enderror"
        required>
    <option value="" disabled>Select country</option>
    @foreach($countries as $country)
        <option value="{{ $country }}" {{ $selected === $country ? 'selected' : '' }}>
            {{ $country }}
        </option>
    @endforeach
</select>
@error($fieldName)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
