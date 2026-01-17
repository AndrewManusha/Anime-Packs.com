<div class="PopularInMonth">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @foreach ($topPacks as $index => $item)
                @php
                    $images = explode(', ', $item->images); // Разбираем один раз
                    $alts = explode(' | ', $item->image_alts); // Разбираем альты
                    $mainImage = $images[0] ?? null;
                    $mainAlt = $alts[0] ?? null;
                    $otherImages = array_slice($images, 1, 4); // Берём максимум 4
                    $otherAlts = array_slice($alts, 1, 4); // Берём альты для остальных изображений
                    $dataImages = implode(', ', array_map(fn($img) => $item->page_url . '/' . (basename($item->page_url) === $item->franchise ? $item->franchise . '/' : '') . $img, $otherImages));
                @endphp
                <a href="{{ $item->page_url }}" class="swiper-slide" data-images="{{ $dataImages }}" role="link">
                    <div class="preview">
                        <img id="mainImage" width="756" height="425" src="storage{{ $item->page_url }}/{{ basename($item->page_url) === $item->franchise ? $item->franchise . '/' : '' }}{{ $mainImage }}" alt="{{ $mainAlt }}" {{ $index > 0 ? 'loading="lazy"' : '' }} decoding="async">
                    </div>
            
                    <div class="info">
                        <div class="title">
                            {{ ucfirst($item->title) }}
                        </div>
            
                        <div class="thumbnails">
                            @foreach ($otherImages as $i => $image)
                                <div class="thumbnail-wrapper">
                                    <img width="152" height="86" src="/storage{{ $item->page_url }}/{{ basename($item->page_url) === $item->franchise ? $item->franchise . '/' : '' }}{{ $image }}" alt="{{ $otherAlts[$i] ?? '' }}" loading="lazy" decoding="async">
                                </div>
                            @endforeach
                        </div>
            
                        <div class="description">
                            {{ ucfirst($item->min_desc) }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    <div class="swiper-button-next swiper-button"></div>
    <div class="swiper-button-prev swiper-button"></div>
    <div class="swiper-pagination"></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const mainSwiper = new Swiper('.swiper-container', {
    slidesPerView: 1,
    spaceBetween: 10,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
    },
    loop: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true
    },
    autoplay: {
        delay: 5000,
        disableOnInteraction: false
    }
});

function handleThumbnails(slide) {
    const thumbnails = slide.querySelectorAll('.thumbnails .thumbnail-wrapper');
    const mainImage = slide.querySelector('#mainImage');
    const originalSrc = mainImage.src;
    const images = slide.getAttribute('data-images')?.split(', ') || [];
    
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('mouseenter', () => {
            if (images[index]) mainImage.src = 'storage' + images[index];
        });
        thumb.addEventListener('mouseleave', () => {
            mainImage.src = originalSrc;
        });
    });
}

const slides = document.querySelectorAll('.swiper-slide');
slides.forEach(slide => {
    slide.addEventListener('mouseenter', () => mainSwiper.autoplay.stop());
    slide.addEventListener('mouseleave', () => mainSwiper.autoplay.start());
    handleThumbnails(slide);
});
</script>
@endpush