@extends('admin.layouts.AdminLayouts')

@section('title-page')
  <title>abc xyz</title>
@endsection
@section('content')
<div class="container-fluid">
  <div class="col-lg-12">
    <div class="row g-4 mb-4">
    <div class="col-md-8">
        {{-- ở dây chứa nội dung bên trái --}}
        <h1>Mọi người sẽ crud ở đây</h1>
    </div>
   
      <div class="col-md-4">
        {{-- ở đây chứa nội dung bên phải       --}}
    </div>
  </div>
 </div>
</div>
@endsection