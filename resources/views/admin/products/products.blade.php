@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Quản lý sản phẩm</h3>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    <div class="row mb-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <!-- Cột Form tìm kiếm -->
                            <form class="row gx-2 align-items-center flex-grow-1 me-3"
                                action="{{ route('admin.products.index') }}" method="GET">
                                <!-- Ô tìm kiếm -->
                                <div class="col-auto">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light" id="search-icon">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control form-group-lg"
                                            placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm"
                                            aria-describedby="search-icon" name="q" value="{{ request('q') }}">
                                    </div>
                                </div>

                                <!-- Select danh mục -->
                                <div class="col-auto">
                                    <select class="form-select" name="category">
                                        <option value="">-- Danh mục --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Select thương hiệu -->
                                <div class="col-auto">
                                    <select class="form-select" name="brand">
                                        <option value="">-- Thương hiệu --</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Select trạng thái -->
                                <div class="col-auto">
                                    <select class="form-select" name="status">
                                        <option value="">-- Trạng thái --</option>
                                        @foreach ($statuses as $key => $status)
                                            <option value="{{ $key }}"
                                                {{ request('status') == $key ? 'selected' : '' }}>
                                                {{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nút submit -->
                                <div class="col-auto">
                                    <button class="btn btn-primary" type="submit">
                                        Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                        Đặt lại
                                    </a>
                                </div>
                            </form>

                            <!-- Nút thêm sản phẩm -->
                            <a href="{{ route('admin.products.create') }}" class="btn btn-success mt-2 mt-md-0">
                                Thêm sản phẩm
                            </a>
                        </div>
                    </div>
                    
                    <!-- Thông báo thành công hoặc lỗi -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Thông báo không tìm thấy -->
                    @if ($noResults)
                        <div class="alert alert-warning" role="alert">
                            Không tìm thấy sản phẩm nào khớp với tiêu chí tìm kiếm.
                        </div>
                    @else
                        <!-- Bảng sản phẩm -->
                        <div class="table-responsive-sm">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Tên sản phẩm</th>
                                        <th class="text-center">Hình ảnh</th>
                                        <th class="text-center">SKU</th>
                                        <th class="text-center">Danh mục</th>
                                        <th class="text-center">Thương hiệu</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Ngày tạo</th>
                                        <th class="text-center">Ngày cập nhật</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td class="text-center">{{ $product->id }}</td>
                                            <td class="text-center text-truncate">
                                                {{ Str::limit($product->name, 20, '...') }}</td>
                                            <td class="text-center">
                                                <img src="{{ Storage::url($product->thumbnail) }}" alt="Thumbnail"
                                                    class="img-fluid" style="max-width: 50px; height: auto;" />
                                            </td>
                                            <td class="text-center">{{ $product->sku }}</td>
                                            <td class="text-center">
                                                {{ $product->category ? $product->category->name : 'N/A' }}</td>
                                            <td class="text-center">{{ $product->brand ? $product->brand->name : 'N/A' }}
                                            </td>
                                            <td class="text-center">{{ $product->status }}</td>
                                            <td class="text-center">{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="btn btn-sm btn-primary">Chi tiết</a>
                                                {{-- <button type="button" class="btn btn-sm btn-warning">Sửa</button> --}}
                                                @if (in_array($product->status, ['active', 'out_of_stock']))
                                                    <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Bạn có chắc chắn muốn ngừng kích hoạt sản phẩm này và toàn bộ biến thể của nó không?')">
                                                            Ngừng bán
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.products.restore', $product->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này và các biến thể không?')">
                                                            Khôi phục
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> 
                            <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $products->withQueryString()->links() }}
                        </div>
                       
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
