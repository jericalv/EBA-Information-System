@extends('faculty.layout')

@section('title', 'Faculty Activity History')
@section('page-title', 'History')

@section('extra-css')
<style>
    .table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .table th, .table td { padding: 14px 18px; border-bottom: 1px solid #eef2f7; text-align: left; vertical-align: top; }
    .table th { background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; }
    .detail { font-size: 12px; color: #64748b; white-space: pre-wrap; }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Subject</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php
                            // Format action label
                            $actionLabel = match($log->action) {
                                'faculty_recommendation_submitted' => 'Recommendation Submitted',
                                'faculty_document_uploaded' => 'Document Uploaded',
                                'faculty_concessionaire_updated' => 'Concessionaire Updated',
                                default => ucwords(str_replace('_', ' ', $log->action)),
                            };

                            // Build human-readable details
                            $detailsText = '—';
                            if ($log->details && is_array($log->details)) {
                                if ($log->action === 'faculty_recommendation_submitted') {
                                    $recommendation = $log->details['faculty_recommendation'] ?? '';
                                    $notes = $log->details['faculty_notes'] ?? '';
                                    $recommendType = $recommendation === 'recommend_approval' ? 'Approval' : 'Rejection';
                                    $detailsText = "Recommended <strong>$recommendType</strong>";
                                    if ($notes) {
                                        $detailsText .= " with note: <em>" . htmlspecialchars($notes) . "</em>";
                                    }
                                } elseif ($log->action === 'faculty_document_uploaded') {
                                    $docType = $log->details['document_type'] ?? 'Document';
                                    $detailsText = "Uploaded <strong>" . htmlspecialchars(ucfirst($docType)) . "</strong>";
                                } elseif ($log->action === 'faculty_concessionaire_updated') {
                                    $detailsText = "Updated concessionaire record";
                                }
                            } elseif ($log->description) {
                                $detailsText = htmlspecialchars($log->description);
                            }
                        @endphp
                        <tr>
                            <td><strong>{{ $actionLabel }}</strong></td>
                            <td>
                                @php
                                    $subjectLabel = match($log->subject_type) {
                                        'uniform_stock'            => 'Stock Item',
                                        'partnership_application'  => 'Partnership Application',
                                        'concessionaire'           => 'Concessionaire',
                                        'product'                  => 'Product',
                                        default => ucwords(str_replace('_', ' ', $log->subject_type ?? '—')),
                                    };
                                @endphp
                                {{ $subjectLabel }}
                                @if ($log->subject_id)
                                #{{ $log->subject_id }}
                                @endif
                            </td>
                            <td>{!! $detailsText !!}</td>
                            <td>
                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                <div style="font-size:13px;color:#64748b;">{{ $log->created_at->format('g:i A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:#64748b;padding:24px;">No activity logs found for your account.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="pagination-wrap">
                {{ $logs->links('faculty.partials.pagination') }}
            </div>
        @endif
    </div>
@endsection
