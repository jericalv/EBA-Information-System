<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ConcessionairePayment;

class PartnershipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'business_name',
        'products_services',
        'student_benefit',
        'unique_selling_point',
        'expected_price_range',
        'phone_number',
        'business_proposal',
        'phone',
        'proposal',
        'letter_of_intent',
        'letter_of_intent_path',
        'letter_of_intent_paths',
        'loi_submitted_at',
        'wizard_status',
        'loi_rejection_reason',
        'form_rejection_reason',
        'docs_recommendation_checked',
        'docs_notice_occupy_checked',
        'docs_notice_termination_checked',
        'docs_moa_contract_checked',
        'receipt_path',
        'receipt_paths',
        'form_submitted_at',
        'receipt_submitted_at',
        'moa_path',
        'contract_path',
        'status',
        'admin_notes',
        'rejection_reason',
        'faculty_recommendation',
        'faculty_notes',
        'reviewed_by',
        'contract_period_start',
        'contract_period_end',
        'warning_30_sent',
        'warning_7_sent',
        'warning_1_sent',
        'address',
        'type_of_business',
        'proposed_location',
        'proposed_duration',
        'is_previous_concessionaire',
        'previous_location_year',
        'certification_agreed',
        'valid_id_path',
        'valid_id_paths',
        'business_permit_path',
        'business_permit_paths',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'loi_submitted_at' => 'datetime',
        'form_submitted_at' => 'datetime',
        'receipt_submitted_at' => 'datetime',
        'contract_period_start' => 'date',
        'contract_period_end' => 'date',
        'warning_30_sent' => 'boolean',
        'warning_7_sent' => 'boolean',
        'warning_1_sent' => 'boolean',
        'docs_recommendation_checked' => 'boolean',
        'docs_notice_occupy_checked' => 'boolean',
        'docs_notice_termination_checked' => 'boolean',
        'docs_moa_contract_checked' => 'boolean',
        'is_previous_concessionaire' => 'boolean',
        'certification_agreed' => 'boolean',
        'letter_of_intent_paths' => 'array',
        'valid_id_paths' => 'array',
        'business_permit_paths' => 'array',
        'receipt_paths' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ConcessionairePayment::class);
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function allPhysicalDocsChecked(): bool
    {
        return $this->docs_recommendation_checked
            && $this->docs_notice_occupy_checked
            && $this->docs_notice_termination_checked
            && $this->docs_moa_contract_checked;
    }

    public function wizardStepNumber(): int
    {
        return match ($this->normalizedWizardStatus()) {
            'loi_pending', 'loi_submitted', 'loi_rejected' => 1,
            'form_pending', 'form_submitted', 'form_rejected' => 2,
            'docs_in_progress', 'receipt_pending' => 3,
            'receipt_submitted', 'final_approved' => 4,
            default => 1,
        };
    }

    protected function normalizedWizardStatus(): string
    {
        return strtolower(trim((string) $this->wizard_status));
    }

    /**
     * Maximum number of pictures an applicant may upload per document.
     */
    public const MAX_DOCUMENT_FILES = 5;

    /**
     * Return the ordered list of stored paths for a document type, falling
     * back to the legacy single-path column when the JSON array is empty.
     *
     * @return array<int, string>
     */
    public function documentPaths(string $type): array
    {
        $arrayColumn = $type . '_paths';
        $singleColumn = $type . '_path';

        $paths = $this->{$arrayColumn};
        if (is_array($paths)) {
            $paths = array_values(array_filter($paths));
            if (! empty($paths)) {
                return $paths;
            }
        }

        $single = $this->{$singleColumn};
        if (! $single && $type === 'letter_of_intent') {
            $single = $this->letter_of_intent;
        }

        return $single ? [$single] : [];
    }

    public function wizardStepLabel(): string
    {
        return match ($this->wizardStepNumber()) {
            1 => 'Letter of Intent',
            2 => 'Application Form',
            3 => 'Office Requirements',
            4 => 'Payment Receipt',
            default => 'Letter of Intent',
        };
    }
}
