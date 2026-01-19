<div class="card shadow-sm">
    <div class="card-header bg-dark text-white text-center">
        <h5 class="mb-0">Xác minh số điện thoại</h5>
    </div>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card-body">
        <p>Vui lòng nhập mã xác minh được gửi đến số điện thoại <strong>{{ session('phone_number') }}</strong>.</p>
        <form method="POST" action="{{ route('account.verify-phone') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Mã xác minh</label>
                <input type="text" name="verification_code" class="form-control" placeholder="Nhập mã xác minh (6 chữ số)">
                @error('verification_code')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-success w-100">Xác minh</button>
        </form>
    </div>
</div>