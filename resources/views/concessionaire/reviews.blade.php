@extends('concessionaire.layout')

@section('title', 'Reviews')

@section('extra-css')
<style>
    .reviews-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .card-body {
        padding: 0;
        overflow-x: auto;
    }
    .section-header {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }
    .reviews-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .reviews-stat-pills {
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }
    .reviews-pill {
        font-size: 12px;
        font-weight: 700;
        padding: 6px 12px;
    }
    .reviews-toolbar {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
    }
    .reviews-toolbar-inner {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
        padding: 16px 24px;
    }
    .reviews-search-wrapper {
        position: relative;
        flex: 1;
        min-width: 0;
    }
    .reviews-search-wrapper svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #94a3b8;
    }
    .filters-select {
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
        cursor: pointer;
        min-width: 170px;
        width: auto;
        flex-shrink: 0;
    }
    .filters-select:focus {
        outline: none;
        border-color: #0a5c2f;
    }
    .reviews-search-input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        color: #1e293b;
        font: inherit;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .reviews-search-input:focus {
        outline: none;
        border-color: #0a5c2f;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    thead th {
        text-align: left;
        padding: 12px 24px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    tbody td {
        padding: 14px 24px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    tbody tr:hover {
        background: #f8fafc;
    }
    tbody tr:last-child td {
        border-bottom: none;
    }
    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #0a5c2f;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .user-name {
        font-weight: 600;
    }
    .user-email {
        font-size: 13px;
        color: #64748b;
    }
    .stars {
        letter-spacing: 1px;
        color: #f59e0b;
        white-space: nowrap;
    }
    .table-meta {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
    }
    .comment-cell {
        color: #334155;
        line-height: 1.55;
        white-space: normal;
    }
    .badge {
        display: inline-flex;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-secondary {
        background: #f1f5f9;
        color: #64748b;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
    .btn-green {
        background: var(--green);
        color: #fff;
    }
    .btn-green:hover {
        background: #0d7a3e;
    }
    .btn-outline {
        background: transparent;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .btn-outline:hover {
        background: #f8fafc;
    }
    .pagination-wrap {
        padding: 16px 24px;
    }
    .pagination-wrap nav {
        width: 100%;
    }

    .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:first-child p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        white-space: nowrap;
    }

    .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:last-child {
        margin-left: auto;
    }

    .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
        display: none;
    }

    .pagination-wrap .pagination {
        display: flex;
        align-items: center;
        gap: 8px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .pagination-wrap .page-item {
        list-style: none;
    }
    .pagination-wrap .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        color: #475569;
        border: 1px solid #e2e8f0;
        background: #fff;
    }
    .pagination-wrap .page-item.disabled .page-link {
        color: #94a3b8;
        background: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .pagination-wrap .page-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .pagination-wrap .page-item.active .page-link {
        background: var(--green);
        color: #fff;
        border-color: var(--green);
    }
    .pagination-wrap svg {
        width: 14px;
        height: 14px;
    }

    @media (max-width: 900px) {
        .card-body {
            overflow-x: auto;
        }
        table {
            min-width: 760px;
        }
        .reviews-toolbar-inner {
            flex-direction: column;
            align-items: stretch;
        }
        .reviews-search-wrapper {
            width: 100%;
        }
        .filters-select {
            width: 100%;
            min-width: 0;
        }
    }
    @media (max-width: 640px) {
        .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
            display: none;
        }

        .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
    }
</style>
@endsection

@section('content')
    <div class="reviews-page">
        <div class="card">
            <div class="card-header reviews-header">
                <div>
                    <strong style="font-size:16px;color:#111827;">Customer Reviews</strong>
                    <div class="section-header">Student feedback for your concessionaire.</div>
                </div>
                <div class="reviews-stat-pills">
                    <span class="badge badge-secondary reviews-pill">
                        Avg {{ $averageRating !== null ? number_format((float) $averageRating, 1) : '0.0' }} &#9733;
                    </span>
                    <span class="badge badge-secondary reviews-pill">{{ number_format($totalReviews) }} Total Reviews</span>
                    <span class="badge badge-secondary reviews-pill">{{ $fiveStarShare }}% Five-Star Share</span>
                </div>
            </div>

            <div class="reviews-toolbar">
                <div class="reviews-toolbar-inner">
                    <div class="reviews-search-wrapper">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input id="reviewsInstantSearch" type="search" class="reviews-search-input" placeholder="Search reviews..." autocomplete="off">
                    </div>

                    <select id="filterReviewType" class="filters-select">
                        <option value="">All Types</option>
                        <option value="store">Store Reviews</option>
                        <option value="product">Product Reviews</option>
                    </select>

                    <select id="filterMinRating" class="filters-select">
                        <option value="">All Ratings</option>
                        <option value="1">&#11088; 1 Star</option>
                        <option value="2">&#11088;&#11088; 2 Stars</option>
                        <option value="3">&#11088;&#11088;&#11088; 3 Stars</option>
                        <option value="4">&#11088;&#11088;&#11088;&#11088; 4 Stars</option>
                        <option value="5">&#11088;&#11088;&#11088;&#11088;&#11088; 5 Stars</option>
                    </select>
                </div>
            </div>

            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Target</th>
                            <th>Comment</th>
                            <th>Date Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            @php
                                $rating = max(0, min(5, (int) ($review['rating'] ?? 0)));
                                $filled = str_repeat('&#9733;', $rating);
                                $empty = str_repeat('&#9734;', max(0, 5 - $rating));
                                $reviewerName = (string) ($review['reviewer_name'] ?? 'Anonymous');
                                $comment = trim((string) ($review['comment'] ?? ''));
                                $target = (string) ($review['target'] ?? 'Unknown');
                                $commentForSearch = strtolower($comment !== '' ? $comment : 'No comment');
                                $targetForSearch = strtolower($target);
                                $typeLabel = (string) ($review['type_label'] ?? 'Review');
                                $createdAt = $review['created_at'] ?? null;
                            @endphp
                            <tr data-review-row="1" data-target="{{ e($targetForSearch) }}" data-comment="{{ e($commentForSearch) }}" data-type="{{ e($review['type'] ?? '') }}" data-rating="{{ $rating }}">
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ strtoupper(substr($reviewerName, 0, 1)) }}</div>
                                        <div>
                                            <div class="user-name">{{ $reviewerName }}</div>
                                            <div class="user-email">{{ optional($createdAt)->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="stars">{!! $filled !!}{!! $empty !!}</span>
                                    <span class="table-meta">{{ $rating }}/5</span>
                                </td>
                                <td>
                                    <div class="user-name">{{ $target }}</div>
                                    <span class="table-meta">{{ $typeLabel }}</span>
                                </td>
                                <td class="comment-cell">{{ $comment !== '' ? $comment : 'No comment' }}</td>
                                <td>{{ optional($createdAt)->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">
                                    No reviews yet. Student feedback will appear here once submitted.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reviews->hasPages())
                <div class="pagination-wrap">
                    {{ $reviews->links('vendor.pagination.simple') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    (() => {
        const searchInput = document.getElementById('reviewsInstantSearch');
        const typeFilter = document.getElementById('filterReviewType');
        const ratingFilter = document.getElementById('filterMinRating');
        const tbody = document.querySelector('.reviews-page tbody');

        if (!searchInput || !tbody) {
            return;
        }

        const reviewRows = Array.from(tbody.querySelectorAll('tr[data-review-row="1"]'));
        if (!reviewRows.length) {
            return;
        }

        const noMatchRow = document.createElement('tr');
        noMatchRow.innerHTML = '<td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">No matching reviews found.</td>';
        noMatchRow.style.display = 'none';
        tbody.appendChild(noMatchRow);

        const applyAllFilters = () => {
            const searchQuery = (searchInput.value || '').trim().toLowerCase();
            const typeValue = typeFilter ? typeFilter.value.toLowerCase() : '';
            const ratingValue = ratingFilter ? ratingFilter.value : '';
            let visibleCount = 0;

            reviewRows.forEach((row) => {
                const target = row.dataset.target || '';
                const comment = row.dataset.comment || '';
                const rowType = row.dataset.type || '';
                const rowRating = row.dataset.rating || '';

                const searchMatch = searchQuery === '' || target.includes(searchQuery) || comment.includes(searchQuery);
                const typeMatch = typeValue === '' || rowType === typeValue;
                const ratingMatch = ratingValue === '' || rowRating === ratingValue;

                const matches = searchMatch && typeMatch && ratingMatch;

                row.style.display = matches ? '' : 'none';
                if (matches) {
                    visibleCount += 1;
                }
            });

            noMatchRow.style.display = visibleCount === 0 ? '' : 'none';
        };

        searchInput.addEventListener('input', applyAllFilters);
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        if (typeFilter) {
            typeFilter.addEventListener('change', applyAllFilters);
        }

        if (ratingFilter) {
            ratingFilter.addEventListener('change', applyAllFilters);
        }
    })();
</script>
@endsection
