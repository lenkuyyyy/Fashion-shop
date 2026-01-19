// Thêm vào giỏ hàng bằng Ajax
$(document).on("click", ".btn-add-cart", function (e) {
    e.preventDefault();

    var variantOption = $("#size-select option:selected");
    var variantId = variantOption.val();
    var quantity = $("#quantity-input").val();
    var image = variantOption.data("image"); // lấy ảnh từ data-image của option

    // Gửi Ajax POST lên server
    $.ajax({
        url: "/cart/add-ajax",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            product_variant_id: variantId,
            quantity: quantity,
            image: image, // gửi kèm ảnh
        },

        //Xử lý kết quả từ server
        success: function (res) {
            if (res.success) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: res.message,
                    showConfirmButton: false,
                    timer: 1500,
                    scrollbarPadding: false,
                });

                $("#cart-count").removeClass("d-none").text(res.cart_count);
                // Đồng bộ cart count giữa các tab
                localStorage.setItem("cart_count", res.cart_count);
            } else {
                // Nếu có order_id → hiển thị nút "Thanh toán lại"
                if (res.order_id) {
                    Swal.fire({
                        icon: "warning",
                        title: "Đơn hàng chưa thanh toán",
                        html: `
                            <p>Sản phẩm này đã nằm trong đơn hàng chưa thanh toán.</p>
                            <form id="retry-payment-form" action="/checkout/retry/${res.order_id}" method="POST">
                                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                                <button type="submit" class="btn btn-primary mt-2">🔁 Thanh toán lại đơn</button>
                            </form>
                        `,
                        showConfirmButton: false,
                        scrollbarPadding: false,
                    });
                } else {
                    // Các lỗi thông thường khác
                    Swal.fire({
                        icon: "error",
                        title: "Thông báo",
                        text: res.message,
                        scrollbarPadding: false,
                    }).then(() => {
                        if (res.message.includes("Vui lòng đăng nhập")) {
                            window.location.href = "/login";
                        }
                    });
                }
            }
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Lỗi!",
                text: "Có lỗi xảy ra khi thêm vào giỏ hàng!",
                scrollbarPadding: false,
            });
        },
    });
});

// Lắng nghe sự kiện storage để đồng bộ cart count giữa các tab
window.addEventListener("storage", function (event) {
    if (event.key === "cart_count") {
        const count = event.newValue || 0;
        $("#cart-count")
            .toggleClass("d-none", count == 0)
            .text(count);
    }
});
