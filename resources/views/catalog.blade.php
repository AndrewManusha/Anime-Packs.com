@extends('layouts.app')

@section('title', 'Best '. ucwords(str_replace('-', ' ', $franchise ?? 'Anime')) .' Resource Packs For Minecraft' )

@section('canonical', route('catalog', array_values(array_filter([
    $section ?: null,
    $franchise ?: null,
    $page && $page != 1 ? "page-$page" : null,
])), true))


@php
    $lastPage = $items->lastPage();
    $prevPage = $page > 1 ? $page - 1 : null;
    $nextPage = ($page < $lastPage) ? $page + 1 : null;
@endphp

@section('pages')
    @if($prevPage)
        <link rel="prev" href="{{ url('/catalog' . 
            ($section ? '/' . $section : '') . 
            ($franchise ? '/' . $franchise : '') . 
            ($prevPage != 1 ? '/page-' . $prevPage : '')) }}">
    @endif

    @if($nextPage)
        <link rel="next" href="{{ url('/catalog' . 
            ($section ? '/' . $section : '') . 
            ($franchise ? '/' . $franchise : '') . 
            '/page-' . $nextPage) }}">
    @endif
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/catalog.css') }}">
@endsection

@section('robots', $search ? 'noindex, follow' : 'index, follow')

