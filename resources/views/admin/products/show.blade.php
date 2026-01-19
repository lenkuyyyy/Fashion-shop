@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>
        Chi tiết & Chỉnh sửa sản phẩm <strong class="text-primary">{{ $product->name }}</strong></h3>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @php
                        $uniqueErrors = array_unique($errors->all());
                    @endphp
                    @foreach ($uniqueErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form chỉnh sửa sản phẩm --}}
        <form id="productForm" action="{{ route('admin.products.update', $product->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Bạn có thể chỉnh sửa thông tin sản phẩm ở đây!
                    </p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <a type="button" class="btn btn-secondary me-2" href="{{ route('admin.products.index') }}">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" id="saveButton" class="btn btn-success">Lưu</button>
                </div>
            </div>

            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-7">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $product->name }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="brand_id" class="form-label">Thương hiệu</label>
                            <select class="form-control" id="brand_id" name="brand_id">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-control" id="category_id" name="category_id">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Kích hoạt
                                </option>
                                <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Không kích
                                    hoạt
                                </option>
                                <option value="out_of_stock" {{ $product->status == 'out_of_stock' ? 'selected' : '' }}>
                                    Hết hàng
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="short_description" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="1">{{ $product->short_description }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-center">
                    <img id="preview_thumbnail" src="{{ Storage::url($product->thumbnail) }}"
                        class="img-fluid rounded mb-2" style="max-height: 400px; object-fit: cover; cursor: pointer;"
                        alt="Ảnh sản phẩm" onclick="document.querySelector('input[name=thumbnail]').click()">
                    <div class="mt-3">
                        <label for="thumbnail" class="form-label d-none" id="label_thumbnail">Chọn ảnh mới</label>
                        <input type="file" id="thumbnail" name="thumbnail" class="form-control d-none" accept="image/*"
                            onchange="previewThumbnail(this)">
                    </div>
                    @error('thumbnail')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-md-12">
                    <h3>Biến thể sản phẩm</h3>
                    <p class="text-muted mb-0">
                        Chỉnh sửa giá nhập, giá bán và số lượng của các biến thể sản phẩm. Các trường màu sắc, kích cỡ không thể chỉnh sửa.
                    </p>
                </div>
            </div>

            <!-- Bảng biến thể sản phẩm -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card p-3">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Ảnh</th>
                                        <th>Màu sắc</th>
                                        <th>Kích cỡ</th>
                                        <th>Trạng thái</th>
                                        <th>Giá nhập</th>
                                        <th>Giá bán</th>
                                        <th>Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($product->variants as $index => $variant)
                                        <tr>
                                            <td>
                                                <img src="{{ asset($variant->image) }}" class="img-fluid rounded"
                                                    style="max-height: 50px; object-fit: cover; cursor: pointer;"
                                                    alt="Ảnh biến thể"
                                                    onclick="document.querySelector(`input[name='variants[{{ $index }}][image]']`).click()">
                                                <input type="file" name="variants[{{ $index }}][image]"
                                                    class="form-control d-none" accept="image/*"
                                                    onchange="previewImage(this, {{ $index }})">
                                                <input type="hidden" name="variants[{{ $index }}][id]"
                                                    value="{{ $variant->id }}">
                                                <input type="hidden" name="variants[{{ $index }}][color]"
                                                    value="{{ $variant->color ?? '' }}">
                                                <input type="hidden" name="variants[{{ $index }}][size]"
                                                    value="{{ $variant->size ?? '' }}">
                                                <input type="hidden" name="variants[{{ $index }}][status]"
                                                    value="{{ $variant->status ?? 'active' }}">
                                                @error('variants.' . $index . '.image')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>{{ $variant->color ?? 'N/A' }}</td>
                                            <td>{{ $variant->size ?? 'N/A' }}</td>
                                            <td>
                                                <select class="form-control" id="status" name="variants[{{ $index }}][status]">
                                                    <option value="active"
                                                        {{ $variant->status == 'active' ? 'selected' : '' }}>Kích hoạt
                                                    </option>
                                                    <option value="inactive"
                                                        {{ $variant->status == 'inactive' ? 'selected' : '' }}>Không kích
                                                        hoạt
                                                    </option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="variants[{{ $index }}][import_price]"
                                                    value="{{ old('variants.' . $index . '.import_price', $variant->import_price) }}"
                                                    step="any">
                                                @error('variants.' . $index . '.import_price')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="variants[{{ $index }}][price]"
                                                    value="{{ old('variants.' . $index . '.price', $variant->price) }}"
                                                    step="any">
                                                @error('variants.' . $index . '.price')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="variants[{{ $index }}][stock_quantity]"
                                                    value="{{ old('variants.' . $index . '.stock_quantity', $variant->stock_quantity) }}"
                                                    step="1">
                                                @error('variants.' . $index . '.stock_quantity')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Sản phẩm không có biến thể nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Nút và form thêm biến thể --}}
        <button type="button" id="add_variant" class="btn btn-primary">-- Thêm biến thể --</button>
        <div id="variant_form_container" class="d-none mt-3">
            <form action="{{ route('admin.products.addVariants', $product->id) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Danh sách màu (phẩy cách)</label>
                        <input type="text" id="colors" class="form-control" placeholder="Đỏ,Trắng,Xanh">
                        <div class="invalid-feedback" id="color_error"></div>
                    </div>
                    <div class="col-md-6">
                        <label>Danh sách size (phẩy cách)</label>
                        <input type="text" id="sizes" class="form-control" placeholder="S,M,L,XL">
                        <div class="invalid-feedback" id="size_error"></div>
                    </div>
                    <div class="col-md-6">
                        <label>Giá nhập mặc định</label>
                        <input type="number" id="default_import_price" class="form-control" step="any">
                        <div class="invalid-feedback" id="default_import_price_error"></div>
                    </div>
                    <div class="col-md-6">
                        <label>Giá mặc định</label>
                        <input type="number" id="default_price" class="form-control" step="any">
                        <div class="invalid-feedback" id="default_price_error"></div>
                    </div>
                    <div class="col-md-6">
                        <label>Số lượng mặc định</label>
                        <input type="number" id="default_quantity" class="form-control" step="1">
                        <div class="invalid-feedback" id="default_quantity_error"></div>
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-success" id="generate_variants"
                            onclick="generateVariants()">Tạo tổ hợp</button>
                        <button type="submit" class="btn btn-info">Lưu</button>
                    </div>
                    <input type="hidden" name="variants" id="variants">
                </div>
                <div class="mt-3">
                    <strong>Các tổ hợp đã tạo:</strong>
                    <ul id="variantList"></ul>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function previewThumbnail(input) {
            const preview = document.getElementById('preview_thumbnail');
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function previewImage(input, index) {
            const preview = document.getElementById(`preview_variant_${index}`);
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function generateSku(length = 8) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let sku = '';
            for (let i = 0; i < length; i++) {
                sku += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return sku;
        }

        function generateVariants() {
            const colors = document.getElementById('colors').value.split(',').map(c => c.trim()).filter(c => c);
            const sizes = document.getElementById('sizes').value.split(',').map(s => s.trim()).filter(s => s);
            const price = document.getElementById('default_price').value;
            const importPrice = document.getElementById('default_import_price').value;
            const quantity = document.getElementById('default_quantity').value;

            if (!colors.length || !sizes.length || !price || !quantity) {
                alert("Vui lòng nhập đủ màu, size, giá và số lượng!");
                return;
            } else if (parseFloat(price) < 0) {
                alert("Giá bán phải lớn hơn hoặc bằng 0!");
                return;
            } else if (parseInt(quantity) < 0) {
                alert("Số lượng phải lớn hơn hoặc bằng 0!");
                return;
            } else if (parseFloat(importPrice) < 0) {
                alert("Giá nhập phải lớn hơn hoặc bằng 0!");
                return;
            }

            const variants = [];
            let html = '';
            colors.forEach(color => {
                sizes.forEach(size => {
                    const sku = generateSku();
                    variants.push({
                        color,
                        size,
                        price,
                        quantity,
                        import_price: importPrice,
                        sku
                    });
                    html +=
                        `<li>${color} - ${size} | SL: ${quantity} | Giá: ${price} | SKU: ${sku} | Giá nhập: ${importPrice}</li>`;
                });
            });

            document.getElementById('variantList').innerHTML = html;
            document.getElementById('variants').value = JSON.stringify(variants);
        }

        document.getElementById('add_variant').addEventListener('click', function() {
            const formContainer = document.getElementById('variant_form_container');
            formContainer.classList.toggle('d-none');
        });
    </script>
@endsection
