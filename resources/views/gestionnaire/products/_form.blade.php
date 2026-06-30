<div class="mb-3">
    <label class="form-label">Catégorie</label>
    <select name="category_id" class="form-select" required>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', optional($product ?? null)->category_id) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Nom</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', optional($product ?? null)->name) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="4" required>{{ old('description', optional($product ?? null)->description) }}</textarea>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Prix ($)</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', optional($product ?? null)->price) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="{{ old('stock', optional($product ?? null)->stock ?? 0) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Image</label>
        @if(!empty($product?->image))
            <div class="mb-2">
                <img src="{{ asset('storage/'.$product->image) }}" alt="Image actuelle" class="img-thumbnail" style="max-height:120px">
            </div>
        @endif
        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <div class="form-text">JPG, PNG ou WebP — max. 10 Mo.</div>
        @error('image')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
