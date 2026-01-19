@extends('admin.layouts.AdminLayouts')
@section('title-page')
<h3>Danh sách Thương hiệu</h3>
@endsection
@section('content')
<div class="container-fluid">
    <div class="col-lg-12">       
        <div class="row g-4 mb-4">
            <!-- Cột trái: Danh sách Brands với bảng và paginate -->
            <div class="col-md-12">
                {{-- Phần tìm kiếm --}}
                <div class="card-header bg-info text-white fw-bold">
                    <div class="row align-items-center">
                        <!-- Tìm kiếm -->
                        <div class="col-md-7">
                        <form class="d-flex" method="GET" action="">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Tìm kiếm Brand...">
                            <button class="btn btn-light text-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                         </form>
                        </div>
                        <!-- Lọc theo trạng thái -->
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('admin.brands.index') }}">
                            <select name="status" onchange="this.form.submit()" class="form-select text-center">
                                <option value="">Tất cả</option>
                                <option value="active"   {{ request('status')==='active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </form>
                        </div>
                        <!-- Nút thêm -->
                        <div class="col-md-2">
                            <button class="btn btn-success">
                              <a href="{{ route('admin.brands.create') }}" class="text-white"><i class="bi bi-plus-circle me-1"></i> Thêm</a>
                            </button>
                            <a href="{{ route('admin.brands.trashed') }}" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Thùng rác
                        </a>
                        </div>
                        
                    </div>
                    
                </div>
                {{-- ở đây sẽ là các thông báo thành công success --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-1" role="alert">
                            <strong class="me-2">Thành công!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    {{-- Thông báo lỗi --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mt-1" role="alert">
                            <strong class="me-2">Lỗi!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                {{-- Kết thúc tìm kiếm --}}
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tên thương hiệu</th> 
                            <th scope="col">Slug</th>  
                            <th scope="col">Ngày tạo</th>                        
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($brands->count())
                            @foreach($brands as $brand)
                                <tr>
                                    <td>{{ $brand->id }}</td>
                                    <td>{{ $brand->name }}</td>
                                      
                                    <td>{{ $brand->slug }}</td>
                                    <td>{{ $brand->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $brand->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $brand->status }}
                                        </span>
                                    </td>
                                   
                                    <td class="d-flex justify-content-center align-items-center">
                                      <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    {{-- ở đây sẽ có nút đển cập nhật trạng thái từ active -> inactive, ngược lại. --}}
                                   <!-- Form nút chuyển trạng thái -->
                                   
                                    <form action="{{ route('admin.brands.toggleStatus', $brand->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn chuyển trạng thái?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm me-1 text-white {{ $brand->status == 'active' ? 'bg-danger' : 'bg-success' }}">
                                            {{-- nếu trạng thái là active thì nút màu xanh, nếu inactive thì nút màu đỏ --}}
                                                 <i class="bi {{ $brand->status == 'active' ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                                {{ $brand->status == 'active' ? 'Ngừng bán' : 'Kích hoạt' }}
                                           
                                           
                                        </button>
                                    </form>
                                    <!-- Nút xóa mềm chuyển vào thùng rác -->
                                <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn chuyển thương hiệu này vào thùng rác?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Thùng rác
                                    </button>
                                </form>
                                    
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{-- Phân trang --}}
                <div class="d-flex justify-content-center">
                    {{ $brands->links() }}
                </div>
            </div>
            <!-- Kết thúc cột trái -->

            <!-- Cột phải: TOP Nhãn hàng bán chạy -->
            {{-- <div class="col-md-3">
         
            
                <div class="card">
                    <div class="card-header mb-2">
                        <strong>
                            <h3 class="card-title">TOP Nhãn hàng bán chạy</h3>
                        </strong>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body p-0">
                        <div class="row text-center g-2 mb-2">
                            <!-- Lặp qua mảng topBrands -->
                            @foreach($topBrands as $brand)
                                <div class="col-3 d-flex flex-column align-items-center">
                                    <!-- Vì bảng brands không có hình ảnh, nên ta sử dụng ảnh placeholder (đặt tại public/images/placeholder.png) -->
                                    

                                    <a class="fw-semibold text-secondary text-center text-truncate d-block w-100"
                                       href="#"
                                       title="{{ $brand->name }}"
                                       style="font-size: 0.85rem;">
                                        {{ $brand->name }}<br>
                                        ({{ $brand->total_sold }})
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div> --}}
            <!-- Kết thúc cột phải -->
        </div>
    </div>
</div>
@endsection
