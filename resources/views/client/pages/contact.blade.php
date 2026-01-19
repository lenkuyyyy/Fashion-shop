@extends('client.pages.page-layout')
@section('content')
    @if (session('success'))
        <div class="alert alert-success mt-3 text-center">{{ session('success') }}</div>
    @endif

    <strong>
        <h2 class="text-center section-heading">Chào mừng bạn đến với website chúng tôi <br> Chúng tôi sử dụng mẫu độc quyền.
        </h2>
    </strong>
    <div class="container contact-us">
        <div class="row">
            <div class="col-lg-6">
                <div id="map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d90186.37207676383!2d-80.13495239500924!3d25.9317678710111!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88d9ad1877e4a82d%3A0xa891714787d1fb5e!2sPier%20Park!5e1!3m2!1sen!2sth!4v1637512439384!5m2!1sen!2sth"
                        width="100%" height="400px" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-heading">
                    <h3>Xin chào!</h3>
                    <span>Hãy liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi nào.</span>
                </div>
                <form id="contact" action="{{ route('contact.send') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <fieldset>
                                <input name="name" type="text" id="name" placeholder="Họ và tên" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <input name="email" type="text" id="email" placeholder="Địa chỉ email" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <textarea name="message" rows="6" id="message" placeholder="Nội dung tin nhắn" required></textarea>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" id="form-submit" class="main-dark-button"><i
                                        class="fa fa-paper-plane"></i> Gửi</button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="subscribe mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="section-heading">
                        <h2>Đăng ký nhận bản tin để được giảm giá 30%</h2>
                        <span>Chi tiết tạo nên sự khác biệt cho HN_447 so với các mẫu khác.</span>
                    </div>
                    <form id="subscribe" action="{{ route('newsletter.subscribe') }}" method="get">
                        <div class="row">
                            <div class="col-lg-5">
                                <fieldset>
                                    <input name="name" type="text" id="name" placeholder="Họ và tên" required>
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
                                <li>Địa điểm cửa hàng:<br><span>Số 1 Trịnh Văn Bô, Hà Nội</span></li>
                                <li>Điện thoại:<br><span>010-020-0340</span></li>
                                <li>Văn phòng:<br><span>Fpt</span></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul>
                                <li>Giờ làm việc:<br><span>07:30 Sáng - 9:30 Tối hàng ngày</span></li>
                                <li>Email:<br><span>HN_447@company.com</span></li>
                                <li>Mạng xã hội:<br><span><a href="#">Facebook</a>, <a href="#">Instagram</a>,
                                        <a href="#">Behance</a>, <a href="#">LinkedIn</a></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
