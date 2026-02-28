<div class="support-banner">
    
</div>

<ul class="recommendations">
    <h2>Other Packs</h2>
    @foreach($recommendations as $index => $rec)
    <a href="{{ url($rec->page_url) }}">
        <li class="card">
            <div class="image">
                <img src="/storage{{ $rec->page_url }}/{{ basename($rec->page_url) === $rec->franchise ? $rec->franchise . '/' : '' }}{{ strtok($rec->images, ', ') }}" width="304" height="171" 
                    alt="{{ !empty(explode(' | ', $rec->image_alts)[0]) ? explode(' | ', $rec->image_alts)[0] : ucfirst($rec->title) . ' from ' . ucwords(str_replace('-', ' ', $rec->franchise)) . ' Resource Pack Preview' }}"
                    @if($index > 2) loading="lazy" decoding="async" @endif >
            </div>
             <div class="info-panel">
                <div class="header">
                    <div class="title">
                        <h2>{{ $rec->title }}</h2>
                    </div>

                    <div class="rating">
                            <div class="minstars" title="{{ $rec->rating }}/5 stars">
                                {!! renderStars($rec->rating) !!}
                            </div>
                        </div>
                </div>
                <div class="description">
                    <p>{{ $rec->min_desc }}</p>
                </div>
            </div>
        </li>
    </a>
    @endforeach
</ul>