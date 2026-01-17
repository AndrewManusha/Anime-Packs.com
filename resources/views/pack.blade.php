@extends('layouts.app')

@section('title', ucwords(str_replace('-', ' ', $item->franchise . ': ' . $item->title . ' ' . $item->type . ' for minecraft ')))

@section('canonical', route('pack', [
    'section' => $item->type,
    'franchise' => $item->franchise,
    'name' => ($name = str_replace('_', '-', pathinfo($item->file, PATHINFO_FILENAME))) === $item->franchise ? null : $name
], true))

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ mix('css/pack.css') }}">
@endsection

@section('robots', 'index, follow')

@section('meta-description', $item->min_desc)

@section('link-image', '/storage' . $fileUrl . '/' . explode(", ", $item->images)[0])

@section('link-image-alt', explode(" | ", $item->image_alts)[0])

@section('ad')
<div class="side-container left desktop-only">
    <div class="ad-inner">
        
    </div>
</div>

<div class="side-container right desktop-only">
    <div class="ad-inner">
        
    </div>
</div>
@endsection

@section('content')
<div class="layout">
    <div class="main-content">
        <div class="gallery">
            @include('partials.ImageGallery', ['item' => $item, 'fileUrl' => $fileUrl])
        </div>
        
        <div class="title">
            <h1>{{ ucfirst($item->title) }} Resource pack</h1>
        </div>
        
        <div class="info-panel">
            <div class="left">
                <div class="tags">
                    <a href="{{ url('/catalog/' . $item->franchise) }}">{{ $item->franchise }}</a>
					@foreach (explode(', ', $item->category) as $tag)
					<a href="{{ url('/catalog/' . $tag) }}">{{ $tag }}</a>
					@endforeach
                </div>
                
                <div id="time">{{ getTime($item->created_at) }}</div>
                
                <div id="stat">{{ "Vievs " . $item->views . " | Downloads " . $item->downloads }}</div>
            </div>
            
            <div class="right">
                <div class="rating">
                    Give a star!)
				    <div class="stars" title="{{ $item->rating }}/5 stars">
				        {!! renderStars($item->rating, true) !!}
				    </div>
				</div>
                
                <a class="download" href="{{ url($item->page_url) }}/download">
                    Download
                </a>
            </div>
        </div>
        
        <div class="description">
            {!! $item->description !!}
            
            <h3>How to Use</h3>
            <p>Rename the corresponding standard Minecraft item on an anvil to the exact name from the resource pack to change its appearance.<br>
            Place the resource pack archive into the <code>.minecraft/resourcepacks</code> folder and activate it in the game settings.</p>
            
            <h3>Item Replacement List</h3>
            <div class="name-list {{ ($includes && count($includes) > 5) ? 'small' : '' }}">
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Target Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($includes)
                            @php
                                $count = count($includes);
                                $i = 0;
                            @endphp
                            
                            @while ($i < $count)
                                @php
                                    $current = $includes[$i];
                                    $rowspan = 1;
                                    
                                    while ($i + $rowspan < $count && $includes[$i + $rowspan]['item'] === $current['item']) {
                                        $rowspan++;
                                    }
                                @endphp
                                
                                @for ($j = 0; $j < $rowspan; $j++)
                                    <tr>
                                        <td class="copy-item">
                                            {{ Str::ucfirst(Str::of($includes[$i + $j]['name'])->replace('_', ' ')->remove('.zip')->lower()) }}
                                        </td>
                                        
                                        @if ($j === 0)
                                            <td rowspan="{{ $rowspan }}">
                                                {{ ucwords($current['item']) }}
                                            </td>
                                        @endif
                                    </tr>
                                @endfor
                                
                                @php $i += $rowspan; @endphp
                            @endwhile
                        @else
                            @foreach ($item['items'] as $subitem)
                                <tr>
                                    <td class="copy-item">
                                        {{ Str::ucfirst(Str::of($subitem['name'])->replace('_', ' ')->remove('.zip')->lower()) }}
                                    </td>
                                    <td>
                                        {{ ucwords($subitem['item']) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
                
            @if($includes && count($includes) > 5)
                <button class="toggle-name-list">
                    Show all
                </button>
            @endif
            
            <h3>Dependencies</h3>
            <p>Requires one of the following mods: CIT Resewn (recommended) or OptiFine (stability not guaranteed).</p>
            
            <h3>Compatibility</h3>
            <p>Works with the latest Minecraft version supported by CIT Resewn. Use on earlier game versions is not supported and may cause errors.</p>
        </div>
    </div>
    
    <div class="sidebar">
        @include('partials.Sidebar', ['recommendations' => $recommendations])
    </div>
</div>
@endsection

@section('scripts')

@stack('scripts')

<script>
    let userId = @json($userId) || localStorage.getItem('user_id') || generateUserId();
    
    trackUserActivity(sendViewRequest);
    
    function sendViewRequest() {
        fetch('/api/track-view', {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ page_url: window.location.href, user_id: userId })
        });
    }
    
    function isBot() {
        return /bot|crawl|spider|slurp|baidu|bing|yandex|duckduckgo|teoma|yahoo/i.test(navigator.userAgent) || navigator.webdriver;
    }
    
    function trackUserActivity(callback) {
        if (isBot()) return;
    
        let isUserActive = false;
        let isRequestSent = false;
        let timerFinished = false;
    
        // Таймер на 3 секунды
        setTimeout(() => {
            timerFinished = true;
            if (isUserActive) {
                callback(); // Если активность была до таймера, отправить запрос после его завершения
                isRequestSent = true;
            }
        }, 3000);
    
        // События активности пользователя
        function setActive() {
            if (!isUserActive) {
                isUserActive = true;
            }
            if (timerFinished && !isRequestSent) {
                callback(); // Если активность после завершения таймера, отправить запрос немедленно
                isRequestSent = true;
            }
        }
    
        ['mousemove', 'touchstart', 'touchmove', 'keydown'].forEach(event => document.addEventListener(event, setActive));
    }
    
    function getHeaders() {
        return { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' };
    }
    
    function generateUserId() {
        const id = [...crypto.getRandomValues(new Uint8Array(8))].map(byte => byte.toString(16).padStart(2, '0')).join('');
        localStorage.setItem('user_id', id);
        return id;
    }

    function sendRating(value) {
        fetch('/api/submit-rating', {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ page_url: "{{ $item->page_url }}", rating: value, user_id: userId })
        })
       .then(response => {
            if (!response.ok) {
                if (response.status === 403) {
                    showModal(); // Показываем окно с предложением создать аккаунт
                }
                return response.json().then(err => {
                    throw new Error(err.error || `Ошибка ${response.status}`); // Принудительно кидаем ошибку
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Оценка успешно отправлена:', data);
        })
        .catch(error => console.error('Ошибка при отправке оценки:', error.message));
    }
    
    function toggleNameList() {
        const list = document.querySelector('.name-list');
        const button = document.querySelector('.toggle-name-list');

        list.classList.toggle('small');
        button.textContent = list.classList.contains('small') ? 'Show all' : 'Hide';
    }

    document.querySelector('.toggle-name-list')?.addEventListener('click', toggleNameList);
    
    document.querySelectorAll('.copy-item').forEach(el => {
      el.addEventListener('click', () => {
        navigator.clipboard.writeText(el.textContent.trim())
          .then(() => {
            el.classList.add('copied');
    
            setTimeout(() => {
              el.classList.remove('copied');
            }, 100); // 1 секунда
          });
      });
    });

    function showModal() {
        if (document.querySelector('.modal')) return; // Чтобы не создавать дубли
    
        // Проверяем, загружен ли файл modal.css
        if (!document.querySelector('#modal-css')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '/css/modals/auth-modal.css';
            link.id = 'auth-modal-css'; // Добавляем id, чтобы избежать повторного подключения
            document.head.appendChild(link);
        }
        
        const modal = document.createElement('div');
        modal.classList.add('modal');
        modal.innerHTML = `
        <div class="close-modal"></div>
        <div class="modal-content">
            <h2>You are not authorized</h2>
            <p>To leave a rating, you need to create an account.</p>
            <a href="/login/google">Login</a>
        </div>
        `;
        
        document.body.appendChild(modal);
        
        document.querySelector('.close-modal').addEventListener('click', () => {
            modal.remove();
            removeModalStyles();
        });
    }
    
    // Удаляем CSS после закрытия модалки
    function removeModalStyles() {
        const modalStyles = document.querySelector('#auth-modal-css');
        if (modalStyles) {
            modalStyles.remove();
        }
    }
    
</script>
@endsection