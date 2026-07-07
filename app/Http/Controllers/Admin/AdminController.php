<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminDocumentUploadedMail;
use App\Mail\ContractPeriodSavedMail;
use App\Mail\PartnershipApprovedMail;
use App\Mail\PartnershipRejectedMail;
use App\Mail\PartnershipStepApprovedMail;
use App\Mail\StaffWelcomeMail;
use App\Models\ConcessionaireReview;
use App\Models\ConcessionairePayment;
use App\Models\ActivityLog;
use App\Models\PartnershipApplication;
use App\Models\ProductReview;
use App\Models\SalesOrder;
use App\Models\UniformStock;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function getUnreadApplicationSteps(): Collection
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'admin') {
            return collect();
        }

        if (! Schema::hasColumns('partnership_applications', [
            'loi_submitted_at',
            'form_submitted_at',
            'receipt_submitted_at',
        ])) {
            return collect();
        }

        if (! Schema::hasColumn('users', 'application_notifications_read_at')) {
            return collect();
        }

        $readAt = $user->application_notifications_read_at;

        $applications = PartnershipApplication::query()
            ->with(['user:id,name,business_name'])
            ->select([
                'id',
                'user_id',
                'business_name',
                'first_name',
                'last_name',
                'loi_submitted_at',
                'form_submitted_at',
                'receipt_submitted_at',
            ])
            ->where(function ($query) {
                $query->whereNotNull('loi_submitted_at')
                    ->orWhereNotNull('form_submitted_at')
                    ->orWhereNotNull('receipt_submitted_at');
            })
            ->when($readAt, function ($query) use ($readAt) {
                $query->where(function ($innerQuery) use ($readAt) {
                    $innerQuery->where('loi_submitted_at', '>', $readAt)
                        ->orWhere('form_submitted_at', '>', $readAt)
                        ->orWhere('receipt_submitted_at', '>', $readAt);
                });
            })
            ->get();

        $events = collect();

        foreach ($applications as $application) {
            $concessionaireName = $application->business_name
                ?: $application->user?->business_name
                ?: $application->user?->name
                ?: trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? ''))
                ?: 'Concessionaire';

            $steps = [
                ['key' => 'loi_submitted_at', 'label' => 'Submitted Letter of Intent'],
                ['key' => 'form_submitted_at', 'label' => 'Submitted Application Form'],
                ['key' => 'receipt_submitted_at', 'label' => 'Submitted Receipt'],
            ];

            foreach ($steps as $step) {
                $submittedAt = $application->{$step['key']};

                if (! $submittedAt) {
                    continue;
                }

                if ($readAt && $submittedAt->lte($readAt)) {
                    continue;
                }

                $events->push([
                    'application_id' => $application->id,
                    'concessionaire_name' => $concessionaireName,
                    'step_label' => $step['label'],
                    'submitted_at' => $submittedAt,
                ]);
            }
        }

        return $events
            ->sortByDesc('submitted_at')
            ->values();
    }

    public function markApplicationNotificationsRead(Request $request)
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            abort(403);
        }

        if (! Schema::hasColumn('users', 'application_notifications_read_at')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return back();
        }

        $user->forceFill([
            'application_notifications_read_at' => now(),
        ])->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    protected function adminView(string $view, array $data = [])
    {
        $unreadApplicationSteps = $this->getUnreadApplicationSteps();

        return view($view, array_merge($data, [
            'unreadApplicationSteps' => $unreadApplicationSteps->take(10),
            'unreadApplicationCount' => $unreadApplicationSteps->count(),
        ]));
    }

    /**
     * Admin dashboard with overview stats.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'cashiers' => User::where('role', 'cashier')->count(),
            'pending_partnerships' => PartnershipApplication::where('status', 'pending')->count(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        $applicationStatusData = [
            'pending' => PartnershipApplication::where('status', 'pending')->count(),
            'approved' => PartnershipApplication::where('status', 'approved')->count(),
            'rejected' => PartnershipApplication::where('status', 'rejected')->count(),
        ];

        $monthlyPaymentsData = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);

            return [
                'month' => $month->format('M Y'),
                'total' => ConcessionairePayment::whereYear('payment_date', $month->year)
                    ->whereMonth('payment_date', $month->month)
                    ->sum('amount'),
            ];
        })->values()->toArray();

        $applicationsTrendData = collect(range(6, 0))->map(function ($i) {
            $month = now()->subMonths($i);

            return [
                'month' => $month->format('M Y'),
                'count' => PartnershipApplication::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        })->values()->toArray();

        $topConcessionairesData = User::where('role', 'concessionaire')
            ->where('is_approved', true)
            ->get()
            ->map(function ($user) {
                $storeReviews = ConcessionaireReview::where('concessionaire_id', $user->id)->count();
                $productReviews = ProductReview::whereHas('product', function ($q) use ($user) {
                    $q->where('concessionaire_id', $user->id);
                })->count();

                return [
                    'name' => $user->business_name ?: $user->name,
                    'reviews' => $storeReviews + $productReviews,
                ];
            })
            ->sortByDesc('reviews')
            ->take(10)
            ->values()
            ->toArray();

        return $this->adminView('admin.dashboard', compact(
            'stats',
            'applicationStatusData',
            'monthlyPaymentsData',
            'applicationsTrendData',
            'topConcessionairesData'
        ));
    }

    /**
     * List all users.
     */
    public function users(Request $request)
    {
        $query = User::query();
        $sort = strtolower((string) $request->query('sort', 'desc'));

        if (! in_array($sort, ['asc', 'desc'], true)) {
            $sort = 'desc';
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('name', $sort)->paginate(15)->withQueryString();

        return $this->adminView('admin.users', compact('users'));
    }

    /**
     * Create a staff account from the admin panel.
     */
    public function createStaffAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', Rule::in(['cashier', 'faculty'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_approved' => true,
            'is_active_concessionaire' => false,
        ]);

        ActivityLog::log(
            'staff_account_created',
            'user',
            (string) $user->id,
            "Admin created staff account for {$user->name} as {$user->role}",
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_by' => Auth::id(),
            ]
        );

        try {
            Mail::to($user->email)->send(new StaffWelcomeMail($user, $validated['password']));
        } catch (\Exception $e) {
            Log::warning('Staff welcome mail failed: ' . $e->getMessage(), [
                'controller' => self::class,
                'user_id' => $user->id,
                'recipient' => $user->email,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Staff account created for {$user->name}.",
        ]);
    }

    /**
     * Update a user's role.
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'cashier', 'concessionaire', 'student', 'faculty'])],
        ]);

        // Prevent removing your own admin access
        if ((int) $user->getKey() === (int) Auth::id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    /**
     * Update a concessionaire business name.
     */
    public function updateBusinessName(Request $request, User $user)
    {
        if ($user->role !== 'concessionaire') {
            return back()->with('error', 'Business name can only be set for concessionaire accounts.');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
        ]);

        $previousName = $user->business_name;
        $user->update([
            'business_name' => $validated['business_name'],
        ]);

        ActivityLog::log(
            'concessionaire_business_name_updated',
            'user',
            (string) $user->id,
            "Updated concessionaire business name for {$user->name}",
            [
                'user_id' => $user->id,
                'old_business_name' => $previousName,
                'new_business_name' => $user->business_name,
            ]
        );

        return back()->with('success', "Business name updated for {$user->name}.");
    }

    /**
     * Delete a user.
     */
    public function destroyUser(User $user)
    {
        // Prevent self-deletion
        if ((int) $user->getKey() === (int) Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->concessionairePayments()->delete();
        $user->delete();

        return back()->with('success', "{$name} has been deleted.");
    }

    /**
     * List all partnership applications.
     */
    public function partnerships(Request $request)
    {
        try {
            DB::table('partnership_applications')
                ->whereIn('status', ['approved', 'registered'])
                ->where('contract_period_edit_count', '>', 0)
                ->whereNull('contract_period_start')
                ->whereNull('contract_period_end')
                ->update([
                    'contract_period_edit_count' => 0,
                    'contract_period_last_edited_year' => null,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Contract period counter reset failed on partnerships listing: ' . $e->getMessage(), [
                'controller' => self::class,
                'admin_id' => Auth::id(),
            ]);
        }

        $query = PartnershipApplication::query()->with([
            'user:id,role,is_active_concessionaire,is_approved',
            'reviewer:id,name',
        ]);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'pending' => PartnershipApplication::where('status', 'pending')->count(),
            'approved' => PartnershipApplication::where('status', 'approved')->count(),
            'rejected' => PartnershipApplication::where('status', 'rejected')->count(),
            'registered' => PartnershipApplication::where('status', 'registered')->count(),
            'expired' => PartnershipApplication::where('status', 'expired')->count(),
        ];

        return $this->adminView('admin.partnerships', compact('applications', 'stats'));
    }

    /**
     * Reject a partnership application.
     */
    public function rejectPartnership(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $application = PartnershipApplication::findOrFail($id);

        if (! in_array($application->status, ['pending', 'under_review'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending applications can be rejected.',
            ], 422);
        }

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $user = $application->user_id
            ? User::find($application->user_id)
            : null;

        if ($user) {
            $user->update([
                'is_approved' => false,
            ]);

            if ($this->shouldSendPartnershipUpdateEmail($application)) {
                $this->sendMail($user->email, new PartnershipRejectedMail($user, $validated['rejection_reason']));
            }
        }

        $detailsMessage = 'Application rejected. Reason: ' . $validated['rejection_reason'];

        ActivityLog::log(
            'partnership_rejected',
            'partnership',
            (string) $application->id,
            $detailsMessage,
            [
                'message' => $detailsMessage,
                'application_id' => $application->id,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function wizardApproveLOI(PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'loi_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $application->update([
                'wizard_status' => 'form_pending',
            ]);

            ActivityLog::log(
                'wizard_loi_approved',
                'partnership_application',
                (string) $application->id,
                'Wizard LOI approved by admin.'
            );

            if ($this->shouldSendPartnershipUpdateEmail($application)) {
                $this->sendMail($application->email, new PartnershipStepApprovedMail(
                    $application->fresh(),
                    'Step 1 of 4 — Letter of Intent',
                    'Your Letter of Intent has been approved',
                    'You may now proceed to fill out and submit your Application Form with your business details.',
                    'Step 2 — Application Form'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Letter of Intent approved. Concessionaire can now fill the application form.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardRejectLOI(Request $request, PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'loi_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            $application->update([
                'wizard_status' => 'loi_rejected',
                'loi_rejection_reason' => $request->reason,
            ]);

            ActivityLog::log(
                'wizard_loi_rejected',
                'partnership_application',
                (string) $application->id,
                'Wizard LOI rejected by admin.',
                [
                    'reason' => $request->reason,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Letter of Intent rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardApproveForm(PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'form_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $application->update([
                'wizard_status' => 'docs_in_progress',
            ]);

            ActivityLog::log(
                'wizard_form_approved',
                'partnership_application',
                (string) $application->id,
                'Wizard application form approved by admin.'
            );

            if ($this->shouldSendPartnershipUpdateEmail($application)) {
                $this->sendMail($application->email, new PartnershipStepApprovedMail(
                    $application->fresh(),
                    'Step 2 of 4 — Application Form',
                    'Your Application Form has been approved',
                    'The EBA Office will now prepare and verify your office requirements (recommendation, notices, and MOA/Contract). You will be notified again once these are complete and you can proceed to payment.',
                    'Step 3 — Office Requirements'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Application form approved. Track physical documents next.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardRejectForm(Request $request, PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'form_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            $application->update([
                'wizard_status' => 'form_rejected',
                'form_rejection_reason' => $request->reason,
            ]);

            ActivityLog::log(
                'wizard_form_rejected',
                'partnership_application',
                (string) $application->id,
                'Wizard application form rejected by admin.',
                [
                    'reason' => $request->reason,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Application form rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardTickDoc(Request $request, PartnershipApplication $application)
    {
        try {
            if (! in_array($application->wizard_status, ['docs_in_progress', 'receipt_pending'], true)) {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $request->validate([
                'doc' => 'required|in:recommendation,notice_occupy,notice_termination,moa_contract',
                'checked' => 'required|boolean',
            ]);

            $columnMap = [
                'recommendation' => 'docs_recommendation_checked',
                'notice_occupy' => 'docs_notice_occupy_checked',
                'notice_termination' => 'docs_notice_termination_checked',
                'moa_contract' => 'docs_moa_contract_checked',
            ];

            $column = $columnMap[$request->doc];

            $application->update([
                $column => $request->boolean('checked'),
            ]);
            $application->refresh();

            $advancedToReceiptPending = false;
            if ($application->allPhysicalDocsChecked() && $application->wizard_status === 'docs_in_progress') {
                $application->update(['wizard_status' => 'receipt_pending']);
                $advancedToReceiptPending = true;
            } elseif (! $application->allPhysicalDocsChecked() && $application->wizard_status === 'receipt_pending') {
                $application->update(['wizard_status' => 'docs_in_progress']);
            }
            $application->refresh();

            if ($advancedToReceiptPending && $this->shouldSendPartnershipUpdateEmail($application)) {
                $this->sendMail($application->email, new PartnershipStepApprovedMail(
                    $application->fresh(),
                    'Step 3 of 4 — Office Requirements',
                    'Your office requirements are complete',
                    'All required documents have been received and verified by the EBA Office. You may now proceed to upload your official payment receipt to finish your application.',
                    'Step 4 — Payment Receipt'
                ));
            }

            ActivityLog::log(
                'wizard_doc_ticked',
                'partnership_application',
                (string) $application->id,
                'Wizard physical document checklist updated by admin.',
                [
                    'doc' => $request->doc,
                    'checked' => $request->boolean('checked'),
                ]
            );

            return response()->json([
                'success' => true,
                'all_docs_checked' => $application->allPhysicalDocsChecked(),
                'wizard_status' => $application->wizard_status,
                'docs' => [
                    'recommendation' => $application->docs_recommendation_checked,
                    'notice_occupy' => $application->docs_notice_occupy_checked,
                    'notice_termination' => $application->docs_notice_termination_checked,
                    'moa_contract' => $application->docs_moa_contract_checked,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardFinalApprove(PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'receipt_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $application->update([
                'wizard_status' => 'final_approved',
                'status' => 'approved',
            ]);

            if ($application->user) {
                $userUpdate = [
                    'is_approved' => true,
                    'is_active_concessionaire' => true,
                ];
                if ($application->business_name) {
                    $userUpdate['business_name'] = $application->business_name;
                }
                $application->user->update($userUpdate);
            }

            ActivityLog::log(
                'partnership_approved',
                'partnership',
                (string) $application->id,
                "Approved partnership application #{$application->id} via wizard final approval",
                [
                    'application_id' => $application->id,
                    'flow' => 'wizard',
                ]
            );

            try {
                if ($application->user) {
                    Mail::to($application->user->email)->send(new PartnershipApprovedMail($application));
                }
            } catch (\Exception $mailEx) {
                Log::warning('PartnershipApprovedMail failed: ' . $mailEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Application fully approved. Concessionaire can now access their dashboard.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function viewPartnershipDocument(Request $request, PartnershipApplication $application, string $type)
    {
        $documentMap = [
            'letter_of_intent' => ['letter_of_intent_path', 'letter_of_intent'],
            'receipt' => ['receipt_path', null],
            'moa' => ['moa_path', null],
            'contract' => ['contract_path', null],
        ];

        if (! array_key_exists($type, $documentMap)) {
            abort(404);
        }

        [$primaryColumn, $fallbackColumn] = $documentMap[$type];
        $sourceColumns = [$type . '_paths', $primaryColumn];
        if ($fallbackColumn) {
            $sourceColumns[] = $fallbackColumn;
        }

        $candidates = $this->resolveDocumentPathCandidates($application, $type, $sourceColumns);

        // When a specific picture index is requested, serve exactly that file.
        $requestedIndex = $request->query('index');
        if ($requestedIndex !== null) {
            $target = $candidates[(int) $requestedIndex] ?? null;
            if ($target && Storage::disk('public')->exists($target)) {
                return response()->file(Storage::disk('public')->path($target));
            }
            abort(404, 'Document file missing from storage.');
        }

        foreach ($candidates as $resolvedPath) {
            if (! Storage::disk('public')->exists($resolvedPath)) {
                continue;
            }

            $updates = [];
            if ($application->{$primaryColumn} !== $resolvedPath) {
                $updates[$primaryColumn] = $resolvedPath;
            }

            if ($type === 'letter_of_intent' && $fallbackColumn && $application->{$fallbackColumn} !== $resolvedPath) {
                $updates[$fallbackColumn] = $resolvedPath;
            }

            if (! empty($updates)) {
                $application->forceFill($updates)->save();
            }

            return response()->file(Storage::disk('public')->path($resolvedPath));
        }

        abort(404, 'Document file missing from storage.');
    }

    private function resolveDocumentPathCandidates(PartnershipApplication $application, string $type, array $sourceColumns): array
    {
        $candidates = [];

        foreach ($sourceColumns as $column) {
            $rawPath = $application->{$column};
            if (! $rawPath) {
                continue;
            }

            $values = is_array($rawPath) ? $rawPath : [$rawPath];
            foreach ($values as $value) {
                if (! $value) {
                    continue;
                }
                $candidates = array_merge($candidates, $this->normalizeStoragePathCandidates((string) $value));
            }
        }

        // Fallback: discover files in likely folders if DB path became stale.
        $possibleFolders = array_values(array_unique(array_filter([
            $application->user_id,
            $application->id,
        ])));

        foreach ($possibleFolders as $folder) {
            $directory = 'partnership_letters/' . $folder;

            foreach (Storage::disk('public')->files($directory) as $file) {
                if ($this->documentFileMatchesType($file, $type)) {
                    $candidates[] = $file;
                }
            }
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function normalizeStoragePathCandidates(string $path): array
    {
        $trimmed = ltrim($path, '/');
        $candidates = [$trimmed];

        if (str_starts_with($trimmed, 'public/')) {
            $candidates[] = substr($trimmed, 7);
        }

        if (str_starts_with($trimmed, 'storage/')) {
            $candidates[] = substr($trimmed, 8);
        }

        if (str_starts_with($trimmed, 'app/public/')) {
            $candidates[] = substr($trimmed, 11);
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsedPath = (string) parse_url($path, PHP_URL_PATH);
            if ($parsedPath !== '') {
                $candidates = array_merge($candidates, $this->normalizeStoragePathCandidates($parsedPath));
            }
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function documentFileMatchesType(string $path, string $type): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        return match ($type) {
            'letter_of_intent' => str_starts_with($filename, 'letter_of_intent'),
            'receipt' => str_starts_with($filename, 'receipt'),
            'moa' => $filename === 'moa' || str_starts_with($filename, 'moa_'),
            'contract' => str_starts_with($filename, 'contract'),
            default => false,
        };
    }

    /**
     * Delete a partnership application.
     */
    public function destroyPartnership(PartnershipApplication $application)
    {
        // Delete the uploaded file if exists
        $documents = array_filter([
            $application->letter_of_intent_path,
            $application->letter_of_intent,
            $application->moa_path,
            $application->contract_path,
        ]);

        foreach ($documents as $path) {
            Storage::disk('public')->delete($path);
        }

        $application->delete();

        return back()->with('success', 'Partnership application deleted.');
    }

    /**
     * Display all concessionaire payments.
     */
    public function paymentsIndex(Request $request)
    {
        $query = ConcessionairePayment::query()->with([
            'concessionaire:id,name,email,business_name',
            'recordedBy:id,name',
        ]);

        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->withCount([
                'concessionairePayments as current_month_payment_count' => function ($paymentQuery) use ($currentMonthStart, $currentMonthEnd) {
                    $paymentQuery->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd]);
                },
            ])
            ->withSum('concessionairePayments as total_paid', 'amount')
            ->withMax('concessionairePayments as last_payment_date', 'payment_date')
            ->orderBy('business_name')
            ->orderBy('name')
            ->get();

        $today = now()->day;
        $overdueCount = $concessionaires->filter(function (User $concessionaire) use ($today) {
            $monthlyFee = (float) ($concessionaire->monthly_fee ?? 0);
            $hasPaidThisMonth = (int) ($concessionaire->current_month_payment_count ?? 0) > 0;

            if ($monthlyFee <= 0 || $hasPaidThisMonth) {
                return false;
            }

            return $today >= 1 && $today < 25;
        })->count();

        $totalCollected = (clone $query)->sum('amount');
        $payments = $query->latest('payment_date')->latest('id')->paginate(20);

        return $this->adminView('admin.payments', compact(
            'payments',
            'totalCollected',
            'concessionaires',
            'overdueCount'
        ));
    }

    /**
     * Display reviews overview and moderation page.
     */
    public function reviewsIndex(Request $request)
    {
        $approvedConcessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->orderByRaw('COALESCE(NULLIF(business_name, ""), name) asc')
            ->get(['id', 'name', 'business_name', 'email']);

        $productReviews = ProductReview::query()
            ->with([
                'user:id,name',
                'product:id,name,concessionaire_id',
                'product.concessionaire:id,name,business_name',
            ])
            ->latest()
            ->get();

        $storeReviews = ConcessionaireReview::query()
            ->with([
                'user:id,name',
                'concessionaire:id,name,business_name',
            ])
            ->latest()
            ->get();

        $productReviewsByConcessionaire = $productReviews
            ->filter(fn (ProductReview $review) => $review->product && $review->product->concessionaire_id)
            ->groupBy(fn (ProductReview $review) => (int) $review->product->concessionaire_id);

        $storeReviewsByConcessionaire = $storeReviews
            ->groupBy(fn (ConcessionaireReview $review) => (int) $review->concessionaire_id);

        $concessionaireRatings = $approvedConcessionaires
            ->map(function (User $concessionaire) use ($productReviewsByConcessionaire, $storeReviewsByConcessionaire) {
                $productReviewSet = $productReviewsByConcessionaire->get($concessionaire->id, collect());
                $storeReviewSet = $storeReviewsByConcessionaire->get($concessionaire->id, collect());

                $productReviewCount = $productReviewSet->count();
                $storeReviewCount = $storeReviewSet->count();

                $avgProductRating = $productReviewCount > 0 ? round((float) $productReviewSet->avg('rating'), 1) : null;
                $avgStoreRating = $storeReviewCount > 0 ? round((float) $storeReviewSet->avg('rating'), 1) : null;

                $availableScores = collect([$avgProductRating, $avgStoreRating])
                    ->filter(fn ($score) => $score !== null);

                $overallRating = $availableScores->isNotEmpty()
                    ? round((float) $availableScores->avg(), 1)
                    : null;

                return [
                    'concessionaire' => $concessionaire,
                    'avg_product_rating' => $avgProductRating,
                    'product_review_count' => $productReviewCount,
                    'avg_store_rating' => $avgStoreRating,
                    'store_review_count' => $storeReviewCount,
                    'overall_rating' => $overallRating,
                    'needs_attention' => $overallRating !== null && $overallRating < 3.0,
                ];
            })
            // Keep unrated concessionaires at the bottom while sorting low-rated first.
            ->sortBy(fn (array $row) => $row['overall_rating'] ?? 99)
            ->values();

        $recentProductReviews = $productReviews->map(function (ProductReview $review) {
            $targetConcessionaire = $review->product?->concessionaire;

            return [
                'id' => $review->id,
                'type' => 'product',
                'type_label' => 'Product Review',
                'concessionaire_id' => $review->product?->concessionaire_id ? (int) $review->product->concessionaire_id : null,
                'reviewer_name' => $review->user?->name ?? 'Deleted User',
                'target_name' => $review->product?->name ?? 'Deleted Product',
                'target_meta' => $targetConcessionaire ? ($targetConcessionaire->business_name ?: $targetConcessionaire->name) : null,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at,
            ];
        });

        $recentStoreReviews = $storeReviews->map(function (ConcessionaireReview $review) {
            $targetConcessionaire = $review->concessionaire;

            return [
                'id' => $review->id,
                'type' => 'store',
                'type_label' => 'Store Review',
                'concessionaire_id' => (int) $review->concessionaire_id,
                'reviewer_name' => $review->user?->name ?? 'Deleted User',
                'target_name' => $targetConcessionaire ? ($targetConcessionaire->business_name ?: $targetConcessionaire->name) : 'Deleted Store',
                'target_meta' => null,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at,
            ];
        });

        $allRecentReviews = $recentProductReviews
            ->concat($recentStoreReviews)
            ->sortByDesc('created_at')
            ->values();

        $filteredRecentReviews = $allRecentReviews->values();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('page');
        $recentReviews = new LengthAwarePaginator(
            $filteredRecentReviews->forPage($currentPage, $perPage)->values(),
            $filteredRecentReviews->count(),
            $perPage,
            $currentPage,
            [
                'path' => route('admin.reviews'),
                'query' => $request->query(),
            ]
        );

        $totalProductReviews = $productReviews->count();
        $totalStoreReviews = $storeReviews->count();
        $averageProductRating = $totalProductReviews > 0 ? round((float) $productReviews->avg('rating'), 1) : null;
        $averageStoreRating = $totalStoreReviews > 0 ? round((float) $storeReviews->avg('rating'), 1) : null;
        $needsAttentionCount = $concessionaireRatings
            ->where('needs_attention', true)
            ->count();

        return $this->adminView('admin.reviews', compact(
            'totalProductReviews',
            'totalStoreReviews',
            'averageProductRating',
            'averageStoreRating',
            'needsAttentionCount',
            'concessionaireRatings',
            'approvedConcessionaires',
            'recentReviews'
        ));
    }

    /**
     * Delete a product review as admin moderation action.
     */
    public function deleteProductReview(int $id)
    {
        $review = ProductReview::query()
            ->with([
                'user:id,name',
                'product:id,name,concessionaire_id',
                'product.concessionaire:id,name,business_name',
            ])
            ->findOrFail($id);

        $reviewerName = $review->user?->name ?? 'Deleted User';
        $productName = $review->product?->name ?? 'Deleted Product';
        $storeName = $review->product?->concessionaire
            ? ($review->product->concessionaire->business_name ?: $review->product->concessionaire->name)
            : null;

        $review->delete();

        try {
            ActivityLog::log(
                'admin_review_deleted',
                'product_review',
                (string) $id,
                'Admin removed a product review',
                [
                    'review_type' => 'product',
                    'review_id' => $id,
                    'reviewer_name' => $reviewerName,
                    'target_name' => $productName,
                    'target_store' => $storeName,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Activity log failed for product review deletion: ' . $e->getMessage(), [
                'controller' => self::class,
                'review_id' => $id,
            ]);
        }

        return back()->with('success', 'Product review removed successfully.');
    }

    /**
     * Delete a store review as admin moderation action.
     */
    public function deleteStoreReview(int $id)
    {
        $review = ConcessionaireReview::query()
            ->with([
                'user:id,name',
                'concessionaire:id,name,business_name',
            ])
            ->findOrFail($id);

        $reviewerName = $review->user?->name ?? 'Deleted User';
        $storeName = $review->concessionaire
            ? ($review->concessionaire->business_name ?: $review->concessionaire->name)
            : 'Deleted Store';

        $review->delete();

        try {
            ActivityLog::log(
                'admin_review_deleted',
                'store_review',
                (string) $id,
                'Admin removed a store review',
                [
                    'review_type' => 'store',
                    'review_id' => $id,
                    'reviewer_name' => $reviewerName,
                    'target_name' => $storeName,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Activity log failed for store review deletion: ' . $e->getMessage(), [
                'controller' => self::class,
                'review_id' => $id,
            ]);
        }

        return back()->with('success', 'Store review removed successfully.');
    }

    public function updateMonthlyFee(Request $request, User $user)
    {
        if ($user->role !== 'concessionaire') {
            return back()->with('error', 'Monthly fee can only be set for concessionaire accounts.');
        }

        $validated = $request->validate([
            'monthly_fee' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $user->update([
            'monthly_fee' => $validated['monthly_fee'],
        ]);

        $businessName = $user->business_name ?: $user->name;

        return back()->with('success', "Monthly fee updated for {$businessName}.");
    }

    /**
     * Save contract period updates for partnership applications.
     */
    public function saveContractPeriod(Request $request, PartnershipApplication $application)
    {
        $editableStatuses = ['pending', 'under_review', 'approved', 'registered'];
        if (! in_array($application->status, $editableStatuses, true)) {
            return back()->withErrors([
                'contract_period' => 'Contract period can only be edited for pending, under review, approved, or registered applications.',
            ]);
        }

        $validated = $request->validate([
            'contract_period_start' => 'required|date',
            'contract_period_end' => 'required|date|after_or_equal:contract_period_start',
        ]);

        $application->contract_period_start = $validated['contract_period_start'];
        $application->contract_period_end = $validated['contract_period_end'];
        $application->save();

        $freshApplication = $application->fresh();
        if (! $freshApplication || ! $freshApplication->contract_period_start || ! $freshApplication->contract_period_end) {
            Log::warning('Contract period save verification failed: dates still null after save.', [
                'controller' => self::class,
                'application_id' => $application->id,
                'admin_id' => Auth::id(),
            ]);

            return back()->with('error', 'Contract period could not be saved. Please try again.');
        }

        if ($this->shouldSendPartnershipUpdateEmail($freshApplication)) {
            $this->sendMail($freshApplication->email, new ContractPeriodSavedMail($freshApplication->fresh()));
        }

        ActivityLog::log(
            'contract_period_saved',
            'partnership',
            (string) $freshApplication->id,
            "Saved contract period for partnership application #{$freshApplication->id}",
            [
                'application_id' => $freshApplication->id,
                'contract_period_start' => $validated['contract_period_start'],
                'contract_period_end' => $validated['contract_period_end'],
                'admin_id' => Auth::id(),
                'admin_email' => Auth::user()?->email,
            ]
        );

        return back()
            ->with('success', 'Contract period saved successfully.')
            ->with('contract_period_saved', true);
    }

    /**
     * Upload partnership document on behalf of concessionaire.
     */
    public function uploadPartnershipDocument(Request $request, PartnershipApplication $application)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:moa,contract,letter_of_intent',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'document.max' => 'File must not exceed 10MB',
        ]);

        $columnMap = [
            'moa' => 'moa_path',
            'contract' => 'contract_path',
            'letter_of_intent' => 'letter_of_intent_path',
        ];

        $documentType = $validated['document_type'];
        $column = $columnMap[$documentType];
        $existingPath = $application->{$column};

        if ($documentType === 'letter_of_intent' && ! $existingPath) {
            $existingPath = $application->letter_of_intent;
        }

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        $file = $validated['document'];
        $extension = $file->getClientOriginalExtension();
        $filename = $documentType . '_' . now()->format('YmdHis') . '_' . $application->id . '.' . $extension;
        $path = $file->storeAs('partnership_letters/' . $application->id, $filename, 'public');

        $updatePayload = [$column => $path];
        if ($documentType === 'letter_of_intent') {
            $updatePayload['letter_of_intent'] = $path;
        }

        $application->update($updatePayload);

        if ($this->shouldSendPartnershipUpdateEmail($application)) {
            $this->sendMail($application->email, new AdminDocumentUploadedMail($application->fresh()));
        }

        ActivityLog::log(
            'partnership_document_uploaded_admin',
            'partnership',
            (string) $application->id,
            "Admin uploaded {$documentType} document for partnership application #{$application->id}",
            [
                'application_id' => $application->id,
                'document_type' => $documentType,
                'document_path' => $path,
                'admin_id' => Auth::id(),
            ]
        );

        return back()->with('success', ucfirst(str_replace('_', ' ', $documentType)) . ' uploaded successfully.');
    }

    public function uploadAllPartnershipDocuments(Request $request, $id)
    {
        $application = PartnershipApplication::findOrFail($id);

        $validated = $request->validate([
            'moa_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'contract_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'loi_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'moa_file.max' => 'MOA file must not exceed 10MB.',
            'contract_file.max' => 'Contract file must not exceed 10MB.',
            'loi_file.max' => 'Letter of Intent file must not exceed 10MB.',
        ]);

        $documentConfigs = [
            'moa_file' => [
                'type' => 'moa',
                'column' => 'moa_path',
                'label' => 'MOA',
            ],
            'contract_file' => [
                'type' => 'contract',
                'column' => 'contract_path',
                'label' => 'Contract',
            ],
            'loi_file' => [
                'type' => 'letter_of_intent',
                'column' => 'letter_of_intent_path',
                'label' => 'Letter of Intent',
            ],
        ];

        $uploaded = [];
        $failed = [];

        foreach ($documentConfigs as $inputName => $config) {
            if (! $request->hasFile($inputName)) {
                continue;
            }

            try {
                $existingPath = $application->{$config['column']};
                if ($config['type'] === 'letter_of_intent' && ! $existingPath) {
                    $existingPath = $application->letter_of_intent;
                }

                if ($existingPath) {
                    Storage::disk('public')->delete($existingPath);
                }

                $file = $validated[$inputName];
                $extension = $file->getClientOriginalExtension();
                $filename = $config['type'] . '_' . now()->format('YmdHis') . '_' . $application->id . '.' . $extension;
                $folder = $application->user_id ?? $application->id;
                $path = $file->storeAs('partnership_letters/' . $folder, $filename, 'public');

                $updatePayload = [$config['column'] => $path];
                if ($config['type'] === 'letter_of_intent') {
                    $updatePayload['letter_of_intent'] = $path;
                }

                $application->update($updatePayload);

                ActivityLog::log(
                    'partnership_document_uploaded_admin',
                    'partnership',
                    (string) $application->id,
                    "Admin uploaded {$config['type']} document for partnership application #{$application->id}",
                    [
                        'application_id' => $application->id,
                        'document_type' => $config['type'],
                        'document_path' => $path,
                        'admin_id' => Auth::id(),
                    ]
                );

                $uploaded[] = $config['label'];
            } catch (\Throwable $e) {
                Log::warning('Admin bulk document upload failed: ' . $e->getMessage(), [
                    'controller' => self::class,
                    'application_id' => $application->id,
                    'document_type' => $config['type'],
                ]);

                $failed[] = $config['label'];
            }
        }

        if (! empty($uploaded) && $this->shouldSendPartnershipUpdateEmail($application)) {
            $this->sendMail($application->email, new AdminDocumentUploadedMail($application->fresh()));
        }

        if (! empty($failed) && ! empty($uploaded)) {
            return response()->json([
                'success' => false,
                'partial' => true,
                'message' => 'Uploaded: ' . implode(', ', $uploaded) . '. Failed: ' . implode(', ', $failed) . '.',
                'uploaded' => $uploaded,
                'failed' => $failed,
            ], 422);
        }

        if (! empty($failed)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload: ' . implode(', ', $failed) . '.',
                'failed' => $failed,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Documents uploaded successfully.',
            'uploaded' => $uploaded,
        ]);
    }

    private function sendMail(string $recipient, Mailable $mailable): void
    {
        try {
            if (config('queue.default') !== 'sync') {
                Mail::to($recipient)->queue($mailable);
                return;
            }

            Mail::to($recipient)->send($mailable);
        } catch (\Exception $e) {
            Log::warning('Mail failed: ' . $e->getMessage(), [
                'controller' => self::class,
                'recipient' => $recipient,
                'mailable' => $mailable::class,
            ]);
        }
    }

    private function shouldSendPartnershipUpdateEmail(PartnershipApplication $application): bool
    {
        if (! $application->user_id) {
            return true;
        }

        $user = User::find($application->user_id);

        if (! $user) {
            return true;
        }

        return $user->getNotificationPreference('email_partnership_updates');
    }

    /**
     * Display application logs.
     */
    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            // Parse log entries (each starts with a date pattern)
            $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[\.\d+]*)\]\s+(\w+)\.(\w+):\s+(.*?)(?=\[\d{4}-\d{2}-\d{2}|\z)/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $logs[] = [
                    'timestamp' => $match[1],
                    'env' => $match[2],
                    'level' => strtolower($match[3]),
                    'message' => trim($match[4]),
                ];
            }

            // Reverse for newest first, then paginate manually
            $logs = array_reverse($logs);
        }

        // Filter by level
        if ($level = $request->input('level')) {
            $logs = array_values(array_filter($logs, fn($l) => $l['level'] === $level));
        }

        // Search
        if ($search = $request->input('search')) {
            $logs = array_values(array_filter($logs, fn($l) => stripos($l['message'], $search) !== false));
        }

        $total = count($logs);
        $perPage = 25;
        $page = max(1, (int) $request->input('page', 1));
        $logs = array_slice($logs, ($page - 1) * $perPage, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $logs, $total, $perPage, $page,
            ['path' => route('admin.logs')]
        );
        $paginator->appends($request->query());

        $fileSize = file_exists($logFile) ? filesize($logFile) : 0;

        return $this->adminView('admin.logs', [
            'logs' => $paginator,
            'fileSize' => $fileSize,
            'total' => $total,
        ]);
    }

    /**
     * Clear the log file.
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }
        return back()->with('success', 'Log file cleared.');
    }

    /**
     * Display activity logs (admin actions audit trail).
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($type = $request->input('type')) {
            $query->where('subject_type', $type);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('subject_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $types = ActivityLog::select('subject_type')->distinct()->pluck('subject_type');

        return $this->adminView('admin.activity-logs', compact('logs', 'actions', 'types'));
    }

    /**
     * Display stock management page.
     */
    public function stocks()
    {
        $stocks = UniformStock::query()
            ->orderBy('item_name')
            ->get();

        return $this->adminView('admin.stocks', compact('stocks'));
    }

    /**
     * Store a new stock item.
     */
    public function storeStock(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100|unique:uniform_stocks,item_name',
            'quantity' => 'required|integer|min:0|max:3000',
            'item_type' => ['nullable', Rule::in(['books', 'uniforms'])],
            'prices' => 'nullable|array',
            'prices.*' => 'nullable|numeric|min:0|max:999999.99',
            'book_price' => 'nullable|numeric|min:0|max:999999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_visible' => 'nullable|boolean',
        ]);

        $itemType = $validated['item_type'] ?? null;
        $prices = null;
        $sizes = null;
        $unitPrice = null;
        $quantity = (int) $validated['quantity'];

        if ($itemType === 'uniforms') {
            $sizeKeys = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
            $prices = [];
            $sizes = [];
            $quantity = 0;

            foreach ($sizeKeys as $sizeKey) {
                $inputKey = strtolower($sizeKey);
                $priceValue = $request->input('price_' . $inputKey, $request->input('prices.' . $sizeKey, 0));
                $sizeQuantity = max(0, (int) $request->input('qty_' . $inputKey, 0));
                $prices[$sizeKey] = max(0, (float) $priceValue);
                $sizes[$sizeKey] = $sizeQuantity;
                $quantity += $sizeQuantity;
            }
        } elseif ($itemType === 'books') {
            $unitPrice = max(0, (float) ($validated['book_price'] ?? 0));
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('stocks', 'public');
        }

        $stock = UniformStock::create([
            'item_name' => $validated['item_name'],
            'icon' => null,
            'image' => $imagePath,
            'item_type' => $itemType,
            'sizes' => $sizes,
            'prices' => $prices,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'is_visible' => (bool) ($validated['is_visible'] ?? false),
        ]);

        ActivityLog::log(
            'stock_item_created',
            'uniform_stock',
            (string) $stock->id,
            "Created stock item {$stock->item_name}",
            [
                'stock_id' => $stock->id,
                'item_name' => $stock->item_name,
                'quantity' => $stock->quantity,
                'is_visible' => $stock->is_visible,
                'admin_id' => Auth::id(),
            ]
        );

        return back()->with('success', 'Stock item added successfully.');
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(Request $request, UniformStock $stock)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:3000',
            'item_type' => ['nullable', Rule::in(['books', 'uniforms'])],
            'book_price' => 'nullable|numeric|min:0|max:999999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $oldQuantity = (int) $stock->quantity;
        $newQuantity = (int) $validated['quantity'];
        $oldImage = $stock->image;

        if ($request->hasFile('image')) {
            if ($stock->image) {
                Storage::disk('public')->delete($stock->image);
            }

            $stock->image = $request->file('image')->store('stocks', 'public');
        }

        $itemType = strtolower((string) ($validated['item_type'] ?? $stock->item_type ?? ''));
        $prices = null;
        $sizes = null;
        $unitPrice = null;

        if ($itemType === 'uniforms') {
            $sizeKeys = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
            $prices = [];
            $sizes = [];
            $newQuantity = 0;

            foreach ($sizeKeys as $sizeKey) {
                $inputKey = strtolower($sizeKey);
                $priceValue = $request->input('price_' . $inputKey, $request->input('prices.' . $sizeKey, 0));
                $sizeQuantity = max(0, (int) $request->input('qty_' . $inputKey, 0));
                $prices[$sizeKey] = max(0, (float) $priceValue);
                $sizes[$sizeKey] = $sizeQuantity;
                $newQuantity += $sizeQuantity;
            }
        } elseif ($itemType === 'books') {
            $unitPrice = max(0, (float) ($validated['book_price'] ?? 0));
        }

        $stock->quantity = $newQuantity;
        $stock->item_type = $itemType ?: $stock->item_type;
        $stock->sizes = $sizes;
        $stock->prices = $prices;
        $stock->unit_price = $unitPrice;
        $stock->save();

        ActivityLog::log(
            'stock_quantity_updated',
            'uniform_stock',
            (string) $stock->id,
            "Updated stock quantity for {$stock->item_name}",
            [
                'stock_id' => $stock->id,
                'item_name' => $stock->item_name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'old_image' => $oldImage,
                'new_image' => $stock->image,
                'admin_id' => Auth::id(),
            ]
        );

        return back()->with('success', "Quantity updated for {$stock->item_name}.");
    }

    /**
     * Archive or restore a stock item safely.
     */
    public function confirmDelete(int $id)
    {
        $stock = UniformStock::find($id);

        if (! $stock) {
            return back()->with('error', 'Stock item not found.');
        }

        $stockId = (int) $stock->id;
        $stockName = (string) $stock->item_name;
        $oldVisibility = (bool) $stock->is_visible;
        $newVisibility = ! $oldVisibility;

        try {
            $stock->update([
                'is_visible' => $newVisibility,
            ]);

            ActivityLog::log(
                $newVisibility ? 'stock_item_restored' : 'stock_item_archived',
                'uniform_stock',
                (string) $stockId,
                $newVisibility ? "Restored stock item {$stockName}" : "Archived stock item {$stockName}",
                [
                    'stock_id' => $stockId,
                    'item_name' => $stockName,
                    'old_visibility' => $oldVisibility,
                    'new_visibility' => $newVisibility,
                    'admin_id' => Auth::id(),
                ]
            );

            return $newVisibility
                ? back()->with('success', "Restored {$stockName}. It is now active in stock lists.")
                : back()->with('success', "Archived {$stockName}. It is now hidden from active lists.");
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', "Unable to update {$stockName} status. Please try again.");
        }
    }
    public function transactionLogs(Request $request)
    {
        $today = now()->toDateString();
        $startDate = (string) $request->query('start_date', $today);
        $endDate = (string) $request->query('end_date', $today);

        validator([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ], [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ])->validate();

        // Keep the range ordered so a reversed selection still returns results.
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        if ($request->query('export') === 'csv') {
            $filename = 'transaction_logs_' . $startDate . '_to_' . $endDate . '.csv';

            return response()->streamDownload(function () use ($startDate, $endDate): void {
                $output = fopen('php://output', 'w');

                fputcsv($output, [
                    'Quantity',
                    'Cashier Name',
                    'Payment Method',
                    'Total Price',
                    'Date',
                ]);

                SalesOrder::query()
                    ->with(['cashier', 'items'])
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->latest('id')
                    ->chunkById(200, function ($orders) use ($output): void {
                        foreach ($orders as $order) {
                            fputcsv($output, [
                                $order->items->sum('quantity'),
                                $order->cashier?->name ?? 'N/A',
                                $order->payment_type,
                                number_format((float) $order->total_amount, 2, '.', ''),
                                optional($order->created_at)->format('M d, Y g:i A'),
                            ]);
                        }
                    });

                fclose($output);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        }

        $orders = SalesOrder::query()
            ->with(['items.uniformStock', 'cashier'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return $this->adminView('admin.transaction_logs', compact('orders', 'startDate', 'endDate'));
    }

    /**
     * Show the site settings CMS page.
     */
    public function siteSettings()
    {
        $settings = \App\Models\SiteSetting::pluck('value', 'key');

        return $this->adminView('admin.site-settings', compact('settings'));
    }

    /**
     * Save all site settings submitted from the CMS form.
     */
    public function updateSiteSettings(Request $request)
    {
        $imageKeys = [
            'hero_image', 'about_image', 'faq_image',
        ];

        $request->validate(
            collect($imageKeys)->mapWithKeys(fn ($key) => [$key => 'nullable|image|max:4096'])->all()
        );

        $textKeys = [
            'hero_title', 'hero_subtitle',
            'uniforms_title', 'uniforms_subtitle',
            'features_title', 'features_subtitle',
            'about_title', 'about_subtitle',
            'feature_1_title', 'feature_1_desc',
            'feature_2_title', 'feature_2_desc',
            'feature_3_title', 'feature_3_desc',
            'showcase_title', 'showcase_subtitle',
            'vision', 'mission',
            'core_value_1', 'core_value_2', 'core_value_3', 'core_value_4', 'core_value_5',
            'faq_1_question', 'faq_1_answer',
            'faq_2_question', 'faq_2_answer',
            'faq_3_question', 'faq_3_answer',
            'faq_4_question', 'faq_4_answer',
        ];

        foreach ($textKeys as $key) {
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key, '')]
            );
        }

        foreach ($imageKeys as $key) {
            if (! $request->hasFile($key)) {
                continue;
            }

            $existing = \App\Models\SiteSetting::get($key, '');
            if ($existing !== '' && Storage::disk('public')->exists($existing)) {
                Storage::disk('public')->delete($existing);
            }

            $path = $request->file($key)->store('site', 'public');
            \App\Models\SiteSetting::updateOrCreate(['key' => $key], ['value' => $path]);
        }

        return redirect()->route('admin.site-settings')->with('success', 'Site settings updated successfully.');
    }
}