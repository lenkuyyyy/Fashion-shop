<script src="{{ asset('assets/js/jquery-2.1.0.min.js') }}"></script>

<script src="{{ asset('assets/js/popper.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('assets/js/owl-carousel.js') }}"></script>
<script src="{{ asset('assets/js/accordions.js') }}"></script>
<script src="{{ asset('assets/js/datepicker.js') }}"></script>
<script src="{{ asset('assets/js/scrollreveal.min.js') }}"></script>
<script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('assets/js/imgfix.min.js') }}"></script>
<script src="{{ asset('assets/js/slick.js') }}"></script>
<script src="{{ asset('assets/js/lightbox.js') }}"></script>
<script src="{{ asset('assets/js/isotope.js') }}"></script>

<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(function() {
        var selectedClass = "";
        $("p").click(function() {
            selectedClass = $(this).attr("data-rel");
            $("#portfolio").fadeTo(50, 0.1);
            $("#portfolio div").not("." + selectedClass).fadeOut();
            setTimeout(function() {
                $("." + selectedClass).fadeIn();
                $("#portfolio").fadeTo(50, 1);
            }, 500);
        });
    });
</script>

{{-- SỬA LẠI SCRIPT CHO TRANG THANH TOÁN --}}
<script>
    // Tìm phần tử chi tiết của phương thức COD
    const paymentDetails = document.getElementById('cod-details');

    // Chỉ thực thi mã này nếu phần tử 'cod-details' tồn tại (tức là đang ở trang thanh toán)
    if (paymentDetails) {
        // Xử lý khi người dùng thay đổi lựa chọn thanh toán
        document.querySelectorAll('input[name="paymentMethod"]').forEach((el) => {
            el.addEventListener('change', function() {
                document.querySelectorAll('.payment-method-details').forEach(div => div.classList.remove('active'));
                
                const selected = this.value;
                const selectedDetails = document.getElementById(selected + '-details');

                if (selectedDetails) {
                    selectedDetails.classList.add('active');
                }
            });
        });

        // Mặc định hiển thị chi tiết cho COD khi tải trang
        paymentDetails.classList.add('active');
    }
</script>
{{-- end checkout script --}}

@stack('scripts')