<section class="section" id="kids">
        <div class="container">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h2 class="fw-bold mb-2">Sản phẩm mới cho trẻ em</h2>
                    <span class="text-muted small d-block">Chú ý đến từng chi tiết là điều khiến HN_447 khác biệt so với các giao diện khác.</span>
                </div>
                <div class="col-auto">
                    <a href="{{ route('products-client', 'tre-em') }}" class="btn btn-outline-dark">Xem thêm</a>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="kid-item-carousel">
                        <div class="owl-kid-item owl-carousel">
                            @foreach($kidsProducts as $product)
                                <div class="item">
                                    <div class="thumb">
                                        <div class="hover-content">
                                            <ul>
                                                <li><a href="{{ route('detail-product', $product->id) }}"><i class="fa fa-eye"></i></a></li>
                                                <li><a href="{{ route('detail-product', $product->id) }}"><i class="fa fa-star"></i></a></li>
                                                <li><a href="{{ route('detail-product', $product->id) }}"><i class="fa fa-shopping-cart"></i></a></li>
                                            </ul>
                                        </div>
                                        <img src="{{ asset('storage/' . $product->thumbnail) }}" style="width:350px;height: 450px"  >
                                    </div>
                                    <div class="down-content">
                                        <h4 style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:250px;" title="{{ $product->name }}">{{ $product->name }}</h4>
                                        <span>
                                            @php
                                                $minPrice = $product->variants->min('price');
                                            @endphp
                                            {{ $minPrice ? number_format($minPrice, 0, ',', '.') . ' VNĐ' : 'Liên hệ' }}
                                        </span>
                                        <ul class="stars">
                                            @for($i=0; $i<5; $i++)
                                                <li><i class="fa fa-star"></i></li>
                                            @endfor
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>