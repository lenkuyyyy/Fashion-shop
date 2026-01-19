<div class="col-lg-8">
    <div class="left-content">
        <div class="slideshow-container" id="slideshow-container">
            @if(isset($slides) && $slides->count() > 0)
                @foreach($slides as $index => $slide)
                    <div class="mySlides">
                        @if($slide->news_id)
                            <a href="{{ route('news.show', $slide->news_id) }}">
                                <img src="{{ $slide->image_url }}" alt="Banner {{ $index + 1 }}" style="width:100%; height:100%; object-fit: cover;">
                            </a>
                        @else
                            <img src="{{ $slide->image_url }}" alt="Banner {{ $index + 1 }}" style="width:100%; height:100%; object-fit: cover;">
                        @endif
                    </div>
                @endforeach
                <a class="prev" onclick="plusSlides(-1)">❮</a>
                <a class="next" onclick="plusSlides(1)">❯</a>
            @else
                <p>Không có slide nào để hiển thị.</p>
            @endif
        </div>
        <br>
        <div style="text-align:center" id="dot-container">
            @if(isset($slides) && $slides->count() > 0)
                @foreach($slides as $index => $slide)
                    <span class="dot" onclick="currentSlide({{ $index + 1 }})"></span>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
.main-banner .left-content .slideshow-container {
    position: relative;
    width: 100%;
    height: 412px;
    background-color: #000;
    overflow: hidden;
}

.main-banner .left-content .mySlides {
    display: none;
    width: 100%;
    height: 100%;
}

.main-banner .left-content .mySlides img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.main-banner .left-content .prev,
.main-banner .left-content .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: auto;
    padding: 10px 15px;
    color: #fff;
    font-size: 24px;
    background: rgba(0, 0, 0, 0.5);
    user-select: none;
    z-index: 10;
}

.main-banner .left-content .prev:hover,
.main-banner .left-content .next:hover {
    background: rgba(0, 0, 0, 0.8);
}

.main-banner .left-content .prev { left: 10px; }
.main-banner .left-content .next { right: 10px; }

.main-banner .left-content .dot {
    cursor: pointer;
    height: 15px;
    width: 15px;
    margin: 0 2px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    transition: background-color 0.6s ease;
}

.main-banner .left-content .dot.active,
.main-banner .left-content .dot:hover {
    background-color: #717171;
}
</style>

@push('scripts')
    <script>
        let slideIndex = 1;
        let autoSlideInterval;

        function plusSlides(n) {
            clearInterval(autoSlideInterval);
            showSlides(slideIndex += n);
            startAutoSlide();
        }

        function currentSlide(n) {
            clearInterval(autoSlideInterval);
            showSlides(slideIndex = n);
            startAutoSlide();
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            if (slides.length === 0) return;
            if (n > slides.length) { slideIndex = 1 }
            if (n < 1) { slideIndex = slides.length }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            if (slides.length > 0) {
                slides[slideIndex - 1].style.display = "block";
            }
            if (dots.length > 0) {
                dots[slideIndex - 1].className += " active";
            }
        }

        function startAutoSlide() {
            autoSlideInterval = setInterval(() => {
                plusSlides(1);
            }, 10000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            showSlides(slideIndex);
            startAutoSlide();
        });
    </script>
@endpush