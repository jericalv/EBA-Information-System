<?php

namespace Tests\Feature;

use App\Models\PartnershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Exercises the Step 2 (Application Form) approve/reject round-trip shown in
 * the admin "Application Details" modal and the concessionaire wizard page,
 * driving the real routes / middleware / validation / DB transitions.
 */
class WizardFormApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function concessionaire(string $wizardStatus = 'form_pending'): array
    {
        $user = User::factory()->create([
            'role' => 'concessionaire',
            'business_name' => 'Old Name',
        ]);

        $application = PartnershipApplication::create([
            'user_id' => $user->id,
            'first_name' => 'Jobert',
            'last_name' => 'Asd',
            'email' => $user->email,
            'business_name' => 'Old Name',
            'proposal' => '',
            'status' => 'pending',
            'wizard_status' => $wizardStatus,
        ]);

        return [$user, $application];
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function validFormPayload(): array
    {
        return [
            'first_name' => 'Jobert',
            'last_name' => 'Asd',
            'business_name' => 'ASPO DJAPSOD',
            'address' => 'blk 123 lot 405 brgy josa',
            'phone_number' => '09238293871',
            'type_of_business' => ['food'],
            'proposed_location' => 'Main canteen area',
            'proposed_duration' => '1 year',
            'is_previous_concessionaire' => 'no',
            'business_proposal' => str_repeat('We will sell affordable rice meals to students. ', 3),
            'certification_agreed' => '1',
            'valid_id' => UploadedFile::fake()->create('valid_id.jpg', 200, 'image/jpeg'),
            'business_permit' => UploadedFile::fake()->create('permit.pdf', 100, 'application/pdf'),
        ];
    }

    /** CONCESSIONAIRE SIDE: submitting the form moves form_pending -> form_submitted. */
    public function test_concessionaire_can_submit_application_form(): void
    {
        Storage::fake('public');
        [$user, $application] = $this->concessionaire('form_pending');

        $response = $this->actingAs($user)
            ->post(route('application.wizard-form'), $this->validFormPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertSame('form_submitted', $application->wizard_status);
        $this->assertNotNull($application->form_submitted_at);
        $this->assertSame('ASPO DJAPSOD', $application->business_name);
        Storage::disk('public')->assertExists($application->valid_id_path);
    }

    /** CONCESSIONAIRE SIDE: the wizard page shows the "under review" state at step 2. */
    public function test_concessionaire_sees_form_under_review_state(): void
    {
        [$user, $application] = $this->concessionaire('form_submitted');

        $response = $this->actingAs($user)->get(route('application'));

        $response->assertOk();
        $response->assertSee('under review', false);  // banner-title text
        $this->assertSame(2, $application->wizardStepNumber());
    }

    /** ADMIN SIDE: Approve Form moves form_submitted -> docs_in_progress. */
    public function test_admin_can_approve_form(): void
    {
        [, $application] = $this->concessionaire('form_submitted');
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.partnerships.wizard.approve-form', $application));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertSame('docs_in_progress', $application->fresh()->wizard_status);
    }

    /** ADMIN SIDE: Reject Form moves form_submitted -> form_rejected and stores the reason. */
    public function test_admin_can_reject_form_with_reason(): void
    {
        [, $application] = $this->concessionaire('form_submitted');
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.partnerships.wizard.reject-form', $application), [
                'reason' => 'Business proposal is incomplete, please revise it.',
            ]);

        $response->assertOk()->assertJson(['success' => true]);
        $fresh = $application->fresh();
        $this->assertSame('form_rejected', $fresh->wizard_status);
        $this->assertSame('Business proposal is incomplete, please revise it.', $fresh->form_rejection_reason);
    }

    /**
     * ADMIN SIDE: reject requires a reason (min 10 chars). The guard works (state is
     * unchanged and success=false), though the controller's try/catch swallows the
     * ValidationException and returns 200 instead of the usual 422 -- see note below.
     */
    public function test_admin_reject_form_requires_reason(): void
    {
        [, $application] = $this->concessionaire('form_submitted');
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.partnerships.wizard.reject-form', $application), ['reason' => 'too short']);

        // The rejection is blocked: state stays form_submitted and success is false.
        $response->assertOk()->assertJson(['success' => false]);
        $this->assertSame('form_submitted', $application->fresh()->wizard_status);
        $this->assertNull($application->fresh()->form_rejection_reason);
    }

    /** GUARD: approving when not in form_submitted is rejected as an invalid state. */
    public function test_admin_cannot_approve_form_in_wrong_state(): void
    {
        [, $application] = $this->concessionaire('form_pending');
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.partnerships.wizard.approve-form', $application));

        $response->assertOk()->assertJson(['success' => false]);
        $this->assertSame('form_pending', $application->fresh()->wizard_status);
    }

    /** ROUND-TRIP: after admin rejects, concessionaire sees the reason and can resubmit. */
    public function test_concessionaire_sees_rejection_and_can_resubmit(): void
    {
        Storage::fake('public');
        [$user, $application] = $this->concessionaire('form_submitted');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->postJson(route('admin.partnerships.wizard.reject-form', $application), [
                'reason' => 'Please attach a clearer valid ID document.',
            ])->assertOk();

        // Concessionaire page now shows the rejection reason.
        $this->actingAs($user)->get(route('application'))
            ->assertOk()
            ->assertSee('Please attach a clearer valid ID document.', false);

        // And the resubmit (form re-POST) works from form_rejected.
        $this->actingAs($user)
            ->post(route('application.wizard-form'), $this->validFormPayload())
            ->assertRedirect();

        $this->assertSame('form_submitted', $application->fresh()->wizard_status);
    }

    /** SECURITY: a concessionaire cannot hit the admin approve endpoint. */
    public function test_concessionaire_cannot_access_admin_approve_route(): void
    {
        [$user, $application] = $this->concessionaire('form_submitted');

        $this->actingAs($user)
            ->postJson(route('admin.partnerships.wizard.approve-form', $application))
            ->assertForbidden();

        $this->assertSame('form_submitted', $application->fresh()->wizard_status);
    }
}
