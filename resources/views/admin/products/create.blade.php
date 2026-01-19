@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Thêm sản phẩm</h3>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
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

        {{-- Form thêm sản phẩm --}}
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                <!-- Thêm ảnh -->
                <div class="col-md-5 text-center">
                    <img id="preview_thumbnail" src="https://via.placeholder.com/400x300?text=Preview"
                        class="img-fluid rounded mb-2" style="max-height: 400px; object-fit: cover; cursor: pointer;"
                        alt="Ảnh sản phẩm" onclick="document.querySelector('input[name=thumbnail]').click()">
                    <div class="mt-3">
                        <label for="thumbnail" class="form-label d-none" id="label_thumbnail">Chọn ảnh</label>
                        <input type="file" id="thumbnail" name="thumbnail" class="form-control d-none" accept="image/*"
                            onchange="previewThumbnail(this)">
                    </div>
                    @error('thumbnail')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thêm thông tin sản phẩm -->
                <div class="col-md-7">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select name="category_id" class="form-control" id="category_id">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="brand_id" class="form-label">Thương hiệu</label>
                            <select name="brand_id" class="form-control" id="brand_id">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" class="form-control" id="status">
                                <option value="active">Kích hoạt</option>
                                <option value="inactive">Tạm ẩn</option>
                                <option value="out_of_stock">Hết hàng</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="short_description" class="form-label">Mô tả ngắn</label>
                            <input type="text" name="short_description" class="form-control" id="short_description"
                                value="{{ old('short_description') }}">
                            @error('short_description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control" id="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Thông tin biến thể  --}}
            <h4>Thông tin biến thể</h4>
            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-12">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Danh sách màu (ngăn cách bằng dấu phẩy)</label>
                            <input type="text" id="colors" name='colors' class="form-control"
                                placeholder="Đỏ,Trắng,Xanh" value="{{ old('colors') }}">
                        </div>

                        <div class="col-md-6">
                            <label>Danh sách size (ngăn cách bằng dấu phẩy)</label>
                            <input type="text" id="sizes" name='sizes' class="form-control" placeholder="S,M,L,XL"
                                value="{{ old('sizes') }}">
                        </div>


                        <div class="col-md-6">
                            <label>Giá nhập mặc định</label>
                            <input type="number" id="default_import_price" name='default_import_price'
                                class="form-control" step="any" value="{{ old('default_import_price') }}">
                        </div>

                        <div class="col-md-6">
                            <label>Số lượng mặc định</label>
                            <input type="number" id="default_quantity" name='default_quantity' class="form-control"
                                value="{{ old('default_quantity') }}">
                        </div>
                        <div class="col-md-6">
                            <label>Giá bán mặc định</label>
                            <input type="number" id="default_price" name='default_price' class="form-control"
                                step="any" value="{{ old('default_price') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nút tạo biến thể (nhấn sau khi đã nhập tất cả thông tin hợp lệ cho biến thể) --}}
            <button type="button" class="btn btn-secondary mb-3" onclick="generateVariants()">Tạo biến thể</button>

            {{-- Danh sách biến thể sẽ được hiển thị ở đây --}}
            <ul id="variantList"></ul>

            {{-- Input ẩn để gửi dữ liệu khi submit (dữ liệu được xử lý ở bên script) --}}
            <input type="hidden" name="variants" id="variants">

            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-danger">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                </div>
            </div>
        </form>

    </div>

@endsection
@section('scripts')
    <script>
        // Hàm preview ảnh thumbnail
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

        // Hàm preview ảnh variant
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

        // auto generate variants
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
            // Sửa ID từ variants_json -> variants
            document.getElementById('variants').value = JSON.stringify(variants);

        }

        // Mở/đóng form thêm biến thể
        document.getElementById('add_variant').addEventListener('click', function() {
            const formContainer = document.getElementById('variant_form_container');
            formContainer.classList.toggle('d-none');
        });

        function showError(inputId, message) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(inputId + '_error');
            input.classList.add('is-invalid');
            error.textContent = message;
        }

        function clearErrors() {
            ['color', 'size', 'default_price', 'default_quantity'].forEach(id => {
                document.getElementById(id).classList.remove('is-invalid');
                document.getElementById(id + '_error').textContent = '';
            });
        }

        // validate input fields before generating variants
        document.getElementById('generate_variants').addEventListener('click', function() {
            clearErrors();
            const colorInput = document.getElementById('color');
            const sizeInput = document.getElementById('size');
            const priceInput = document.getElementById('default_price');
            const importPriceInput = document.getElementById('default_import_price');
            const quantityInput = document.getElementById('default_quantity');

            let hasError = false;

            const colors = colorInput.value.split(',').map(c => c.trim()).filter(Boolean);
            const sizes = sizeInput.value.split(',').map(s => s.trim()).filter(Boolean);
            const price = priceInput.value;
            const importPrice = importPriceInput.value;
            const quantity = quantityInput.value;

            if (!colors.length) {
                showError('color', 'Vui lòng nhập ít nhất một màu.');
                hasError = true;
            }

            if (!sizes.length) {
                showError('size', 'Vui lòng nhập ít nhất một kích cỡ.');
                hasError = true;
            }

            if (!price || parseFloat(price) < 0) {
                showError('default_price', 'Giá phải lớn hơn hoặc bằng 0.');
                hasError = true;
            }
            if (!importPrice || parseFloat(importPrice) < 0) {
                showError('default_import_price', 'Giá nhập phải lớn hơn hoặc bằng 0.');
                hasError = true;
            }

            if (!quantity || parseInt(quantity) < 0) {
                showError('default_quantity', 'Số lượng phải lớn hơn hoặc bằng 0.');
                hasError = true;
            }

            if (hasError) return;

        });

    </script>
@endsection
