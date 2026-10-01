<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama produk <span class="text-danger">*</span></label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required maxlength="255">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="category" class="form-label">Kategori</label>
        <input id="category" name="category" type="text" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $product->category) }}" maxlength="255">
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="product_code" class="form-label">Kode produk</label>
        <input id="product_code" name="product_code" type="text" class="form-control @error('product_code') is-invalid @enderror" value="{{ old('product_code', $product->product_code) }}" maxlength="100">
        @error('product_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="barcode" class="form-label">Barcode</label>
        <input id="barcode" name="barcode" type="text" class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode', $product->barcode) }}" maxlength="100">
        @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
        <input id="price" name="price" type="number" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
        <input id="stock" name="stock" type="number" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock ?? 0) }}" min="0" step="1" required>
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="image" class="form-label">Image</label>
        <input id="image" name="image" type="text" class="form-control @error('image') is-invalid @enderror" value="{{ old('image', $product->image) }}" maxlength="255" placeholder="URL atau path gambar">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <div class="form-check">
            <input id="is_active" name="is_active" type="checkbox" value="1" class="form-check-input" @checked(old('is_active', $product->exists ? $product->is_active : true))>
            <label for="is_active" class="form-check-label">Status aktif</label>
        </div>
        @error('is_active')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>