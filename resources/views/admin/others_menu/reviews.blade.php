@extends('admin.layouts.AdminLayouts')

@section('title')
  <title>Quản lý đánh giá</title>
@endsection

@section('content')
<div class="container-fluid">
  <div class="col-lg-12">
    <div class="row g-4 mb-4">
      <!-- Cột đánh giá -->
      <div class="col-md-12">
        <!-- Bộ lọc -->
        <form method="GET" class="mb-3 d-flex gap-2">
        <select name="rating" class="form-select" style="width: 120px;">
            <option value="">Tất cả sao</option>
            @for ($i = 1; $i <= 5; $i++)
            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} sao</option>
            @endfor
        </select>

        <select name="status" class="form-select" style="width: 140px;">
            <option value="">Tất cả trạng thái</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chưa duyệt</option>
        </select>

        <input type="date" name="date" class="form-control" style="width: 170px;" value="{{ request('date') }}">

        <select name="date_range" class="form-select" style="width: 170px;">
            <option value="">Tất cả thời gian</option>
            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hôm nay</option>
            <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>7 ngày qua</option>
            <option value="last_30_days" {{ request('date_range') == 'last_30_days' ? 'selected' : '' }}>30 ngày qua</option>
            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>Tháng này</option>
            <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>Năm nay</option>
        </select>



        <button type="submit" class="btn btn-primary">Lọc</button>
        <a href="{{ route('reviews') }}" class="btn btn-secondary">Reset</a>
        </form>

        @foreach ($reviews as $review)
        <div class="card card-success collapsed-card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="bi bi-chat-left-text me-2"></i>{{ $review->product->name ?? 'Sản phẩm' }}
              @if($review->status !== 'approved')
              <span class="badge bg-danger ms-2 d-flex align-items-center">
                <i class="bi bi-exclamation-circle me-1"></i> Chưa duyệt
              </span>
              @endif
            </h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <img src="{{ $review->product->image ?? '#' }}" alt="Ảnh sản phẩm" height="50px" class="rounded me-2 border">
              <div>
                <a href="#" class="fw-semibold text-decoration-none">{{ $review->product->name ?? 'Tên sản phẩm' }}</a><br>
                <span class="badge bg-primary"><i class="bi bi-tags me-1"></i>{{ $review->product->category->name ?? 'Danh mục' }}</span>
              </div>
            </div>

                                <div class="mb-3 border-bottom pb-2">
                                    <div class="d-flex justify-content-between">
                                        <strong><i
                                                class="bi bi-person-circle me-1"></i>{{ $review->user->name ?? 'Khách' }}</strong>
                                        <span class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <small class="text-muted"><i
                                            class="bi bi-clock me-1"></i>{{ $review->created_at->format('d/m/Y') }}</small>
                                    <p class="mt-2 mb-1">{{ $review->comment }}</p>

                                    <div class="mt-2">
                                        @if ($review->status !== 'approved')
                                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-check-circle"></i> Duyệt
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc muốn xoá đánh giá này?')">
                                                <i class="bi bi-trash"></i> Xoá
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="mt-4">
                        {{ $reviews->links('pagination::bootstrap-5') }}
                    </div>
                </div>


                <!-- Cột Chat khách hàng  chưa làm kịp-->
                {{-- <div class="col-md-4">
                    <div class="card direct-chat direct-chat-primary mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Tin nhắn từ khách hàng</h3>
                            <div class="card-tools">
                                <span title="3 New Messages" class="badge text-bg-primary">3</span>
                                <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                </button>
                                <button type="button" class="btn btn-tool" title="Contacts" data-lte-toggle="chat-pane">
                                    <i class="bi bi-chat-text-fill"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="direct-chat-messages">
                                <!-- Tin nhắn 1 -->
                                <div class="direct-chat-msg">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-start">Alexander Pierce</span>
                                        <span class="direct-chat-timestamp float-end">23 Jan 2:00 pm</span>
                                    </div>
                                    <img class="direct-chat-img" src="../../dist/assets/img/user1-128x128.jpg"
                                        alt="message user image" />
                                    <div class="direct-chat-text">Is this template really for free? That's unbelievable!
                                    </div>
                                </div>
                                <!-- Tin nhắn phản hồi -->
                                <div class="direct-chat-msg end">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-end">Sarah Bullock</span>
                                        <span class="direct-chat-timestamp float-start">23 Jan 2:05 pm</span>
                                    </div>
                                    <img class="direct-chat-img" src="../../dist/assets/img/user3-128x128.jpg"
                                        alt="message user image" />
                                    <div class="direct-chat-text">You better believe it!</div>
                                </div>
                                <!-- Thêm tin nhắn khác nếu cần -->
                            </div>

                            <div class="direct-chat-contacts">
                                <ul class="contacts-list">
                                    <li>
                                        <a href="#">
                                            <img class="contacts-list-img" src="../../dist/assets/img/user1-128x128.jpg"
                                                alt="User Avatar" />
                                            <div class="contacts-list-info">
                                                <span class="contacts-list-name">Count Dracula <small
                                                        class="contacts-list-date float-end">2/28/2023</small></span>
                                                <span class="contacts-list-msg">How have you been? I was...</span>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- Có thể thêm danh sách liên hệ -->
                                </ul>
                            </div>
                        </div>

                        <div class="card-footer">
                            <form action="#" method="post">
                                <div class="input-group">
                                    <input type="text" name="message" placeholder="Type Message ..."
                                        class="form-control" />
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-primary">Send</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> --}}
                <!-- End cột chat -->
            </div>
        </div>
    </div>
@endsection
