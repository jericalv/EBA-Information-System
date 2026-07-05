@extends('admin.layout')

@section('title', 'Reviews')

@section('extra-css')
<style>
    .reviews-overview {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .reviews-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .reviews-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }
    .reviews-card:nth-child(1)::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .reviews-card:nth-child(2)::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .reviews-card:nth-child(3)::before { background: linear-gradient(90deg, #f59e0b, #fb923c); }
    .reviews-card:nth-child(4)::before { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
    .reviews-card:nth-child(5)::before { background: linear-gradient(90deg, #ef4444, #f87171); }
    .reviews-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .reviews-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .reviews-card-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    .reviews-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .reviews-card:nth-child(1) .reviews-card-icon { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }
    .reviews-card:nth-child(2) .reviews-card-icon { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed; }
    .reviews-card:nth-child(3) .reviews-card-icon { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
    .reviews-card:nth-child(4) .reviews-card-icon { background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); color: #0d9488; }
    .reviews-card:nth-child(5) .reviews-card-icon { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
    .reviews-card-value {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 10px;
    }
    .reviews-card-note {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
    }
    @media (max-width: 1400px) {
        .reviews-overview {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 992px) {
        .reviews-overview {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 576px) {
        .reviews-overview {
            grid-template-columns: 1fr;
        }
    }

    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-warning { background: #ffedd5; color: #9a3412; }
    .badge-type-product { background: #dbeafe; color: #1d4ed8; }
    .badge-type-store { background: #ede9fe; color: #6d28d9; }

    .rating-value {
        font-weight: 700;
        color: #0f172a;
    }
    .rating-muted {
        color: #64748b;
        font-size: 13px;
    }
    .overall-score {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        color: #0f172a;
    }
    .stars {
        letter-spacing: 1px;
        color: #f59e0b;
        white-space: nowrap;
    }
    .comment-cell {
        max-width: 340px;
        color: #334155;
    }
    .table-meta {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
    }
    .actions-cell {
        white-space: nowrap;
    }
    .actions-cell form {
        display: inline;
    }
    .section-header {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }
    .filters-bar {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        width: 100%;
    }
    .filters-field {
        display: grid;
        gap: 6px;
        min-width: 170px;
        flex: 1;
    }
    .filters-field label {
        font-size: 12px;
        color: #334155;
        font-weight: 700;
    }
    .filters-input,
    .filters-select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        color: #1e293b;
        font: inherit;
        font-size: 14px;
    }
    .filters-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }
</style>
@endsection

@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <div>
                <strong style="font-size:16px;color:#111827;">Per-Concessionaire Ratings</strong>
                <div class="section-header">Sorted by overall score ascending (worst performing first).</div>
            </div>
            <span class="badge badge-pending">Approved Concessionaires: {{ $concessionaireRatings->count() }}</span>
        </div>
        <div class="card-header" style="border-bottom: 1px solid #f1f5f9;">
            <div class="filters-bar">
                <div class="filters-field" style="max-width:320px;flex:0 0 320px;">
                    <label for="concessionaireRatingsSearch">Search Business</label>
                    <input id="concessionaireRatingsSearch" type="text" class="filters-input" placeholder="Type a business name...">
                </div>
                <div class="filters-field" style="max-width:220px;flex:0 0 220px;">
                    <label for="concessionaireRatingsFlag">Flag Status</label>
                    <select id="concessionaireRatingsFlag" class="filters-select">
                        <option value="all">All</option>
                        <option value="healthy">Healthy</option>
                        <option value="low">Low Rating</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Business</th>
                        <th>Avg Product Rating</th>
                        <th>Product Reviews</th>
                        <th>Avg Store Rating</th>
                        <th>Store Reviews</th>
                        <th>Overall Score</th>
                        <th>Flag</th>
                    </tr>
                </thead>
                <tbody id="concessionaireRatingsBody">
                    @forelse ($concessionaireRatings as $row)
                        @php
                            $businessName = $row['concessionaire']->business_name ?: $row['concessionaire']->name;
                        @endphp
                        <tr data-concessionaire-row="1" data-business="{{ strtolower($businessName) }}" data-flag="{{ $row['needs_attention'] ? 'low' : 'healthy' }}">
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ $row['concessionaire']->initials() }}</div>
                                    <div>
                                        <div class="user-name">{{ $businessName }}</div>
                                        <div class="user-email">{{ $row['concessionaire']->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($row['avg_product_rating'] !== null)
                                    <span class="rating-value">{{ number_format((float) $row['avg_product_rating'], 1) }}/5</span>
                                @else
                                    <span class="rating-muted">No reviews</span>
                                @endif
                            </td>
                            <td>{{ $row['product_review_count'] }}</td>
                            <td>
                                @if ($row['avg_store_rating'] !== null)
                                    <span class="rating-value">{{ number_format((float) $row['avg_store_rating'], 1) }}/5</span>
                                @else
                                    <span class="rating-muted">No reviews</span>
                                @endif
                            </td>
                            <td>{{ $row['store_review_count'] }}</td>
                            <td>
                                @if ($row['overall_rating'] !== null)
                                    <span class="overall-score">{{ number_format((float) $row['overall_rating'], 1) }}/5</span>
                                @else
                                    <span class="rating-muted">No reviews</span>
                                @endif
                            </td>
                            <td>
                                @if ($row['needs_attention'])
                                    <span class="badge badge-warning">⚠ Low Rating</span>
                                @else
                                    <span class="badge badge-approved">Healthy</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:#94a3b8;">No approved concessionaires found.</td>
                        </tr>
                    @endforelse
                    <tr id="concessionaireRatingsEmpty" style="display:none;">
                        <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No concessionaires match your filters.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <div>
                <strong style="font-size:16px;color:#111827;">Recent Reviews Feed</strong>
                <div class="section-header">Most recent product and store reviews across the system.</div>
            </div>
            <span class="badge badge-secondary" id="recentReviewsCountBadge">Showing {{ $recentReviews->count() }} of {{ $recentReviews->total() }}</span>
        </div>
        <div class="card-header" style="border-bottom: 1px solid #f1f5f9;">
            <div class="filters-bar">
                <div class="filters-field" style="max-width:280px;flex:0 0 280px;">
                    <label for="filterCommentSearch">Search Comments</label>
                    <input id="filterCommentSearch" type="text" class="filters-input" placeholder="Search comments..." oninput="applyRecentFilters()">
                </div>

                <div class="filters-field" style="max-width:240px;flex:0 0 240px;">
                    <label for="filterConcessionaire">Concessionaire</label>
                    <select id="filterConcessionaire" class="filters-select" onchange="applyRecentFilters()">
                        <option value="">All</option>
                        @foreach ($approvedConcessionaires as $concessionaire)
                            <option value="{{ $concessionaire->id }}">
                                {{ $concessionaire->business_name ?: $concessionaire->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filters-field" style="max-width:210px;flex:0 0 210px;">
                    <label for="filterType">Review Type</label>
                    <select id="filterType" class="filters-select" onchange="applyRecentFilters()">
                        <option value="">All</option>
                        <option value="product">Product Review</option>
                        <option value="store">Store Review</option>
                    </select>
                </div>

                <div class="filters-field" style="max-width:220px;flex:0 0 220px;">
                    <label for="filterMinRating">Rating</label>
                    <select id="filterMinRating" class="filters-select" onchange="applyRecentFilters()">
                        <option value="">All</option>
                        <option value="1">1 Star</option>
                        <option value="2">2 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="5">5 Stars</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Reviewer</th>
                        <th>Target</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="recentReviewsBody">
                    @forelse ($recentReviews as $review)
                        @php
                            $rating = (int) ($review['rating'] ?? 0);
                            $filled = str_repeat('★', max(0, min(5, $rating)));
                            $empty = str_repeat('☆', max(0, 5 - max(0, min(5, $rating))));
                            $comment = trim((string) ($review['comment'] ?? ''));
                            $commentPreview = $comment !== '' ? \Illuminate\Support\Str::limit($comment, 80) : 'No comment';
                        @endphp
                        <tr data-comment="{{ strtolower($comment) }}"
                            data-concessionaire="{{ $review['concessionaire_id'] ?? '' }}"
                            data-type="{{ $review['type'] ?? '' }}"
                            data-rating="{{ $review['rating'] ?? 0 }}">
                            <td>
                                <span class="badge {{ $review['type'] === 'product' ? 'badge-type-product' : 'badge-type-store' }}">
                                    {{ $review['type_label'] }}
                                </span>
                            </td>
                            <td>{{ $review['reviewer_name'] }}</td>
                            <td>
                                <span class="rating-value">{{ $review['target_name'] }}</span>
                                @if (! empty($review['target_meta']))
                                    <span class="table-meta">Store: {{ $review['target_meta'] }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="stars">{{ $filled }}{{ $empty }}</span>
                                <span class="table-meta">{{ $rating }}/5</span>
                            </td>
                            <td class="comment-cell" title="{{ $comment !== '' ? $comment : 'No comment' }}">{{ $commentPreview }}</td>
                            <td>{{ optional($review['created_at'])->format('M d, Y h:i A') }}</td>
                            <td class="actions-cell">
                                <form method="POST" action="{{ $review['type'] === 'product' ? route('admin.reviews.delete-product', $review['id']) : route('admin.reviews.delete-store', $review['id']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-red btn-sm" onclick="return confirm('Remove this review? This action cannot be undone.');">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:#94a3b8;">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($recentReviews->hasPages())
            <div class="pagination-wrap">
                {{ $recentReviews->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    (function () {
        const ratingsSearchInput = document.getElementById('concessionaireRatingsSearch');
        const ratingsFlagSelect = document.getElementById('concessionaireRatingsFlag');
        const ratingRows = Array.from(document.querySelectorAll('tr[data-concessionaire-row="1"]'));
        const ratingsEmptyRow = document.getElementById('concessionaireRatingsEmpty');

        const applyConcessionaireFilters = () => {
            if (!ratingRows.length) {
                return;
            }

            const searchTerm = (ratingsSearchInput?.value || '').trim().toLowerCase();
            const selectedFlag = ratingsFlagSelect?.value || 'all';
            let visibleCount = 0;

            ratingRows.forEach((row) => {
                const business = (row.dataset.business || '').toLowerCase();
                const flag = row.dataset.flag || 'healthy';

                const matchesSearch = !searchTerm || business.includes(searchTerm);
                const matchesFlag = selectedFlag === 'all' || flag === selectedFlag;
                const isVisible = matchesSearch && matchesFlag;

                row.style.display = isVisible ? '' : 'none';
                if (isVisible) {
                    visibleCount += 1;
                }
            });

            if (ratingsEmptyRow) {
                ratingsEmptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        };

        if (ratingsSearchInput) {
            ratingsSearchInput.addEventListener('input', applyConcessionaireFilters);
        }
        if (ratingsFlagSelect) {
            ratingsFlagSelect.addEventListener('change', applyConcessionaireFilters);
        }

        applyConcessionaireFilters();

        window.applyRecentFilters = function () {
            const commentInput = document.getElementById('filterCommentSearch');
            const concessionaireSelect = document.getElementById('filterConcessionaire');
            const typeSelect = document.getElementById('filterType');
            const ratingSelect = document.getElementById('filterMinRating');
            const tbody = document.getElementById('recentReviewsBody');
            const countBadge = document.getElementById('recentReviewsCountBadge');

            if (!tbody) { return; }

            const commentQuery = (commentInput?.value || '').trim().toLowerCase();
            const selectedConcessionaire = concessionaireSelect?.value || '';
            const selectedType = typeSelect?.value || '';
            const selectedRating = ratingSelect?.value || '';

            const rows = Array.from(tbody.querySelectorAll('tr[data-type]'));
            let visibleCount = 0;

            rows.forEach((row) => {
                const comment = row.dataset.comment || '';
                const concessionaire = row.dataset.concessionaire || '';
                const type = row.dataset.type || '';
                const rating = row.dataset.rating || '';

                const matchesComment = !commentQuery || comment.includes(commentQuery);
                const matchesConcessionaire = !selectedConcessionaire || concessionaire === selectedConcessionaire;
                const matchesType = !selectedType || type === selectedType;
                const matchesRating = !selectedRating || rating === selectedRating;

                const isVisible = matchesComment && matchesConcessionaire && matchesType && matchesRating;
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) { visibleCount += 1; }
            });

            if (countBadge) {
                countBadge.textContent = 'Showing ' + visibleCount + ' of ' + rows.length;
            }
        };

        window.applyRecentFilters();
    })();
</script>
@endsection
