@extends('layouts.app')

@section('title', 'Post Your Ad')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h4 class="fw-bold mb-4" style="color:var(--lokabuy-primary)">Post Your Ad</h4>


            <form method="POST" action="{{ route('listings.store') }}"
                  enctype="multipart/form-data"
                  x-data="listingForm()" @submit.prevent="submitForm">
                @csrf

                {{-- Category & Subcategory --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Category</h6>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror"
                                    x-model="categoryId" @change="onCategoryChange" required>
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                            data-type="{{ $cat->slug }}"
                                            {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" x-show="subcategories.length > 0" x-cloak>
                            <label class="form-label fw-semibold small">Subcategory <span class="text-danger">*</span></label>
                            <select name="subcategory_id"
                                    class="form-select @error('subcategory_id') is-invalid @enderror"
                                    :required="subcategories.length > 0">
                                <option value="">Select subcategory</option>
                                <template x-for="sub in subcategories" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name"></option>
                                </template>
                            </select>
                            @error('subcategory_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" x-show="isRealEstate" x-cloak>
                            <label class="form-label fw-semibold small">Type <span class="text-danger">*</span></label>
                            <select name="listing_type" class="form-select @error('listing_type') is-invalid @enderror">
                                <option value="">Select type</option>
                                <option value="buy"  {{ old('listing_type') == 'buy'  ? 'selected' : '' }}>For Sale</option>
                                <option value="rent" {{ old('listing_type') == 'rent' ? 'selected' : '' }}>For Rent</option>
                            </select>
                            @error('listing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Ad Details --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Ad Details</h6>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required maxlength="100"
                                   placeholder="e.g. iPhone 14 Pro Max 256GB">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
                            <textarea name="description" rows="5"
                                      class="form-control @error('description') is-invalid @enderror"
                                      required placeholder="Describe what you're selling...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Price (₹) <span class="text-danger">*</span></label>
                            <input type="text" inputmode="decimal" name="price"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price') }}" required placeholder="0"
                                   autocomplete="off">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Location --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Location</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">City <span class="text-danger">*</span></label>
                                <input type="text" name="location_city"
                                       class="form-control @error('location_city') is-invalid @enderror"
                                       value="{{ old('location_city') }}" required placeholder="e.g. Mumbai">
                                @error('location_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">State <span class="text-danger">*</span></label>
                                <input type="text" name="location_state"
                                       class="form-control @error('location_state') is-invalid @enderror"
                                       value="{{ old('location_state') }}" required placeholder="e.g. Maharashtra">
                                @error('location_state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Country <span class="text-danger">*</span></label>
                                @include('partials._country-select', ['currentValue' => 'India'])
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Photos --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4" x-data="imagePicker()">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <h6 class="fw-bold mb-0">Photos</h6>
                            <span class="small text-muted" x-text="previews.length + ' / 10'"></span>
                        </div>
                        <p class="text-muted small mb-3">Up to 10 photos (JPEG, PNG, WebP - Max 5 MB each)</p>

                        <label class="d-block border border-2 border-dashed rounded-3 text-center py-4 px-3"
                               :class="previews.length >= 10 ? 'opacity-50' : ''"
                               :style="previews.length >= 10 ? 'cursor:not-allowed;border-color:var(--lokabuy-border)!important' : 'cursor:pointer;border-color:var(--lokabuy-border)!important'"
                               for="imageInput">
                            <i class="bi bi-camera fs-2 d-block mb-1" style="color:var(--lokabuy-primary)"></i>
                            <span class="small fw-semibold" style="color:var(--lokabuy-primary)" x-text="previews.length >= 10 ? 'Maximum photos reached' : 'Click to add photos'"></span>
                            <input id="imageInput" type="file" name="images[]"
                                   multiple accept="image/jpeg,image/jpg,image/png,image/webp"
                                   class="d-none" @change="preview($event)"
                                   :disabled="previews.length >= 10">
                        </label>

                        <div class="d-flex flex-wrap gap-2 mt-3" x-show="previews.length > 0" x-cloak>
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="position-relative">
                                    <img :src="src" class="rounded" width="90" height="72"
                                         style="object-fit:cover">
                                    <button type="button"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 lh-1"
                                            style="width:18px;height:18px;font-size:.6rem"
                                            @click="remove(i)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        @php
                            $imageErrors = collect($errors->messages())
                                ->filter(fn ($msgs, $key) => str_starts_with($key, 'images'))
                                ->flatten();
                        @endphp
                        @if($imageErrors->isNotEmpty())
                            <div class="alert alert-danger small mt-3 mb-0 py-2 px-3">
                                @foreach($imageErrors as $msg)
                                    <div>{{ $msg }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn fw-bold text-white px-5 py-2"
                        style="background:var(--lokabuy-primary)">
                    Post Ad
                </button>

            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const allSubcategories = @json($categories->mapWithKeys(fn($c) => [$c->id => $c->subcategories->map(fn($s) => ['id' => $s->id, 'name' => $s->name])]));
const realEstateSlug = 'real-estate';

function listingForm() {
    return {
        categoryId: '{{ old("category_id", "") }}',
        subcategories: [],
        isRealEstate: false,

        onCategoryChange(e) {
            const id = e.target.value;
            const slug = e.target.options[e.target.selectedIndex]?.dataset?.type ?? '';
            this.subcategories = allSubcategories[id] ?? [];
            this.isRealEstate = slug === realEstateSlug;
        },

        submitForm(e) {
            e.target.submit();
        },
    };
}

function imagePicker() {
    return {
        previews: [],
        fileList: [],

        preview(event) {
            const selected = Array.from(event.target.files);
            const remaining = 10 - this.fileList.length;
            selected.slice(0, remaining).forEach(file => {
                this.fileList.push(file);
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
            event.target.value = '';
            this.syncInput();
        },

        remove(index) {
            this.previews.splice(index, 1);
            this.fileList.splice(index, 1);
            this.syncInput();
        },

        syncInput() {
            const input = document.getElementById('imageInput');
            const dt = new DataTransfer();
            this.fileList.forEach(f => dt.items.add(f));
            input.files = dt.files;
        },
    };
}
</script>
@endpush
