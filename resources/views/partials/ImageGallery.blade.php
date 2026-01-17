@php
    $imageLinks = !empty($item->images) ? explode(', ', $item->images) : [];
    $imgAlts = !empty($item->imgalts) ? explode(' | ', $item->imgalts) : [];
    $franchise = !empty($item->franchise) ? $item->franchise : 'default';
    $slides = '';

    foreach ($imageLinks as $index => $image_link) {
        if ($image_link) {
            $alt = $imgAlts[$index] ?? "Resource Pack Preview Image";
            $src = "/storage" . $fileUrl . "/" .  $image_link;
            $loadingAttribute = $index === 0 ? '' : 'loading="lazy"';
            $slides .= "<div class='swiper-slide'><img src='$src' alt='$alt' $loadingAttribute></div>";
        }
    }
@endphp
<div class="galery">
    <div class="swiper-container gallery-top">
        <div class="swiper-wrapper">
            {!! $slides !!}
        </div>
        <div class="swiper-button-next swiper-button"></div>
        <div class="swiper-button-prev swiper-button"></div>
    </div>
    <div class="swiper-container gallery-thumbs">
        <div class="swiper-wrapper">
            {!! $slides !!}
        </div>
    </div>
    <div class="fullscreen-button"></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const galleryThumbs = new Swiper('.gallery-thumbs', {
    spaceBetween: 10,
    slidesPerView: 'auto',
    freeMode: true,
    watchSlidesVisibility: true,
    watchSlidesProgress: true,
    slideToClickedSlide: true,
    centeredSlides: true,
    centeredSlidesBounds: true
});

const galleryTop = new Swiper('.gallery-top', {
    spaceBetween: 10,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
    },
    thumbs: {
        swiper: galleryThumbs
    },
    loop: true
});

const fullscreenButton = document.querySelector('.fullscreen-button');
const galleryContainer = document.querySelector('.gallery-top');

fullscreenButton.addEventListener('click', () => {
    galleryContainer.classList.toggle('fullscreen');
    fullscreenButton.classList.toggle('fullscreen-active');
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && galleryContainer.classList.contains('fullscreen')) {
        galleryContainer.classList.remove('fullscreen');
        fullscreenButton.classList.remove('fullscreen-active');
    }
});
</script>
@endpush