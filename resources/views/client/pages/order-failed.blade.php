<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán thất bại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .order-failed-container {
            max-width: 450px;
            margin: 80px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 40px 30px;
            text-align: center;
        }
        .order-failed-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="order-failed-container">
        <div class="order-failed-icon">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <h1 class="text-danger mb-3">Thanh toán thất bại</h1>
        <p class="mb-4">{{ $error }}</p>
        <a href="{{ route('checkout') }}" class="btn btn-primary"><i class="bi bi-arrow-left"></i>Tạo lại đơn</a>
        <a href="{{ route('orders.index') }}" class="btn btn-danger"><i class="bi bi-clock-history"></i>Xem lại đơn</a>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>
</html>