@extends('admin.layouts.AdminLayouts')
@section('title-page')
<h3>Chỉnh sửa thương hiệu</h3>
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
         <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" class="form-control border border-2 p-4"
             enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên thương hiệu</label>
                <input type="text" id="name" name="name" value="{{ old('name', $brand->name) }}" class="form-control">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $brand->slug) }}" class="form-control">
                @error('slug')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status">Trạng thái:</label>
                <select name="status" class="form-control">
                    <option value="active" {{ $brand->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ $brand->status == 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
                </select>
            </div>
            <button type="submit" class="btn btn-warning mb-2">Cập nhật</button>
        </form>
    </div>
    </div>
        
      
</div>

@endsection