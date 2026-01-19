@extends('admin.layouts.AdminLayouts')
@section('title-page')
    <h3>Thêm mới thương hiệu</h3>
@endsection

@section('content')
<div class="container-fluid">
  <div class="col-lg-12">
    {{-- class này là độ dài tối đa --}}
    <div class="row g-4 mb-4 justify-content-center">
      {{-- class này là làm bố cục lên cùng hàng --}}
    <div class="col-md-8 ">
        {{-- ở dây chứa nội dung bên trái --}}
        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-danger mb-2"> <i class="bi bi-arrow-left"></i>Quay lại</a>
         {{-- form này dùng để tạo mới thương hiệu --}}
         <form action="{{ route('admin.brands.store') }}" method="POST" class="form-control border border-2 p-4"
            {{-- enctype là để upload file --}}
          enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên thương hiệu</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="form-control">
                @error('slug')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status">Trạng thái:</label>
                <select name="status" class="form-control">
                    <option value="active">Kích hoạt</option>
                    <option value="inactive">Ngừng hoạt động</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-30"><i class="bi bi-plus-circle"></i>Tạo mới</button>
        </form>
    </div>
    </div>
        
      
</div>

@endsection