@section('meta-description', 'Download the best anime resource packs for Minecraft! Weapons, accessories, and decorations from popular anime.')

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
    <h1 id="title">
        @if ($search)
            Search results: {{ $search }}
        @else
            {{ ucfirst(str_replace('-', ' ', $franchise ?? 'Anime')) }} resource packs for Minecraft 
        @endif
        @if ($page > 1) page {{ $page }} @endif
    </h1>
    <section id="catalog-filters">
	    <form id="searchForm" action="{{ url('/catalog' . ($section ? '/' . $section : '')) }}" method="GET" onsubmit="updateSearchURL(event)" accept-charset="UTF-8" autocomplete="off">
            <input type="text" name="search" id="searchInput" placeholder="Search for {{ $section ? str_replace('-', ' ', $section) . 's' : 'all' }}..." value="{{ $search }}">
            <div class="searchButton" onclick="updateSearchURL(event)"><div class="search-icon"></div></div>
        </form>
        
        <script>
        function updateSearchURL(event) {
            event.preventDefault(); // Предотвращаем стандартную отправку формы
        
            let searchInput = document.getElementById('searchInput').value.trim();
            let form = document.getElementById('searchForm');
        
            if (searchInput) {
                let formattedSearch = searchInput.replace(/\s+/g, '-'); // Заменяем все пробелы на "-"
                let newAction = form.action + '/search:' + encodeURIComponent(formattedSearch);
                window.location.href = newAction;
            } else {
                window.location.href = form.action;
            }
        }
        </script>
        
	    <div id="sort">
            <div class="sort" style="display:{{ $search ? "none" : ""}};">
				<label for="dropdown-toggle-1" class="label">
				<input type="checkbox" id="dropdown-toggle-1" class="dropdown-checkbox" {{ isset($franchise) && $franchise ? 'checked' : '' }} />
				    <div  class="dropdown-label">
				        Franchises
				        <div class="dropdown-icon"></div>
				    </div>
				<div class="dropdown-menu">
				@foreach($franchises as $item)
                    <a class="dropdown-item {{ $franchise == $item ? 'on' : 'off' }}" href="{{ url('/catalog' . ($section ? '/' . $section : '') . ($franchise == $item ? '' : '/' . $item) . ($category ? '/category-' . implode('-', array_map(fn($cat) => $cat, $category)) : '')) }}">
                        <div class="dropdown-item-icon {{ $franchise == $item ? 'on' : 'off' }} circle"></div>{{ ucfirst(str_replace('-', ' ', $item)) }}
                    </a>
                @endforeach
                </div>
                </label>
            </div>
            <div class="sort" style="display:{{ $search ? "none" : ""}};">
				<label for="dropdown-toggle-2" class="label">
				<input type="checkbox" id="dropdown-toggle-2" class="dropdown-checkbox" {{ isset($category) && $category ? 'checked' : '' }} />
				    <div  class="dropdown-label">
				        Categories
				        <div class="dropdown-icon"></div>
				    </div>
				<div class="dropdown-menu">
				@foreach($categories as $item)
                    @php
                        if (in_array($item, $category)) {
                            $NewCategory = array_diff($category, [$item]);
                        } else {
                            $NewCategory = array_merge($category, [$item]);
                        }
                        $NewCategoryString = implode('-', $NewCategory);
                        $url = url('/catalog' . ($section ? '/' . $section : '') . ($franchise ? '/' . $franchise : '') . ($NewCategoryString ? '/category-' . $NewCategoryString : ''));
                    @endphp
                    <a class="dropdown-item {{ in_array($item, $category) ? 'on' : 'off' }}" href="{{ $url }}">
                        <div class="dropdown-item-icon {{ in_array($item, $category) ? 'on' : 'off' }}"></div>{{ ucfirst(str_replace('-', ' ', $item)) }}
                    </a>
                @endforeach
                </div>
                </label>
            </div>
        </div>
	</section>
    @include('partials.pagination', ['items' => $items, 'franchise' => $franchise, 'category' => $category, 'page' => $page, 'search' => $search, 'section' => $section])
    <ul id="content">
        @foreach($items as $index => $item)
        <li class="card">
				<div class="image">
                    <a href="{{ $item->page_url }}">
                        <img src="/storage{{ $item->fileUrl }}/{{ strtok($item->images, ', ') }}" width="251" height="141" 
                            alt="{{ !empty(explode(' | ', $item->image_alts)[0]) ? explode(' | ', $item->image_alts)[0] : ucfirst($item->title) . ' from ' . ucwords(str_replace('-', ' ', $item->franchise)) . ' Resource Pack Preview' }}"
                            @if($index > 2) loading="lazy" decoding="async" @endif >
                    </a>
			    </div>
				<div class="info-panel">
					<div class="header">
                        <div class="title">
                            <h2><a href="{{ $item->page_url }}">{{ ucfirst($item->title) }}</a></h2>
                        </div>
                        
                        <div class="tags">
                            <a href="{{ url('/catalog/' . ($section ? $section . '/' : '') . $item->franchise) }}">{{ $item->franchise }}</a>
                            <span> | </span>
                            @foreach (explode(', ', $item->category) as $tag)
                            <a href="{{ url('/catalog/' . ($section ? $section . '/' : '') . 'category-' . $tag) }}">{{ $tag }}</a>
                            @if (!$loop->last)
                            <span> | </span>
                            @endif
                            @endforeach
                        </div>
                        
                        <div class="rating">
                            <div class="stars" title="{{ $item->rating }}/5 stars">
                                {!! renderStars($item->rating) !!}
                            </div>
                        </div>
                            
                        <div class="statistics">
                            <span class="views">{{ shortenNumber($item->views) }}</span> |
                            <span class="downloads"><div class="download-icon"></div>{{ shortenNumber($item->downloads) }}</span>
                        </div>
                    </div>
					
					<div class="description">
						{{ $item->min_desc }}
					</div>
					
					<div class="footer">
						<div class="created_at">
							{{ getTime($item->created_at) }}
						</div>
						<div class="updated_at" style="{{ $item->updated_at == $item->created_at ? 'display: none;' : '' }}">
                            <div class="refresh-icon"><div class="arrow"></div></div>  
                            {{ getTime($item->updated_at) }}
                        </div>
					</div>
				</div>
		</li>
        @endforeach
    </ul>
    @include('partials.pagination', ['items' => $items, 'franchise' => $franchise, 'category' => $category, 'page' => $page, 'search' => $search, 'section' => $section])
@endsection

@section('scripts')

@stack('scripts')

@endsection