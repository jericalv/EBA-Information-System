@php
    $name        = $c->business_name ?: $c->name;
    $gi          = abs(crc32($name)) % count($gradients);
    $poster      = $c->cover_photo ?: $c->carousel_image;
    $rating      = (float) ($c->display_rating ?? 0);
    $reviewCount = (int) ($c->display_review_count ?? 0);
    $productCount = (int) ($c->products_count ?? 0);
    $initials    = strtoupper(mb_substr($name, 0, 2));
    $variant     = $variant ?? 'grid';
@endphp
<a href="{{ route('concessionaires.show', $c) }}"
   class="cc-card cc-card--{{ $variant }}"
   data-name="{{ strtolower($name) }}"
   data-location="{{ strtolower($c->location ?: 'CvSU-Trece Campus') }}">

    <div class="cc-poster"
         @if ($poster)
             style="background-image:url('{{ asset('storage/' . $poster) }}');"
         @else
             style="background:{{ $gradients[$gi] }};"
         @endif>

        @unless ($poster)
            <span class="cc-monogram" aria-hidden="true">{{ $initials }}</span>
        @endunless

        <span class="cc-poster-shade"></span>
    </div>

    <div class="cc-card-body">
        <h3 class="cc-card-name">{{ $name }}</h3>
        <span class="cc-card-loc">{{ $c->location ?: 'CvSU-Trece Campus' }}</span>

        <div class="cc-meta">
            @if ($reviewCount > 0)
                <span class="cc-rating">{{ number_format($rating, 1) }}</span>
                <span class="cc-meta-dot">·</span>
                <span>{{ $reviewCount }} {{ $reviewCount === 1 ? 'review' : 'reviews' }}</span>
            @else
                <span>No reviews yet</span>
            @endif
            <span class="cc-meta-dot">·</span>
            <span>{{ $productCount }} {{ $productCount === 1 ? 'item' : 'items' }}</span>
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
</a>
