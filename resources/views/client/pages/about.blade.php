@extends('client.pages.page-layout')
@section('content')
    <div class="container">
        @auth
            @if (is_null(Auth::user()->email_verified_at))
                <div class="alert alert-warning text-center mt-4">
                    <strong>⚠ Email của bạn chưa được xác minh!</strong>

                    @if (session('resent_code'))
                        <div class="text-success mt-2">✅ Mã đã được gửi tới <b>{{ Auth::user()->email }}</b></div>
                    @endif

                    <form method="POST" action="{{ route('verify.send') }}" class="mt-2">
                        @csrf
                        <button class="btn btn-warning btn-sm">Gửi mã xác minh</button>
                    </form>

                    <form method="POST" action="{{ route('verify.check') }}" class="mt-2 w-50 mx-auto">
                        @csrf
                        <input type="text" name="code" class="form-control mb-2"
                            placeholder="Nhập mã xác minh đã gửi đến email" required>
                        <button class="btn btn-success btn-sm w-100">Xác minh</button>
                    </form>
                </div>
            @else
                <div class="alert alert-success text-center mt-4">
                    ✅ Email của bạn đã được xác minh!
                </div>
            @endif
        @endauth

        @if (session('success'))
            <div class="alert alert-success text-center mt-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- ***** About Area Starts ***** -->
        <div class="about-us">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="left-image">
                            <img src="assets/images/about-left-image.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="right-content">
                            <h4>Về Chúng Tôi & Hành Trình Khởi Đầu</h4>
                            <span>Chúng tôi là một dự án thời trang mới được khởi tạo với mong muốn mang đến những sản phẩm
                                chất lượng và trải nghiệm mua sắm tốt nhất cho khách hàng.</span>
                            <div class="quote">
                                <i class="fa fa-quote-left"></i>
                                <p>Mỗi bước đi đều là sự học hỏi, mỗi sản phẩm đều là sự tâm huyết. Chúng tôi tin rằng sự
                                    chân thành sẽ tạo nên giá trị bền vững.</p>
                            </div>
                            <p>Là một website thời trang mới, chúng tôi đang trong quá trình xây dựng và phát triển. Mặc dù
                                còn non trẻ, nhưng chúng tôi cam kết mang đến những sản phẩm chất lượng, giá cả hợp lý và
                                dịch vụ khách hàng tận tâm. Chúng tôi luôn lắng nghe phản hồi từ khách hàng để không ngừng
                                cải thiện và hoàn thiện mình.</p>
                            <ul>
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li><a href="#"><i class="fa fa-behance"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ***** About Area Ends ***** -->

        <!-- ***** Our Team Area Starts ***** -->
        <section class="our-team">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-heading">
                            <h2>Đội Ngũ Đang Phát Triển</h2>
                            <span>Chúng tôi đang xây dựng một đội ngũ chuyên nghiệp để phục vụ khách hàng tốt nhất.</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="team-item">
                            <div class="thumb">
                                <div class="hover-effect">
                                    <div class="inner-content">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img src="assets/images/team-member-01.jpg" alt="Thành viên đội ngũ">
                            </div>
                            <div class="down-content">
                                <h4>Đang Tuyển Dụng</h4>
                                <span>Vị Trí Đang Mở</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="team-item">
                            <div class="thumb">
                                <div class="hover-effect">
                                    <div class="inner-content">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img src="assets/images/team-member-02.jpg" alt="Thành viên đội ngũ">
                            </div>
                            <div class="down-content">
                                <h4>Đang Tuyển Dụng</h4>
                                <span>Vị Trí Đang Mở</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="team-item">
                            <div class="thumb">
                                <div class="hover-effect">
                                    <div class="inner-content">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img src="assets/images/team-member-03.jpg" alt="Thành viên đội ngũ">
                            </div>
                            <div class="down-content">
                                <h4>Đang Tuyển Dụng</h4>
                                <span>Vị Trí Đang Mở</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Our Team Area Ends ***** -->

        <!-- ***** Services Area Starts ***** -->
        <section class="our-services">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-heading">
                            <h2>Dịch Vụ Chúng Tôi Cung Cấp</h2>
                            <span>Chúng tôi đang phát triển và mở rộng các dịch vụ để đáp ứng nhu cầu ngày càng tăng của
                                khách hàng.</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Bán Hàng Online</h4>
                            <p>Website thương mại điện tử hiện đại, giao diện thân thiện, dễ sử dụng. Chúng tôi đang không
                                ngừng cải thiện cho trải nghiệm mua sắm tốt hơn.</p>
                            <img src="assets/images/service-01.jpg" alt="Dịch vụ bán hàng online">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Giao Hàng Tận Nơi</h4>
                            <p>Dịch vụ giao hàng đang được phát triển và mở rộng. Chúng tôi cam kết giao hàng an toàn và
                                đúng hẹn đến khách hàng.</p>
                            <img src="assets/images/service-02.jpg" alt="Dịch vụ giao hàng">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Hỗ Trợ Khách Hàng</h4>
                            <p>Đội ngũ hỗ trợ khách hàng đang được đào tạo để phục vụ tốt nhất. Chúng tôi luôn sẵn sàng lắng
                                nghe và giải đáp mọi thắc mắc.</p>
                            <img src="assets/images/service-03.jpg" alt="Dịch vụ hỗ trợ">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Services Area Ends ***** -->

        <!-- ***** Subscribe Area Starts ***** -->
        <div class="subscribe">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="section-heading">
                            <h2>Bằng Cách Đăng Ký Nhận Bản Tin, Bạn Sẽ Nhận Được Giảm Giá 30%</h2>
                            <span>Chính những chi tiết nhỏ tạo nên sự khác biệt cho HN_447 so với các mẫu khác.</span>
                        </div>
                        <form id="subscribe" action="{{ route('newsletter.subscribe') }}" method="get">
                            <div class="row">
                                <div class="col-lg-5">
                                    <fieldset>
                                        <input name="name" type="text" id="name" placeholder="Họ và tên"
                                            required>
                                    </fieldset>
                                </div>
                                <div class="col-lg-5">
                                    <fieldset>
                                        <input name="email" type="text" id="email" pattern="[^ @]*@[^ @]*"
                                            placeholder="Địa chỉ email của bạn" required>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <button type="submit" id="form-submit" class="main-dark-button"><i
                                                class="fa fa-paper-plane"></i></button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-6">
                                <ul>
                                    <li>Địa Chỉ Cửa Hàng:<br><span>Số 1 Trịnh Văn Bô, Hà Nội</span></li>
                                    <li>Điện Thoại:<br><span>010-020-0340</span></li>
                                    <li>Văn Phòng:<br><span>Fpt</span></li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul>
                                    <li>Giờ Làm Việc:<br><span>07:30 Sáng - 9:30 Tối hàng ngày</span></li>
                                    <li>Email:<br><span>HN_447@company.com</span></li>
                                    <li>Mạng Xã Hội:<br><span><a href="#">Facebook</a>, <a
                                                href="#">Instagram</a>, <a href="#">Behance</a>, <a
                                                href="#">LinkedIn</a></span></li>,
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ***** Subscribe Area Ends ***** -->
    </div>
@endsection
