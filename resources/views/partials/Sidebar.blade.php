<div class="support-banner">
    
</div>

<ul class="recommendations">
    <h2>Other Packs</h2>
    @foreach($recommendations as $index => $rec)
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