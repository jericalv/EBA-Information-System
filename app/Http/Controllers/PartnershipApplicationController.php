<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewPartnershipMail;
use App\Mail\PartnershipDocumentsReceivedMail;
use App\Mail\PartnershipDocumentUploadedMail;
use App\Mail\PartnershipReceivedMail;
use App\Models\ActivityLog;
use App\Models\PartnershipApplication;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PartnershipApplicationController extends Controller
{
    public function create()
    {
        return redirect()->route('application');
    }

    public function store(Request $request)
    {
        return redirect()->route('application')
            ->with('success', 'Your account is currently pending approval. Please upload your required documents on your application page.');
    }

    public function status(Request $request)
    {
        $user = $request->user();

        $application = $this->ensureApplicationForUser($user);

        return view('partnerships.status', compact('application'));
    }

    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'concessionaire') {
            abort(403);
        }

        $application = $this->ensureApplicationForUser($user);

        return view('partnerships.application', compact('application'));
    }

    public function settingsPage(Request $request)
    {
        return $this->show($request);
    }

    public function updateApplicantInfo(Request $request)
    {
        $user = $request->user();

        $application = $this->ensureApplicationForUser($user);

        if (! in_array($application->status, ['pending', 'rejected'], true)) {
            return back()->with('error', 'Your application has already been processed and can no longer be edited.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'regex:/^9\d{9}$/'],
            'business_name' => ['required', 'string', 'max:15'],
            'products_services' => ['nullable', 'string'],
            'student_benefit' => ['nullable', 'string'],
            'unique_selling_point' => ['nullable', 'string'],
            'expected_price_range' => ['nullable', 'string', 'max:255'],
            'business_proposal' => ['required', 'string', 'min:50', 'max:1000'],
        ]);

        $normalizedPhoneNumber = '+63' . $validated['phone_number'];

        $application->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'business_name' => $validated['business_name'],
            'products_services' => $validated['products_services'] ?? null,
            'student_benefit' => $validated['student_benefit'] ?? null,
            'unique_selling_point' => $validated['unique_selling_point'] ?? null,
            'expected_price_range' => $validated['expected_price_range'] ?? null,
            'phone_number' => $normalizedPhoneNumber,
            'business_proposal' => $validated['business_proposal'],
            // Keep legacy columns in sync for existing views/admin flows.
            'phone' => $normalizedPhoneNumber,
            'proposal' => $validated['business_proposal'],
        ]);

        $user->update([
            'business_name' => $validated['business_name'],
        ]);

        return back()->with('success', 'Application information saved successfully.');
    }

    public function uploadDocument(Request $request)
    {
        $user = $request->user();

        $application = $this->ensureApplicationForUser($user);

        if (!$application) {
            return redirect()->route('application')
                ->with('error', 'No partnership application found.');
        }

        if (! in_array($application->status, ['pending', 'rejected'], true)) {
            return redirect()->route('application')
                ->with('error', 'You can only upload documents while your application is under review.');
        }

        $validated = $request->validate([
            'document_type' => 'required|in:moa,contract,letter_of_intent',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'document.max' => 'File must not exceed 5MB',
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
        $filename = $documentType . '_' . now()->format('YmdHis') . '.' . $extension;
        $folderUserId = $application->user_id ?? $user->id;
        $path = $file->storeAs('partnership_letters/' . $folderUserId, $filename, 'public');

        $payload = [
            $column => $path,
            'user_id' => $application->user_id ?? $user->id,
        ];

        if ($documentType === 'letter_of_intent') {
            $payload['letter_of_intent'] = $path;
        }

        $application->update($payload);

        if ($user->getNotificationPreference('email_partnership_updates')) {
            $this->sendMail($application->email, new PartnershipDocumentsReceivedMail($application));
        }

        $this->sendMail($this->resolveAdminEmail(), new PartnershipDocumentUploadedMail($application));

        ActivityLog::log(
            'partnership_document_uploaded',
            'partnership',
            (string) $application->id,
            'Uploaded additional partnership document',
            [
                'application_id' => $application->id,
                'email' => $application->email,
                'document_type' => $documentType,
                'document' => $path,
            ]
        );

        return redirect()->route('application')
            ->with('success', 'Document uploaded successfully.');
    }

    public function uploadLOI(Request $request)
    {
        $application = $this->ensureApplicationForUser($request->user());

        if (! in_array($application->wizard_status, ['loi_pending', 'loi_rejected'], true)) {
            abort(403);
        }

        $validated = $request->validate([
            'loi_files' => ['required', 'array', 'min:1', 'max:' . PartnershipApplication::MAX_DOCUMENT_FILES],
            'loi_files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'loi_files.required' => 'Please select at least one file.',
            'loi_files.max' => 'You may upload up to ' . PartnershipApplication::MAX_DOCUMENT_FILES . ' files only.',
            'loi_files.*.max' => 'Each file must not exceed 5MB.',
            'loi_files.*.mimes' => 'Each file must be a PDF, JPG, PNG, or WebP.',
        ]);

        $storedPaths = $this->storeDocumentFiles(
            Auth::id(),
            'letter_of_intent',
            $application->documentPaths('letter_of_intent'),
            $validated['loi_files']
        );

        $application->update([
            'letter_of_intent_paths' => $storedPaths,
            'letter_of_intent_path' => $storedPaths[0] ?? null,
            'letter_of_intent' => $storedPaths[0] ?? null,
            'wizard_status' => 'loi_submitted',
            'loi_submitted_at' => now(),
            'loi_rejection_reason' => null,
        ]);

        ActivityLog::log(
            'loi_uploaded',
            'partnership_application',
            (string) $application->id,
            'Letter of Intent uploaded for wizard step 1.',
            [
                'user_id' => Auth::id(),
                'files' => $storedPaths,
            ]
        );

        return back()->with('success', 'Letter of Intent submitted. Waiting for admin review.');
    }

    public function submitWizardForm(Request $request)
    {
        $application = $this->ensureApplicationForUser($request->user());

        if (! in_array($application->wizard_status, ['form_pending', 'form_rejected'], true)) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'business_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'phone_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'type_of_business' => ['required', 'array', 'min:1'],
            'type_of_business.*' => ['in:food,non_food'],
            'proposed_location' => ['required', 'string', 'max:255'],
            'proposed_duration' => ['required', 'string', 'max:255'],
            'is_previous_concessionaire' => ['required', 'in:yes,no'],
            'previous_location_year' => ['nullable', 'string', 'max:255'],
            'business_proposal' => ['required', 'string', 'min:50', 'max:1000'],
            'certification_agreed' => ['required', 'accepted'],
            'valid_id' => ['required', 'array', 'min:1', 'max:' . PartnershipApplication::MAX_DOCUMENT_FILES],
            'valid_id.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'business_permit' => ['nullable', 'array', 'max:' . PartnershipApplication::MAX_DOCUMENT_FILES],
            'business_permit.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'valid_id.required' => 'Please upload at least one valid ID file.',
            'valid_id.max' => 'You may upload up to ' . PartnershipApplication::MAX_DOCUMENT_FILES . ' valid ID files only.',
            'valid_id.*.max' => 'Each file must not exceed 5MB.',
            'valid_id.*.mimes' => 'Each file must be a PDF, JPG, PNG, or WebP.',
            'business_permit.max' => 'You may upload up to ' . PartnershipApplication::MAX_DOCUMENT_FILES . ' business permit files only.',
            'business_permit.*.max' => 'Each file must not exceed 5MB.',
            'business_permit.*.mimes' => 'Each file must be a PDF, JPG, PNG, or WebP.',
        ]);

        $userId = Auth::id();
        $validIdPaths = $this->storeDocumentFiles(
            $userId,
            'valid_id',
            $application->documentPaths('valid_id'),
            $validated['valid_id']
        );

        $businessPermitPaths = $application->documentPaths('business_permit');
        if (! empty($validated['business_permit'])) {
            $businessPermitPaths = $this->storeDocumentFiles(
                $userId,
                'business_permit',
                $application->documentPaths('business_permit'),
                $validated['business_permit']
            );
        }

        $application->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'business_name' => $validated['business_name'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
            // Keep legacy column in sync for existing views/admin flows.
            'phone' => $validated['phone_number'],
            'type_of_business' => implode(',', $validated['type_of_business']),
            'proposed_location' => $validated['proposed_location'],
            'proposed_duration' => $validated['proposed_duration'],
            'is_previous_concessionaire' => $validated['is_previous_concessionaire'] === 'yes',
            'previous_location_year' => $validated['is_previous_concessionaire'] === 'yes'
                ? ($validated['previous_location_year'] ?? null)
                : null,
            'business_proposal' => $validated['business_proposal'],
            'certification_agreed' => true,
            'valid_id_path' => $validIdPaths[0] ?? null,
            'valid_id_paths' => $validIdPaths,
            'business_permit_path' => $businessPermitPaths[0] ?? null,
            'business_permit_paths' => $businessPermitPaths,
            'wizard_status' => 'form_submitted',
            'form_submitted_at' => now(),
            'form_rejection_reason' => null,
        ]);

        $request->user()->update([
            'business_name' => $validated['business_name'],
        ]);

        ActivityLog::log(
            'wizard_form_submitted',
            'partnership_application',
            (string) $application->id,
            'Wizard application form submitted.',
            [
                'user_id' => Auth::id(),
            ]
        );

        return back()->with('success', 'Application form submitted. Waiting for admin review.');
    }

    public function uploadReceipt(Request $request)
    {
        $application = $this->ensureApplicationForUser($request->user());

        if ($application->wizard_status !== 'receipt_pending') {
            abort(403);
        }

        $validated = $request->validate([
            'receipt_files' => ['required', 'array', 'min:1', 'max:' . PartnershipApplication::MAX_DOCUMENT_FILES],
            'receipt_files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'receipt_files.required' => 'Please select at least one file.',
            'receipt_files.max' => 'You may upload up to ' . PartnershipApplication::MAX_DOCUMENT_FILES . ' files only.',
            'receipt_files.*.max' => 'Each file must not exceed 5MB.',
            'receipt_files.*.mimes' => 'Each file must be a PDF, JPG, PNG, or WebP.',
        ]);

        $storedPaths = $this->storeDocumentFiles(
            Auth::id(),
            'receipt',
            $application->documentPaths('receipt'),
            $validated['receipt_files']
        );

        $application->update([
            'receipt_path' => $storedPaths[0] ?? null,
            'receipt_paths' => $storedPaths,
            'wizard_status' => 'receipt_submitted',
            'receipt_submitted_at' => now(),
        ]);

        ActivityLog::log(
            'receipt_uploaded',
            'partnership_application',
            (string) $application->id,
            'Final receipt uploaded for wizard.',
            [
                'user_id' => Auth::id(),
                'files' => $storedPaths,
            ]
        );

        return back()->with('success', 'Receipt submitted. Waiting for final approval.');
    }

    /**
     * Store an ordered set of uploaded files for a document type, replacing
     * any previously stored files. Returns the list of stored relative paths.
     *
     * @param  array<int, \Illuminate\Http\UploadedFile>  $files
     * @param  array<int, string>  $existingPaths
     * @return array<int, string>
     */
    private function storeDocumentFiles($userId, string $prefix, array $existingPaths, array $files): array
    {
        foreach ($existingPaths as $old) {
            if ($old) {
                Storage::disk('public')->delete($old);
            }
        }

        $stored = [];
        $index = 0;
        foreach ($files as $file) {
            $index++;
            $ext = $file->getClientOriginalExtension();
            $filename = $prefix . '_' . $index . '.' . $ext;
            $stored[] = Storage::disk('public')->putFileAs(
                'partnership_letters/' . $userId,
                $file,
                $filename
            );
        }

        return $stored;
    }

    private function ensureApplicationForUser($user): PartnershipApplication
    {
        $existing = PartnershipApplication::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        $firstName = trim((string) ($user->first_name ?? strtok($user->name, ' ')));
        $lastName = trim((string) ($user->last_name ?? str($user->name)->after($firstName)));

        $application = PartnershipApplication::create([
            'user_id' => $user->id,
            'first_name' => $firstName !== '' ? $firstName : $user->name,
            'middle_name' => null,
            'last_name' => $lastName !== '' ? $lastName : 'Applicant',
            'email' => $user->email,
            'business_name' => $user->business_name ?? '',
            'phone_number' => null,
            'business_proposal' => null,
            'phone' => null,
            'proposal' => '',
            'status' => 'pending',
        ]);

        if ($user->getNotificationPreference('email_partnership_updates')) {
            $this->sendMail($application->email, new PartnershipReceivedMail($application));
        }

        $this->sendMail($this->resolveAdminEmail(), new AdminNewPartnershipMail($application));

        ActivityLog::log(
            'partnership_auto_created',
            'partnership',
            (string) $application->id,
            'Created pending partnership application for newly registered concessionaire',
            [
                'application_id' => $application->id,
                'user_id' => $user->id,
                'email' => $application->email,
            ]
        );

        return $application;
    }

    public function resubmitApplication(Request $request)
    {
        $user = $request->user();

        $application = $this->ensureApplicationForUser($user);

        if ($application->status !== 'rejected') {
            abort(403);
        }

        $application->update([
            'status' => 'pending',
            'rejection_reason' => null,
            'wizard_status' => 'loi_pending',
            'loi_rejection_reason' => null,
            'form_rejection_reason' => null,
            'docs_recommendation_checked' => false,
            'docs_notice_occupy_checked' => false,
            'docs_notice_termination_checked' => false,
            'docs_moa_contract_checked' => false,
            'receipt_path' => null,
            'receipt_paths' => null,
        ]);

        ActivityLog::log(
            'partnership_resubmitted',
            'partnership',
            (string) $application->id,
            'Applicant resubmitted their partnership application.',
            [
                'message' => 'Applicant resubmitted their partnership application.',
            ]
        );

        return redirect()->route('application')
            ->with('success', "Your application has been resubmitted. We'll review it shortly.");
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

    private function resolveAdminEmail(): string
    {
        return config('mail.admin_email')
            ?? env('APP_ADMIN_EMAIL')
            ?? config('mail.from.address');
    }
}
