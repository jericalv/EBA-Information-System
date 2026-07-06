@php
    $name         = $c->business_name ?: $c->name;
    $gi           = abs(crc32($name)) % count($gradients);
    $poster       = $c->cover_photo ?: $c->carousel_image;
    $rating       = (float) ($c->display_rating ?? 0);
    $reviewCount  = (int) ($c->display_review_count ?? 0);
    $productCount = (int) ($c->products_count ?? 0);
    $initials     = strtoupper(mb_substr($name, 0, 2));
    $location     = $c->location ?: 'CvSU-Trece Campus';
    $variant      = $variant ?? 'grid';
@endphp
<a href="{{ route('concessionaires.show', $c) }}"
   class="cc-card cc-card--{{ $variant }}"
   data-name="{{ strtolower($name) }}"
   data-location="{{ strtolower($location) }}">

    <div class="cc-poster"
         @if ($poster)
             style="background-image:url('{{ asset('storage/' . $poster) }}');"
         @else
             style="background:{{ $gradients[$gi] }};"
         @endif>

        @unless ($poster)
            <span class="cc-monogram" aria-hidden="true">{{ $initials }}</span>
        @endunless

        @if ($reviewCount > 0)
            <span class="cc-rating-chip">
                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                </svg>
                {{ number_format($rating, 1) }}
            </span>
        @endif
    </div>

    <div class="cc-body">
        <h3 class="cc-name">{{ $name }}</h3>
        <div class="cc-loc">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
            </svg>
            <span>{{ $location }}</span>
        </div>

        @php $peek = $c->products->take(3); @endphp
        @if ($peek->isNotEmpty())
            <div class="cc-peek">
                @foreach ($peek as $p)
                    <span class="cc-peek-img" style="background-image:url('{{ asset('storage/' . $p->image) }}');" title="{{ $p->name }}"></span>
                @endforeach
                @if ($productCount > 3)
                    <span class="cc-peek-more">+{{ $productCount - 3 }}</span>
                @endif
            </div>
        @endif
    </div>

    <div class="cc-foot">
        <span class="cc-foot-meta">
            <strong>{{ $productCount }}</strong> {{ $productCount === 1 ? 'item' : 'items' }}
            @if ($reviewCount > 0)
                <span class="cc-foot-dot">&middot;</span>
                <strong>{{ $reviewCount }}</strong> {{ $reviewCount === 1 ? 'review' : 'reviews' }}
            @else
                <span class="cc-foot-dot">&middot;</span> No reviews yet
            @endif
        </span>
        <span class="cc-view">
            Visit
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"/>
            </svg>
        </span>
    </div>
</a>
