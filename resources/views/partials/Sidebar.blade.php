<div class="support-banner">
    
</div>

<div class="recommendations">
    <h2>Other Packs</h2>
    @foreach($recommendations as $index => $rec)
    <a href="{{ url($rec->page_url) }}">
        <div class="card">
            <img src="/storage{{ $rec->page_url }}/{{ basename($rec->page_url) === $rec->franchise ? $rec->franchise . '/' : '' }}{{ strtok($rec->images, ', ') }}" width="304" height="171" 
                alt="{{ !empty(explode(' | ', $rec->image_alts)[0]) ? explode(' | ', $rec->image_alts)[0] : ucfirst($rec->title) . ' from ' . ucwords(str_replace('-', ' ', $rec->franchise)) . ' Resource Pack Preview' }}"
                @if($index > 2) loading="lazy" decoding="async" @endif >
            <h2>{{ $rec->title }}</h2>
        </div>
    </a>
    @endforeach
</div>