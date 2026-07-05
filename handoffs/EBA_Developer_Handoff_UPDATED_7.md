# EBA Information System — Developer Handoff Context
## Last Updated: June 17, 2026
## Primary Developer: John Eric Alvarado

---

## RECENT UPDATES (June 17, 2026)
- **Site Settings CMS Expanded to Images + FAQs (Not Just Text):** The landing-page CMS now lets admins edit images and FAQs, not only titles/copy. Added image-upload fields for the **hero image**, **about image**, and **FAQ image**, plus editable **FAQ items** (4 question/answer pairs) and the **concessionaires showcase heading/description**.
- **New `SiteSetting::image()` Helper:** Added `App\Models\SiteSetting::image(string $key, string $default)` which resolves an uploaded relative path (stored on the `public` disk, e.g. `site/abc.jpg`) to `asset('storage/...')`, returns full URLs/absolute paths as-is, and falls back to the provided default asset URL when no image has been uploaded. Added `use Illuminate\Support\Str;` to the model.
- **`updateSiteSettings()` Now Handles File Uploads:** `AdminController@updateSiteSettings` validates image keys (`hero_image`, `about_image`, `faq_image`) as `nullable|image|max:4096`, deletes the previous file via `Storage::disk('public')->delete()` when replaced, stores new files under `storage/app/public/site/`, and persists the relative path as the setting value. Text keys expanded to include `showcase_title`, `showcase_subtitle`, and `faq_1..4_question` / `faq_1..4_answer`. (`Storage` and `Request` were already imported.)
- **Seeder Defaults Extended:** `SiteSettingSeeder` now seeds `showcase_title`/`showcase_subtitle` and the four FAQ question/answer defaults (idempotent via `firstOrCreate`). Image settings are intentionally NOT seeded — they fall back to the original bundled assets until an admin uploads a replacement.
- **`welcome.blade.php` Images + FAQs Are Data-Driven:** Hero, About, and FAQ images now render via `SiteSetting::image(...)` with the original `asset('images/...')` paths as fallbacks. FAQ items loop over `faq_1..4_*` with hardcoded defaults; an empty question hides that FAQ row.
- **Site Settings Page Redesigned (Space-Efficient Layout):** Rebuilt `resources/views/admin/site-settings.blade.php` from stacked full-width fields into a clean sectioned layout — icon section headers, responsive 2/3-column grids (`.ss-grid-2`/`.ss-grid-3`/auto-fit) so fields use horizontal space instead of one wide row each, image-upload cards with live JS thumbnail preview (`ssPreview()`), and a sticky bottom Save bar. Form now uses `enctype="multipart/form-data"` and surfaces validation errors.
- **Concessionaires Showcase Converted to a Dynamic Coverflow Carousel:** Replaced the static 2×2 image grid on `welcome.blade.php` with a 3D "coverflow" gallery driven by **approved + active concessionaires** (`role=concessionaire`, `is_active_concessionaire=true`, `is_approved=true`, `latest()->take(12)`). Each slide uses the concessionaire's `carousel_image` (fallback chain `carousel_image → cover_photo → profile_photo → gradient`), shows business name + location, and links to `concessionaires.show`. More approvals = more slides — same data logic as the concessionaires page hero carousel, but **intentionally different styling** (curved coverflow vs. the full-bleed hero with thumbnail strip).
- **Showcase Layout = Heading Above, Carousel Below:** Per request, the section is stacked: `showcase_title` heading (left) + `showcase_subtitle` description and "View All Concessionaires" button (right) on top, then a full-width coverflow gallery below. JS (`cfMove`/`cfGo`/`layout`) computes per-card `translateX/translateZ/rotateY/scale` from offset-to-active; auto-advances every 5s (pauses on hover); clicking a side card recenters it while clicking the centered card follows its link; responsive recalced on resize. Self-contained `<style>`+`<script>` live inside the section (consistent with the existing inline `SiteSetting` query pattern, since `/` is a `Route::view('/', 'welcome')` with no controller).
- **Obsolete Manual Showcase Card Editors Removed:** Because cards now auto-populate from concessionaires, the previous manual `showcase_1..4_image/title/desc` editors were removed from the CMS view, dropped from the controller `$imageKeys`/`$textKeys`, removed from the seeder, and their rows deleted from the `site_settings` table. The CMS now shows an explanatory note in the Showcase section; only `showcase_title`/`showcase_subtitle` remain editable there.
- **Data State At Handoff:** 6 approved + active concessionaires exist (only 1 has uploaded a `carousel_image`; the rest fall back to cover/profile photo or a gradient). `public/storage` symlink is present, so uploaded images serve immediately.
- **Verification (Local):** `php artisan view:cache` compiles all Blade (welcome + redesigned site-settings) with no errors; `SiteSettingSeeder` reseeded successfully; `GET /` returns HTTP 200 with the coverflow markup (`cf-stage`/`cf-card`/`cf-gallery`/`cf-header`) and all 6 cards rendered.
- **Open Follow-up (Pending User Input):** User asked to fine-tune the coverflow (card size / overlap / rotation angle / auto-rotate speed). Current tuned values live in the `layout()` function inside the showcase `<script>` in `welcome.blade.php`: `gap = clamp(120, width*0.27, 320)`, `rotateY = clamp(±38, -offset*26)`, `translateZ = -|offset|*110`, `scale = max(0.7, 1 - |offset|*0.08)`, far cards (`|offset|>2`) hidden.

## AUDIT BASIS (June 17, 2026)
- Workspace still has no local git metadata (`Is a git repository: false`); this update is based on direct inspection of the files edited this session plus local `artisan` verification (view cache compile, seeder run, `curl` of `GET /`).

## RECENT UPDATES (June 16, 2026)
- **Admin Bell Notification Feature Added (Application Wizard Feed):** Admin navbar now has a bell/dropdown pattern matching faculty behavior, but driven by concessionaire wizard step submissions. Dropdown rows show concessionaire/business name, step label (`Submitted Letter of Intent` / `Submitted Application Form` / `Submitted Receipt`), relative timestamp, and deep-link to admin partnerships filtered by application id.
- **Admin Notification Read State (Per-Admin):** Added route `POST /admin/notifications/mark-read` (`admin.notifications.mark-read`) and `AdminController@markApplicationNotificationsRead()`. Clicking the bell marks all admin application notifications as read at once (same all-at-once behavior as faculty payments).
- **Admin Unread Aggregation Logic Added:** Implemented `AdminController@getUnreadApplicationSteps()` + shared `adminView()` injector so all admin pages receive `$unreadApplicationSteps` and `$unreadApplicationCount` automatically.
- **New Notification Read Column on Users:** Added migration `2026_06_16_120001_add_application_notifications_read_at_to_users_table.php` introducing nullable `users.application_notifications_read_at` timestamp (same pattern as faculty payment notification read timestamp).
- **Wizard Step Submission Timestamps Added:** Added migration `2026_06_16_120002_add_step_submission_timestamps_to_partnership_applications_table.php` introducing nullable `loi_submitted_at`, `form_submitted_at`, and `receipt_submitted_at` on `partnership_applications`.
- **Wizard Submission Writes Now Timestamped:** `PartnershipApplicationController` now sets `loi_submitted_at` in `uploadLOI()`, `form_submitted_at` in `submitWizardForm()`, and `receipt_submitted_at` in `uploadReceipt()`.
- **Safety Guard for Unmigrated Environments:** Added schema checks in admin notification methods so admin pages do not hard-fail if new columns are missing before migrations run; bell quietly shows no notifications until schema is present.
- **Admin Bell UI Hotfix (No Tailwind Utilities in Admin Layout):** Initial bell block reused faculty utility classes, causing oversized SVG/icon rendering in admin layout. Fixed by replacing Tailwind utility-dependent markup with explicit local CSS classes and native JS dropdown toggle/close handling in `resources/views/admin/layout.blade.php`.
- **Partnership Application Wizard Routes Added:** Applicant flow now includes staged uploads/submissions via `POST /application/loi`, `POST /application/form`, and `POST /application/receipt` in `PartnershipApplicationController`.
- **Admin Wizard Review/Approval Endpoints Added:** Admin partnership review flow now includes wizard endpoints for LOI/Form approve/reject, per-document ticks, and final approval, plus document viewing route `GET /admin/partnerships/{application}/document/{type}`.
- **Concessionaire Payment Field Rename (`reference_number` -> `or_number`):** Payment records now use `or_number` as the nullable receipt reference field. Code references in model/controller/views were updated. A compatibility migration adds/backfills `or_number` from legacy `reference_number` for existing data.
- **Cashier Payment Form OR Number UX:** Added optional OR Number input in `resources/views/cashier/payments.blade.php` with placeholder `e.g. 7919825 T` and helper note: `Enter the OR number from the physical AF No. 51-C receipt.`
- **Payment Receipt PDF Copy Update:** In `resources/views/pdf/payment-receipt.blade.php`, title is now `Payment Receipt`. Footer disclaimer now reads: `This is an internal payment record. The Official Receipt is AF No. 51-C issued by the Collecting Officer.`
- **Timezone Standardization to Philippine Time:** App timezone is now `Asia/Manila` in `config/app.php`. Cashier history `Recorded At` displays were updated to Manila time formatting in cashier history table and PDFs.
- **Admin Payments Actions + History Access:** Added admin routes for all-concessionaire payment history view/download and receipt download (`/admin/payments/history/view`, `/admin/payments/history/pdf`, `/admin/payments/{payment}/receipt`). Admin payments table now includes an `Action` column with `Download Receipt` per row.
- **Admin Payments Filter Dropdown Source Fix:** Concessionaire dropdown options are now generated client-side from rendered payment rows (`.concessionaire-name`) on `DOMContentLoaded` so options always match visible table data.

## RECENT UPDATES (June 15, 2026)
- **True Email-First Registration Implemented (No Immediate User Creation):** Registration submit now stores only pending data and sends a confirmation link. No `users` or `partnership_applications` record is created until token confirmation.
- **New Pending Registration Data Layer Added:** Created migration `2026_06_15_134011_create_pending_registrations_table.php` with `token` (unique), `first_name`, `last_name`, `email` (unique), bcrypt-hashed `password`, and `expires_at` (24 hours). Added `App\Models\PendingRegistration` with fillable fields, datetime cast, and `isExpired()` helper.
- **Custom Pending Registration Controller Added:** Added `App\Http\Controllers\Auth\PendingRegistrationController` with:
  - `store()` validation + duplicate non-expired pending check + `updateOrCreate` by email + mail send
  - `confirm()` token validation + expiration cleanup + existing-user guard + final user creation + concessionaire-only partnership creation + pending record deletion
- **Custom Confirmation Email Added:** Added `App\Mail\PendingRegistrationMail` using `Content(markdown: 'emails.pending-registration')` and `confirmUrl` based on `/register/confirm/{token}`. Added `resources/views/emails/pending-registration.blade.php` in the existing mail markdown component style.
- **Auth View Updates (Minimal):**
  - `resources/views/pages/auth/register.blade.php` now posts to `register.pending` and displays session success/error flashes.
  - `resources/views/pages/auth/login.blade.php` now shows a green inline success banner when `session('status') === 'account-created'` using `session('message')`, while preserving existing status modal behavior for other status values.
  - Added `resources/views/pages/auth/confirm-pending.blade.php` using the existing split-screen auth shell pattern.
- **Route Wiring for New Flow:** Added guest routes in `routes/web.php`:
  - `POST /register` -> `PendingRegistrationController@store` (`register.store`) to override direct Fortify registration submit behavior safely.
  - `POST /register/pending` -> `PendingRegistrationController@store` (`register.pending`).
  - `GET /register/confirm/{token}` -> `PendingRegistrationController@confirm` (`register.confirm`).
  - Fortify `GET /register` remains active for serving the registration page.
- **Fortify Registration Action Bypass Applied (Least Invasive):** `FortifyServiceProvider` now binds `Fortify::createUsersUsing(RejectRegistration::class)` with a new `App\Actions\Fortify\RejectRegistration` action, preventing accidental direct account creation via Fortify internals.
- **Verification Notes (Local):**
  - `php artisan migrate` succeeded; `pending_registrations` table created.
  - Route list confirms `POST /register` and `POST /register/pending` resolve to `PendingRegistrationController@store` and confirmation route is present.
  - Controller-flow verification confirmed:
    - submit creates pending row but no user row,
    - duplicate submit while pending is non-expired returns required duplicate message,
    - non-`@cvsu.edu.ph` confirm creates `role=concessionaire` + partnership row,
    - `@cvsu.edu.ph` confirm creates `role=student` + no partnership row,
    - re-clicking used token redirects to `/register` with invalid-link message.

- **Landing Page CMS for Admins Added:** Created a simple site settings CMS so admins can edit the public landing page from the dashboard. Added `site_settings` migration, `App\Models\SiteSetting` with `SiteSetting::get($key, $default)`, `SiteSettingSeeder` for hero/features/mission/vision/core values defaults, `AdminController@siteSettings` and `updateSiteSettings`, `GET/POST /admin/site-settings`, a new admin form at `resources/views/admin/site-settings.blade.php`, and a `Site Settings` sidebar link in `resources/views/admin/layout.blade.php`.
- **Landing Page Content Is Now Data-Driven:** Updated `resources/views/welcome.blade.php` so hero text, features copy, mission/vision, and core values read from `SiteSetting::get(...)` with fallbacks, keeping the landing page editable without changing the view again.
- **All Email Templates Migrated to Laravel Markdown Mail Layout:** Converted all 15 templates under `resources/views/emails/` from plain HTML wrappers to Laravel mail markdown components (`@component('mail::message')` + `mail::button`) for consistent default Laravel branding/styling and improved rendering across mail clients.
- **All Matching Mailables Switched to Markdown Rendering:** Updated all 15 related classes in `app/Mail/` to use `Content(markdown: 'emails....')` instead of `view:` to align with the new template format.
- **PartnershipRejectedMail Constructor Type Fix:** Updated `app/Mail/PartnershipRejectedMail.php` to accept `User|PartnershipApplication` (instead of `User` only) and made `rejectionReason` optional, with fallback from application `rejection_reason` and default `'Not specified.'`.
- **PartnershipRejectedMail Content Mapping Hardened:** Added model-aware name resolution in `content()` so the template receives correct `name` whether the mailer is instantiated with `User` or `PartnershipApplication`.
- **Tinker Manual-Test Clarification Added (Operational Note):** Manual send failures seen in Tinker were due to session state and command usage (undefined variables/new session, split chained calls). Valid test flow must define `$application` and `$user` in the same active Tinker session before calling `Mail::to(...)->send(...)`.

## RECENT UPDATES (June 15, 2026 — Uniform Stocks Follow-up)
- **Uniform Stocks Per-Size Architecture Corrected Back to JSON Columns:** Reverted the temporary `uniform_stock_sizes` table approach and standardized per-size data on `uniform_stocks` JSON columns:
  - `prices` = per-size price map (`XS`..`5XL`)
  - `sizes` = per-size quantity map (`XS`..`5XL`)
- **Detour Rollback Completed:** Rolled back and removed migration `2026_06_15_150000_create_uniform_stock_sizes_table.php`, deleted `app/Models/UniformStockSize.php`, removed `sizes()` relation from `UniformStock`, and removed all relation-based eager loading / lookups (`with('sizes')`, `loadMissing('sizes')`).
- **Controller Persistence Rewired to JSON Pattern:** `storeStock()` and `updateStock()` in both `AdminController` and `FacultyController` now read `price_xs...price_5xl` and `qty_xs...qty_5xl`, then write both JSON columns (`prices`, `sizes`) and keep `uniform_stocks.quantity` synchronized as the total of size quantities for uniforms.
- **Edit Prefill Source Restored:** Admin and faculty stock modals now prefill from JSON columns (`data-prices` from `prices`, `data-sizes` from `sizes`) instead of relation-derived arrays.
- **Public Products Campus Items Rollback:** Removed relation-dependent per-size pills from `resources/views/products/index.blade.php` that relied on `UniformStockSize`; cards now follow prior quantity/status behavior without relation assumptions.
- **Stock Detail Page Uses JSON Columns:** `resources/views/stocks/show.blade.php` now renders `PRICING BY SIZE` from `prices` JSON and `STOCK BY SIZE` from `sizes` JSON.
- **Admin Edit Modal Double-Scrollbar Fix:** Resolved nested scroll conflict by making modal shell non-scrolling and keeping scroll on modal body only (`max-height` adjusted to avoid simultaneous page/modal scrollbar behavior).
- **Faculty Per-Size Grid Layout Synced to Admin (Targeted View Fix):** In `resources/views/faculty/stocks.blade.php`, both Add and Edit per-size blocks now use admin's one-size-per-row structure:
  - single header row for `Prices by Size` / `Stocks by Size`
  - each size row = `[size label | price input | stock input]`
  - no 3-column grouped size layout and no duplicated heading pattern.

## AUDIT BASIS (June 15, 2026)
- Verified directly from workspace file contents after edits.
- Workspace still has no local git metadata available (`The workspace does not contain a git repository`), so audit is based on current source inspection.

## RECENT UPDATES (June 12, 2026)
- **Review Word Filter Wired Into Save Flows (Product + Store):** `app/Services/WordFilterService.php` is now actively used during review create/update. `app/Providers/AppServiceProvider.php` registers `\App\Services\WordFilterService::class` as a singleton. Both `ProductController` (`storeReview`, `updateReview`) and `ConcessionaireController` (`storeReview`, `updateReview`) now resolve the service and block submissions when inappropriate language is detected.
- **Review Validation UX for Comment Field Added:** Review forms now preserve user-entered comment text on validation failure and surface `comment` validation errors under the textarea on both product and concessionaire pages. Updated blades: `resources/views/products/show.blade.php` and `resources/views/concessionaires/show.blade.php`.
- **Admin User Deletion FK-Safe Fix for Concessionaire Payments:** `AdminController@destroyUser` now deletes related `concessionaire_payments` rows via `$user->concessionairePayments()->delete()` before deleting the user, preventing FK constraint failures on `concessionaire_payments.concessionaire_id -> users.id`.
- **User Model Relationship Confirmed for Payment Cascade:** `app/Models/User.php` includes `concessionairePayments(): HasMany` with foreign key `concessionaire_id`, and this relationship is now required by the admin user deletion flow.
- **Staff Stocks CRUD Is Active Under `/staff` Routes:** Current `routes/web.php` includes faculty-protected stocks endpoints (`staff.stocks.index/store/update/destroy/visibility`) wired to `FacultyController` stock methods and `resources/views/faculty/stocks.blade.php`.
- **Admin Transaction Logs Page Route Is Present:** `/admin/transaction-logs` (`admin.transaction-logs`) is currently registered and points to `AdminController@transactionLogs`, with UI in `resources/views/admin/transaction_logs.blade.php`.

## IMPLEMENTATION NOTE (June 12, 2026)
- **Staff Transaction/Checkout Source-of-Truth Drift to Verify:** Current routes under `/staff` call `FacultyController` methods (`transactionLogsIndex`, `uniformCheckoutIndex`, `storeUniformCheckout`), while current blades `resources/views/faculty/transaction_logs.blade.php` and `resources/views/faculty/uniform_checkout.blade.php` still render Livewire wrappers (`@livewire('cashier.transaction-logs')`, `@livewire('cashier-checkout')`). Both patterns exist in source; confirm intended canonical flow before further refactors.

## AUDIT BASIS (June 12, 2026)
- The workspace currently has **no local git repository metadata** available for `git diff`/history (`The workspace does not contain a git repository`).
- This handoff update is based on direct code inspection of current source files and recent file modification timestamps.

## RECENT UPDATES (June 4, 2026)
- **Registration Role Auto-Assignment for CvSU Emails:** Updated `app/Actions/Fortify/CreateNewUser.php` so new registrations using `@cvsu.edu.ph` are created as `role=student` with `is_approved=true`. Non-`@cvsu.edu.ph` registrations remain `role=concessionaire` with `is_approved=false`.
- **Partnership Application Creation Scoped to Concessionaires:** New registration now auto-creates `PartnershipApplication` only for non-`@cvsu.edu.ph` users (concessionaire path). Student registrations no longer receive a pending partnership application record.
- **Faculty Bell Notification Feature Added (Payment Feed):** Reused the concessionaire bell/dropdown UI block in `resources/views/faculty/layout.blade.php` (same visual classes/styles), but wired content to faculty payment notifications. Dropdown now lists unread concessionaire payments (`Payment Recorded`), shows business name + paid amount, uses `View Transaction Logs →` linking to `staff.transaction-logs`, and includes mark-as-read behavior on bell click.
- **Faculty Payment Notification Read State (DB + Route + Controller):** Added migration `2026_06_04_120000_add_payment_notifications_read_at_to_users_table.php` with `users.payment_notifications_read_at` timestamp. Added `markPaymentsRead()` + `getUnreadPayments()` in `FacultyController`, and route `POST /staff/notifications/mark-read` (`staff.notifications.mark-read`). Faculty controller view responses now include `$unreadPayments` and `$unreadCount` for faculty pages.
- **Faculty Concessionaires Page Replaced with Monthly Fee Tracking Table:** `resources/views/faculty/concessionaires/index.blade.php` was fully replaced (search bar, legacy concessionaire table, edit modal, and related JS removed). The page now shows only the Monthly Fee Tracking card/table pattern copied from admin, including status badges, avatar initials, monthly fee input, and per-row `Set Fee` action.
- **Faculty Monthly Fee Update Wiring:** Added `PATCH /staff/concessionaires/{id}/monthly-fee` (`staff.concessionaires.monthly-fee`) and `FacultyController@updateMonthlyFee`, mirroring admin validation/behavior for updating concessionaire `monthly_fee`.
- **Payment Due-Date Rule Changed (15th -> 1st) Across Modules:** Updated cutoff logic from 15th to 1st-era handling: unpaid concessionaires are now `due_soon` on days 25-31 and `overdue` on days 1-24. Applied in `CashierController`, `AdminController`, `FacultyController`, `AppServiceProvider`, and matching status branches in `admin/payments.blade.php` and `faculty/concessionaires/index.blade.php`.
- **Reminder Schedule and Reminder Copy Updated to 1st:** Scheduler changed in `routes/console.php` from `monthlyOn(12, '08:00')` to `monthlyOn(1, '08:00')` (verified as `0 8 1 * *`). Updated reminder text/date references from `15th` to `1st` in `SendPaymentDueReminders`, `PaymentDueReminderMail`, and concessionaire/cashier UI copy (`concessionaire/payments.blade.php`, `concessionaire/layout.blade.php`, `cashier/payments.blade.php`).
- **Supersedes Older 15th/12th Payment Notes:** Any earlier handoff notes referencing due date cutoff = 15th or reminder run day = 12th are now outdated and replaced by the June 4, 2026 updates above.

- **Faculty-Owned Uniform Module Wrappers Kept Intact:** The faculty pages for transaction logs and uniform checkout now act as thin wrappers that embed the restored Livewire components instead of reimplementing the backend flow. `resources/views/faculty/transaction_logs.blade.php` renders `@livewire('cashier.transaction-logs')`, and `resources/views/faculty/uniform_checkout.blade.php` renders `@livewire('cashier-checkout')`.
- **Livewire Components Restored After Accidental Deletion:** Recreated `app/Livewire/Cashier/TransactionLogs.php`, `resources/views/livewire/cashier/transaction-logs.blade.php`, and `resources/views/livewire/cashier-checkout.blade.php` so the original cashier Livewire behavior remains the source of truth for the two moved staff pages.
- **Dashboard Shell Rebuilt for Staff Livewire Pages:** Reworked the checkout and transaction-log presentation with the standard dashboard card shell, clean section headers, Tailwind table styling, and emerald action buttons/inputs while leaving Livewire bindings and checkout logic unchanged.
- **Faculty Sidebar Reflects Moved Uniform Tools:** Faculty navigation now includes Transaction Logs, Uniform Checkout, and Stocks; cashier sidebar no longer shows those entries.

## RECENT UPDATES (June 3, 2026)
- **Cashier Stocks Management Enabled (Shared Table):** Added cashier-side stock management using the same `uniform_stocks` table as admin (no new migration/table). New cashier routes under `/cashier/stocks`: index, create, update, delete, and visibility toggle.
- **Cashier Controller Stock CRUD + Logging:** Added `stocksIndex`, `storeStock`, `updateStock`, `destroyStock`, and `toggleStockVisibility` to `app/Http/Controllers/Cashier/CashierController.php`, including validation, image upload/delete via `storage/app/public/stocks/`, and `ActivityLog` entries (`stock_added`, `stock_updated`, `stock_deleted`, `stock_visibility_toggled`) tied to cashier user context.
- **Cashier Stocks Page Added:** Implemented `resources/views/cashier/stocks.blade.php` with stat cards, add-item form, searchable stock table, row actions dropdown, edit modal, delete confirmation modal, and status badges (Active/Archived), aligned to the admin stocks visual structure.
- **Cashier Sidebar + Breadcrumb Integration:** Added `Stocks` nav item to `resources/views/cashier/layout.blade.php` (between Uniform Checkout and Logout) and wired breadcrumb detection using `request()->routeIs('cashier.stocks*')`.
- **Cashier Stocks UI Parity Fix:** Resolved cashier layout regressions by adding missing table/search styles in `cashier/stocks.blade.php` (fixed oversized search icon and broken table spacing) so cashier stocks now mirrors admin stocks layout behavior.

## RECENT UPDATES (June 2, 2026)
- **Admin Stocks Table Consolidation (Visibility -> Status):** Replaced the old `Visibility` toggle column in `resources/views/admin/stocks.blade.php` with a dedicated `STATUS` column. Each row now shows a compact badge: `Active` (emerald) when `is_visible = true` and `Archived` (slate) when `is_visible = false`.
- **Actions-Only Row Controls:** Removed row-level visibility toggle controls and consolidated all row operations into the `ACTIONS` dropdown. Option labels were updated (`Edit Details`, `Archive Item`, `Restore Item`) and now adapt to current item state.
- **Dropdown Overflow/Clipping Hardening:** Updated the stocks table/card wrappers and actions cell layering to prevent clipping at the last rows. The floating menu now uses high-priority positioning classes (`absolute right-0 z-50 mt-1 w-36 rounded-md bg-white shadow-xl border border-slate-100 pointer-events-auto`) and preserved upward/downward Alpine direction switching.
- **Archive/Restore Backend Refactor (FK-Safe):** Updated `AdminController@confirmDelete` to stop hard-deleting stock rows and instead toggle `uniform_stocks.is_visible` for archive/restore. This preserves linked historical sales records and avoids foreign key constraint failures.
- **Obsolete Visibility Route/Handler Removed:** Removed the independent visibility toggle route (`/stocks/{stock}/visibility`) from `routes/web.php` and removed `toggleStockVisibility()` from `AdminController` to align backend behavior with the consolidated status/actions workflow.
- **Activity Logging + Flash Feedback:** Archive/restore actions now log as `stock_item_archived` / `stock_item_restored` with old/new visibility metadata, and return success flash banners shown in the admin layout.

## RECENT UPDATES (May 29, 2026)
- **Faculty UI Enhancement — Single-Page Modals:** Built native page-overlay modal experiences for both `Review Application` actions on `staff.partnerships.index` and `Edit Concessionaire` actions on `staff.concessionaires.index`. Replaced standard separate page linking with fully populated, responsive modals to streamline staff workflow. 
- **Faculty Modal Flash-State Handlers:** Redesigned response logic on form completions for staff tools — replacing plain session top banners with beautiful bounce-animated `Changes Saved / Success` overlay modals matching the system-wide visual pattern. 
- **Faculty Breadcrumbs Replaced Hardcoded Headings:** Stripped static page title headers and descriptive subtexts on all Faculty portal views (Dashboard, Concessionaires, Partnerships, History) and deployed dynamic `/Dashboard / {Current Page}` system breadcrumbs directly into the top navbar component exactly mirroring the concessionaire portal styles.
- **Improved Controller Eager Loading:** Inserted generic parameter eager-loading adjustments (`with('latestPartnershipApplication')`) across `FacultyController` arrays to enable fluid inline JS parameter propagation using `data-*` attributes for instant modal rendering without N+1 queries.
- **Faculty Route Prefix Migration Completed (`/faculty/*` → `/staff/*`):** Staff-facing faculty pages now run under `Route::middleware(['auth', 'faculty'])->prefix('staff')->name('staff.')`; redirects and Blade route calls now use `staff.*` names while keeping faculty controllers, middleware, and `resources/views/faculty/*` view paths unchanged.
- **Faculty Dashboard Charts Rewired to Real Data (ApexCharts):** Replaced placeholder chart data with DB-backed datasets in `FacultyController@dashboard` and `resources/views/faculty/dashboard.blade.php`. Current chart row now shows (1) **Applications Per Month** for the last 6 months and (2) **Application Status Distribution** for `Pending`, `Under Review`, `Approved`, `Rejected`, and `Registered`.

## RECENT UPDATES (May 24, 2026)
- **Faculty Layout Shell Rebuilt to Match Cashier/Concessionaire Pattern:** Rebuilt `resources/views/faculty/layout.blade.php` using the same Flowbite-style dark sidebar structure and top navbar pattern used by staff portals, including mobile drawer behavior, dark overlay backdrop support, user dropdown, and shared shell spacing.
- **Faculty Sidebar Theme Corrected to Campus Green:** Sidebar now uses the same dark green `#1a3c2e` styling (not navy/black) and includes EBA logo/system block, faculty identity block (`auth()->user()->name` + role badge), SVG nav links, Back to Main Site, and POST logout action.
- **Faculty Nav Route/Active-State Wiring Standardized:** Sidebar links now use `request()->routeIs()` active highlighting for Dashboard, Partnerships, Concessionaires, and History. Settings link is now conditional and only rendered if `staff.settings` exists.
- **Faculty Route Name Compatibility Guard Added:** Because current route names are `staff.partnerships.index` and `staff.concessionaires.index` (not `staff.partnerships` / `staff.concessionaires`), layout link targets now use route-existence fallback logic to prevent route errors.
- **Top Navbar Slot Added for Page Titles:** Added `@yield('page-title')` usage in faculty layout and wired faculty pages to set page titles for dashboard topbar display.
- **All Four Faculty Pages Wired to Shared Layout Shell:** Confirmed shell usage and title-slot wiring for:
  - `resources/views/faculty/dashboard.blade.php`
  - `resources/views/faculty/partnerships/index.blade.php`
  - `resources/views/faculty/concessionaires/index.blade.php`
  - `resources/views/faculty/history.blade.php`
- **Faculty Partnerships Search Converted to Instant Client-Side:** Removed Search/Clear buttons from `resources/views/faculty/partnerships/index.blade.php`; added in-page JS filtering by applicant name, email, and business name. `All Status` dropdown remains as-is (server-side submit behavior retained).
- **Faculty Concessionaires Search Converted to Instant Client-Side:** Removed Search/Clear buttons from `resources/views/faculty/concessionaires/index.blade.php`; added in-page JS filtering by business name, concessionaire name, and email.
- **No Functional Regression to Protected Actions:** Review actions on partnerships and Edit actions on concessionaires were intentionally preserved with existing routes and behavior.
- **No Backend Logic Changes for Faculty Pass:** No controller/route logic changes were made for this May 24 faculty shell/search update; changes are Blade/view-layer only.

## RECENT UPDATES (May 23, 2026)
- **Cashier Module Split into Three Views:** Cashier panel flow is now separated into dedicated pages for dashboard, payment recording, and payment history (controller methods and routes aligned to the split structure).
- **Cashier Payment Eligibility Bug Fixed (Paid But No Monthly Fee):** Fixed condition-order issue in cashier payment preparation logic where paid concessionaires with missing `monthly_fee` were incorrectly treated as `no_contract` and still showed Record Payment.
- **Cashier Store Validation Type Safety Fix:** Added strict integer casting before `in_array(..., true)` payment checks to prevent ID type-mismatch false negatives during already-paid validation.
- **Monthly Fee Column Added to Cashier Payments Table:** Added a dedicated Monthly Fee column in `resources/views/cashier/payments.blade.php` with formatted peso output and `Not Set` fallback.
- **Cashier History Filtering Moved to Client-Side for Instant UX:** Reworked payment history filtering from server-driven query filters to instant browser-side filtering.
- **Cashier History Filter Simplified to Single Search Input:** Removed From/To date filters, Payment Type filter, Apply button, and Clear Filters button; retained a single client-side search box for concessionaire name matching.
- **Cashier History Data Load Updated for Client Filtering:** `CashierController@historyIndex` now loads the complete recent payment set used by the page-level instant filter instead of request-filtered + hard-limited results.
- **Cashier Dashboard Redesign Iterations (Prototype + Revert):** Multiple dashboard chart/stat-card redesign passes were prototyped during session, including trendline/sparkline variants and chart-type swaps. Final note: `resources/views/cashier/dashboard.blade.php` was manually reverted by user afterward, so current source reflects the reverted baseline.

## RECENT UPDATES (May 22, 2026)
- **Admin Partnerships — Contract Period Flow Simplified:** Removed contract-period edit mode in the admin modal and replaced it with always-visible Start Date and End Date inputs plus a direct Save action. Contract period save is now allowed for `pending`, `under_review`, `approved`, and `registered` statuses. Yearly edit-limit enforcement was removed from the save flow.
- **Admin Partnerships Modal Visual Overhaul:** Reworked `resources/views/admin/partnerships.blade.php` modal to a wider layout (`640px`) with redesigned identity header, bordered/accented section cards, pill-style Download actions in document rows, softer Faculty Review/Proposal panels, and a sticky action footer.
- **Admin Partnerships Data/Action UX Cleanup:** Removed the display-only Application Path row from Application Info and removed inline table-level Approve actions from the list view. Actions column now surfaces View only; approval remains inside the modal.
- **Admin Partnerships Reject Flow Split Modal:** Reject action now opens dedicated `#rejectModal` (with applicant context + required reason) instead of inline reject form injection. Cancel closes only the reject modal while Application Details remains open.
- **Admin Payments Instant Search Simplification:** Removed legacy filter-bar controls and kept a single instant client-side search box (`Business name or concessionaire`) for payment row filtering.
- **Admin Reviews Filter UX Refactor:** Per-Concessionaire Ratings filter row now uses a narrower Search Business field beside Flag Status. Recent Reviews Feed now filters fully client-side (search + concessionaire + type + exact rating), removed Apply/Clear actions, switched rating options to exact 1-5 stars, and updated showing-count badge live.
- **Admin Reviews Controller Filter Removal:** `AdminController@reviewsIndex` no longer applies request-driven server filters for concessionaire/type/rating. It now loads all recent reviews and relies on Blade-side JavaScript for instant filtering.
- **Admin Stocks Page Redesign:** Rebuilt `resources/views/admin/stocks.blade.php` with top stat cards (total items, quantity, visible, hidden + low-stock indicator), refreshed item form styles, table thumbnail/badge cleanup, modal-based edit workflow, and instant table search.
- **Landing Page Motion + Background Transition Pass:** Expanded animations and easing across the public landing page, then removed section-level scroll-lift artifacts that caused dotted backgrounds to shift while scrolling. Added smooth dotted-to-solid gradient blending on Hero and Campus Concessionaires sections (top/bottom transitions) and kept navigation behavior unchanged.

## RECENT UPDATES (May 21, 2026)
- **Partnership Application Document Upload Modal Redesign:** Replaced individual upload buttons with a single "Upload Files" button that opens an animated modal. Modal displays all three document types (MOA, Contract, Letter of Intent) with status badges, current file names, and individual upload forms. Added smooth entrance/exit animations with `cubic-bezier` easing, backdrop blur effects, and transition from upload modal to success modal on submission. Implemented sessionStorage tracking for seamless modal transitions.
- **Registration Success Modal Redesign:** Converted registration successful modal from wide layout (460px) to standard compact design (400px max-width). Added circular green checkmark icon, centered layout, and smooth entrance animations with bounce effect using `cubic-bezier(0.175, 0.885, 0.32, 1.275)`. Modal now matches the system-wide success modal design pattern.
- **Cashier Dashboard Button Icon Enhancement:** Added emoji icons to cashier payment buttons for better visual clarity: 📋 clipboard emoji for "View" and "View History" buttons, 💾 floppy disk emoji for "Download History" and "Download Receipt" buttons across all cashier payment tables and action rows.
- **Product Review Section Complete Redesign:** Removed inline "Update Review" and "Delete Review" buttons. Created "Your Review Display" card with gradient green background showing user's star rating and comment. Added compact, centered "Edit Review" button that opens an animated modal. Edit modal features star rating selector, comment textarea, "Delete Review" and "Update Review" actions. Delete action opens a confirmation modal with warning icon. Success modal appears after review submission/update with smooth transitions. All modals include entrance/exit animations, backdrop blur, click-outside-to-close, and Escape key support. Fixed star rating display bug where all 5 stars were showing as filled regardless of actual rating. Modal width set to 500px with centered, compact buttons.

## RECENT UPDATES (May 16, 2026)
- **Automated Payment Due Reminder — Scheduler Wired:** `SendPaymentDueReminders` command already existed but was never scheduled. Wired it to `routes/console.php` using `monthlyOn(12, '08:00')` so it fires on the 12th of every month at 8:00 AM. Removed the internal day-12 self-guard from the command so scheduling controls timing. Verified via `php artisan schedule:list` — registered as `0 8 12 * *`. No new tables, columns, or mailables added.

## RECENT UPDATES (May 5, 2026)
- **Concessionaire Payments Status Banner + Sidebar Bell:** Added `paid / due_soon / overdue` banner on `resources/views/concessionaire/payments.blade.php` using new flags (`hasPaidThisMonth`, `isDueSoon`, `hasOverduePayment`) passed from `AppServiceProvider`. Moved the bell into the concessionaire sidebar profile row and show a badge when due soon or overdue.
- **Fortify Auth Views + Split-Screen Redesign:** Created `resources/views/pages/auth/forgot-password.blade.php` and `resources/views/pages/auth/reset-password.blade.php`, fixed undefined `$request` by using `request()` helpers, and redesigned auth pages into a split-screen layout with `public/images/vector-2.JPG` and `public/images/vector-3.JPG`.
- **Public Navbar Mobile Toggle + Containers:** Applied the welcome-style navbar (mobile toggle, scroll shadow) to public pages, including `products/index.blade.php`, `products/show.blade.php`, `concessionaires/index.blade.php`, and `concessionaires/show.blade.php`. Standardized container widths to `max-width: 1200px` with mobile padding and added small-screen grid collapse rules.
- **Auth Navbar Alignment:** Updated auth pages to use the welcome navbar block and the same mobile menu JS for consistency with public pages.
- **Registration Split Name Fields:** Registration now uses separate First Name and Last Name fields; `users.name` is derived as `first_name . ' ' . last_name`.
- **Partnership Rejection + Resubmission:** Added soft rejection flow with `rejection_reason`, admin reject route with required reason, applicant rejection banner + resubmission back to `pending`, and `PartnershipRejectedMail` with reason + resubmit CTA.
- **Concessionaire Sidebar Payment Bell Details:** Sidebar bell now uses `AppServiceProvider` view composer flags (`$hasOverduePayment`, `$isDueSoon`, `$hasPaidThisMonth`), shows an orange dot for due soon/overdue, and links to `/concessionaire/payments` (no new DB table).
- **Payment Status Logic — Four States:** Standardized `paid`, `due_soon`, `overdue`, `no_contract` across Cashier/Admin logic and badges.
- **Concessionaire Payments Banner States:** Banner colors now map to green (paid), yellow (due soon before 15th), and red (overdue after 15th).
- **Auth Split-Screen Coverage:** Login, register, forgot password, and reset password now use split-screen layouts with full-bleed vector imagery, public navbar, and mobile behavior that hides the image panel below 768px.
- **Forgot + Reset Password Views:** Fortify password reset views added with no new controller/mailable; reset view uses `request()` to avoid the undefined `$request` error.
- **Landing Features Section Redesign:** Platform features section updated with an elevated center card, icon containers, and unified typography/spacing.

## RECENT UPDATES (April 22, 2026)
- **Faculty Dashboard Added + Redirect Updated:** Added `/staff/dashboard` as the faculty landing page with summary cards, chart cards, and quick links (`FacultyController@dashboard`, `resources/views/faculty/dashboard.blade.php`). Updated faculty post-login redirect to `route('staff.dashboard')` in `LoginResponse.php`.
- **Faculty Sidebar Layout Upgrade:** Reworked `resources/views/faculty/layout.blade.php` to match the concessionaire dark-sidebar shell (profile block, active nav states, Back to Main Site, bottom Log Out). Added Dashboard link above Partnerships.
- **Staff Dropdown Link Consolidation:** Simplified faculty/staff profile dropdown links by consolidating separate Partnerships + Concessionaires entries into a single Dashboard entry in public/desktop/mobile menu variants.
- **Landing Hero Redesign:** Replaced top hero in `resources/views/welcome.blade.php` with updated campaign copy and CTA structure (Your Campus · Marketplace · All in One Place), switched CTAs to Products/Concessionaires, and added right-side vector visual.
- **Public Background Tone Refresh:** Updated public page-level backgrounds from bright white to warm neutral tones (stone-like `#F5F5F4` / `#FAFAF8`) across `welcome.blade.php`, `products/index.blade.php`, `products/show.blade.php`, `concessionaires/index.blade.php`, and `concessionaires/show.blade.php`, while keeping cards/surfaces white for contrast.
- **Landing Footer Content Refresh + Logo Sizing Fix:** Updated footer quick links/services in `welcome.blade.php` to active system routes, kept dynamic copyright year, and fixed footer logo distortion by preserving aspect ratio.

## PROJECT OVERVIEW
A Laravel-based campus Information System for CvSU Trece Martires City Campus.
Current scope focuses on concessionaire partnerships, products, reviews, uniform stocks, and payments.

- **Framework:** Laravel 12
- **Frontend:** Blade, Livewire/Volt, Tailwind CSS
- **Database:** MySQL (local: eba_capstone)
- **Auth:** Laravel Fortify
- **Email:** Gmail SMTP (production) / Mailtrap (local testing)
- **Queue:** sync (both environments)
- **Live URL:** https://eba.cvsutrece.com
- **Local:** http://127.0.0.1:8000

---

## RECENT UPDATES (April 20, 2026)
- **Cashier Payments Module (Phase 2):** Added full payment history features on `cashier/payments` including per-concessionaire history view/download and global history view/download using dedicated DomPDF templates and cashier routes.
- **Cashier Filters for Recent Payment History:** Added server-side filtering for recent payment history by concessionaire, payment type, date range, and keyword search so cashier can quickly inspect transactions.
- **Cashier Payments UI Structure Cleanup:** Reworked cashier payments table actions by separating payment controls and action controls, and introduced a dedicated Payment History section with clearer hierarchy.
- **Payment Recording Limit Update:** Updated cashier payment validation rule from max 2 payments/month to max 1 payment per concessionaire per calendar month.
- **Cashier Success Modal:** Replaced inline payment-success flash with a modal to improve visibility and reduce layout shifts.
- **Login Registration-Success Modal:** Converted the registration success status on `resources/views/pages/auth/login.blade.php` from inline alert to a modal presentation for consistency.
- **Partnership Route Consolidation + Upload Redirect Fix:** Consolidated onboarding access to `/settings/application`; legacy `/partnership/status` now redirects there, and partnership upload redirects now consistently return to `settings.application`.
- **Application Page Design Enhancement:** Enhanced `resources/views/partnerships/settings-application.blade.php` visual design and improved section clarity for applicant info + document upload workflow.
- **Choose File UI Enhancement:** Replaced default file input styling with improved custom file-picker styling on the application page.
- **Concessionaire Sidebar Navigation Update:** Added a `Back to Main Site` link in `resources/views/concessionaire/layout.blade.php` sidebar for faster public-site navigation.
- **Fixed Monthly Due-Date Status System (Superseded):** Earlier pass used a 15th-based cutoff. Current active rule is the 1st-cycle model (`due_soon` days 25-31, `overdue` days 1-24) as documented in newer updates.
- **Concessionaire Data Wipe Recovery (Local):** Restored local concessionaire records after accidental data wipe via Tinker-based cleanup/recovery flow and verified role/state consistency.

## RECENT UPDATES (April 19, 2026)
- **Concessionaire Sidebar Layout Stabilization:** Fixed RouteNotFound exception by correcting sidebar Settings route from `settings.profile` to `profile.edit` in `resources/views/concessionaire/layout.blade.php`.
- **Concessionaire Products Page Cleanup:** Removed leftover legacy profile-shell markup from `resources/views/concessionaire/products.blade.php` (cover graphic, profile header, review count, tabs block). Products page now contains only product-management content (action row, filters, grid, modals, scripts).
- **Concessionaire Layout Topbar Removal:** Removed the white topbar strip (title + user avatar row) from `resources/views/concessionaire/layout.blade.php`, including related CSS and HTML, so content starts directly under the main shell.
- **Concessionaire Reviews Redesign + Data Merge:** Rebuilt `resources/views/concessionaire/reviews.blade.php` to admin-style table card layout with stat pills, server-side filters, target column, and pagination. Updated `ConcessionaireController@reviews` to merge store reviews (`ConcessionaireReview`) and product reviews (`ProductReview`) for the logged-in concessionaire, with GET filters `type`, `min_rating` (exact star match), and `search`.
- **Admin Dashboard ApexCharts Integration:** Added 4 responsive ApexCharts to `resources/views/admin/dashboard.blade.php` in a 2x2 card grid below stat cards: Application Status Breakdown (pie), Monthly Payments (bar), Applications Over Time (area), and Top Concessionaires by Reviews (horizontal bar). Added ApexCharts CDN and chart init scripts using `@json()` data.
- **Admin Dashboard Controller Data:** Extended `AdminController@dashboard` to compute and pass `$applicationStatusData`, `$monthlyPaymentsData`, `$applicationsTrendData`, and `$topConcessionairesData` for the new charts.

## RECENT UPDATES (April 12, 2026)
- **Review Permissions Fix:** Pending concessionaires (`is_approved = false`) were incorrectly blocked from submitting reviews. Removed the pending-concessionaire denial from `ProductController` and `ConcessionaireController` review methods. Blade own-product guard added to `products/show.blade.php`. Pending concessionaires can now review other stores/products like students.
- **Admin Reviews Module:** New page at `/admin/reviews` with three sections: summary stat cards (total reviews, averages, needs-attention count), per-concessionaire ratings table sorted worst-first with Low Rating flag for averages below 3.0, and a paginated recent reviews feed with inline delete moderation. Filter controls added: client-side business name search + flag dropdown for the ratings table; server-side GET filters (concessionaire, type, min rating, comment search, clear) for the feed.
- **Admin Partnerships Modal Redesign:** `admin/partnerships.blade.php` modal rebuilt into sectioned layout — identity header with avatar/name/status badge, Application Info card, Contract Period card, Documents checklist card, Faculty Review card, Proposal card, and a separated action footer. Removed leftover debug lines (`contract_period_start: Invalid Date`, etc.).
- **Contract Period Edit Flow:** Edit button added to Contract Period card for approved/registered applications only. Pending/under review applications show "Contract period for online applicants is set during approval." instead. Path A post-approval edit block removed. Yearly edit limit enforced: max 2 edits per calendar year per application via new `contract_period_edit_count` and `contract_period_last_edited_year` columns. Remaining edits shown in modal. Approval flow resets counters to 0. "Invalid Date" display bug fixed — modal JS now handles ISO timestamp format correctly. Page reloads after successful save so updated dates display immediately.
- **Admin Partnerships Stat Cards:** Removed Registered and Expired cards from the top summary row. Only Pending, Approved, and Rejected are shown.
- **Concessionaire Dashboard Redesign:** Full visual overhaul of `/concessionaire` dashboard. New layout with stats row (average rating, customer reviews, products, payments this month), Ratings Snapshot with bar chart, Recent Customer Reviews feed, and Recent Products grid. Quick Actions section removed. Scroll artifact on nav tabs removed.
- **Concessionaire Navigation Fix:** `concessionaire/layout.blade.php` now uses `@include('partials.public-nav-links')` matching the faculty layout pattern. Old custom hamburger dropdown and its JS removed.
- **Customer Reviews Bug Fix:** `ConcessionaireReview` tab on the concessionaire dashboard was showing "No Reviews Yet" despite reviews existing. Query mismatch identified and fixed — tab now correctly queries `ConcessionaireReview` with `concessionaire_id = auth()->id()`.
- **Concessionaire Products Page Redesign:** Products page rebuilt inside the concessionaire profile shell (same cover/avatar/tabs layout as Reviews tab). Card design updated: large square image, overlaid category and availability badges, clean typography, small Edit/Delete text links. Search/filter bar added above grid matching public products page style (text search, category dropdown, sort dropdown). Header text/stat pills removed — only Add Product button remains. Inter font registered via `@font-face` from `public/fonts/web/` and applied to the page. Server-side GET filtering scoped to logged-in concessionaire's own products.

---

## ROLE SYSTEM

Valid roles are FIVE:
- `admin` — Faculty/staff. Login ONLY via /admin/login
- `cashier` — Campus staff. Login via /login, navigates to /cashier/payments
- `concessionaire` — Default role on registration
- `student` — Public pages + authenticated review writing only
- `faculty` — Campus faculty/staff. Login via /login, navigates to /staff/dashboard

### Role boundaries:
- Admin → /admin only, redirected away from main site
- Cashier → /cashier/payments, main site browsing, 403 on /admin
- Faculty → /staff/dashboard, main site browsing, 403 on /admin
- Concessionaire (approved) → /concessionaire dashboard + main site
- Concessionaire (pending, `is_approved = false`) → main site browsing allowed, blocked from /concessionaire tools
- Student → public browsing + can write reviews, blocked from /concessionaire and /admin
- Guests → public pages only

### Partnership Access Rules:
- New registrations with `@cvsu.edu.ph` are created as `role=student`, `is_approved=true`
- Non-`@cvsu.edu.ph` registrations are created as `role=concessionaire`, `is_approved=false`
- A pending partnership application is auto-created only for concessionaire registrations
- Applicants manage documents through `/application` (standalone page)
- Legacy `/settings/application` and `/partnership/status` now redirect to `/application`

### Cashier Access
- Login: /login (main site login page)
- After login: redirected to /cashier/payments
- Can: record payments for concessionaires, view all concessionaires and their payment history, manage uniform stocks via `/cashier/stocks`, and browse main site public pages
- Cannot: access /admin, approve partnerships, write reviews, access concessionaire tools

**Cashier profile dropdown nav:**
Home · Payments · Settings · Log out

**Redirect implementation (cashier post-login):**
- Class: `app/Http/Responses/LoginResponse.php`
- Implements: `Laravel\Fortify\Contracts\LoginResponse`
- Logic: if `auth()->user()->role === 'cashier'` → redirect to `/cashier/payments`, else default Fortify redirect
- Registered in: `app/Providers/FortifyServiceProvider.php` via `$this->app->singleton(LoginResponse::class, \App\Http\Responses\LoginResponse::class)`

---

### Faculty Access
- Login: /login (main site login page)
- After login: redirected to /staff/dashboard
- Can: review partnership applications, upload MOA/Contract/LOI documents, submit recommendations, update concessionaire records, view own action history, browse main site public pages
- Cannot: approve or reject applications (admin only), manage user accounts or roles, access /admin, view system logs, record payments, manage stocks

**Faculty profile dropdown nav:**
Home · Dashboard · History · Settings · Log out

**Redirect implementation (faculty post-login):**
- Same `LoginResponse.php` — if `auth()->user()->role === 'faculty'` → redirect to `/staff/dashboard`

**Faculty recommendation flow:**
1. Concessionaire submits application
2. Faculty reviews on `/staff/partnerships/{id}` — uploads docs, selects `Recommend Approval` or `Recommend Rejection`, adds notes
3. Admin sees the faculty recommendation panel in `/admin/partnerships` modal before making the final approve/reject call
4. Application status does NOT change when faculty submits a recommendation — admin makes the final call

---

## MIDDLEWARE
- `admin` — blocks non-admin from /admin (403)
- `concessionaire` — blocks non-concessionaire and unapproved concessionaire from /concessionaire
- `cashier` — blocks non-cashier from /cashier routes (403)
- `faculty` — blocks non-faculty from /staff routes (403)
- `RestrictAdminToAdminPanel` — redirects admin away from main site with flash message
- `LocalEnvOnlyMiddleware` — blocks /dev routes in production (403)
- `EnforceValidRole` — global middleware, blocks any user whose role is not in the valid roles list [admin, cashier, concessionaire, student, faculty], logs warning, aborts 403

---

## MODULES

### 1. COURT RESERVATION / REFUND MODULE
Status: removed from current system scope.

All reservation/refund routes, controllers, views, and related admin pages were removed in the March 31, 2026 refactor.

---

### 2. PARTNERSHIP / CONCESSIONAIRE APPLICATION
**Current flow (single onboarding model):**
- User registers with non-`@cvsu.edu.ph` email → role is `concessionaire`, pending approval
- System auto-creates pending partnership application for concessionaire registrations
- User registers with `@cvsu.edu.ph` email → role is `student`, approved, and no partnership application is created
- User can browse public pages while pending
- User uploads signed documents (MOA, Contract, optional LOI) from `/application`
- Admin approves/rejects in `/admin/partnerships`
- Approval sets `users.is_approved = true` and enables concessionaire tools

**Key files:**
- `PartnershipApplicationController.php`
- `AdminController.php` (approvePartnership, rejectPartnership, uploadPartnershipDocument, saveContractPeriod)
- `resources/views/partnerships/` — applicant views
- `resources/views/admin/partnerships.blade.php` — admin panel

**Application statuses:** pending → approved → registered → rejected → expired

**Documents:**
- `moa_path` — Memorandum of Agreement (required)
- `contract_path` — Contract (required)
- `letter_of_intent_path` — Letter of Intent (optional)
- Stored: `storage/app/public/partnership_letters/{user_id}/`

**Applicant pages:**
- `/application` — redesigned two-section page (see below)
- `/settings/application` and `/partnership/status` — legacy redirects to `/application`

**Application Page Redesign:**

Section 1 — Applicant Information Form
- Fields: First Name, Last Name, Email (read-only), Business Name, Phone Number (PH format), Brief Business Proposal (50-1000 chars)
- Editable only while status = pending
- Read-only with notice once submitted
- Saved via PATCH /application/info
- New columns on partnership_applications:
  - first_name (string nullable)
  - last_name (string nullable)
  - phone_number (string nullable)
  - business_proposal (text nullable)

Section 2 — Signed Documents Checklist (existing, unchanged)
- Shows soft warning if Section 1 is incomplete
- Does not block uploads

New route: PATCH /application/info 
           → PartnershipApplicationController@updateApplicantInfo
           → name: application.info

New migration: AddApplicantInfoToPartnershipApplications

Bug fixed: business_name was being auto-populated from the username on registration. Removed from CreateNewUser.php and ensureApplicationForUser(). Business name is now only set when the concessionaire explicitly fills it in.

**Rejection + Resubmission (Soft Rejection):**
- New migration: `add_rejection_reason_to_partnership_applications` (adds `rejection_reason`)
- New route: `POST /admin/partnerships/{id}/reject` → `AdminController@rejectPartnership`
- New route: `POST /application/resubmit` → `PartnershipApplicationController@resubmitApplication`
- Admin rejection requires a reason (separate reject modal in admin partnerships page)
- Applicant sees rejection banner with reason on `/application`
- Resubmission flips status back to `pending` and clears `rejection_reason`
- `PartnershipRejectedMail` sends the reason with a resubmit CTA

**Contract Period:**
- `contract_period_start` and `contract_period_end` on partnership_applications
- Daily scheduler checks expiry: `php artisan contract:check-expiry`
- Warnings at 30, 7, 1 day before expiry
- On expiry: role stays concessionaire but `is_active_concessionaire = false`
- Legacy accounts (no contract period) → always treated as active

**Admin Document Upload — All at Once:**
Admin can now upload all three documents in one submission from the /admin/partnerships modal.

- Individual upload buttons remain for single-document updates
- Upload All submits via fetch (AJAX) — no page reload during upload
- Validates MOA + Contract required, LOI optional client-side
- Shows loading state, inline success/error feedback
- Reloads page on success

New route: POST /admin/partnerships/{id}/upload-all-documents
           → AdminController@uploadAllPartnershipDocuments
           → name: admin.partnerships.upload.all

New controller method: uploadAllPartnershipDocuments()
- Validates all three files
- Stores to same path: storage/app/public/partnership_letters/{user_id}/
- Logs each file to ActivityLog
- Sends AdminDocumentUploadedMail on success
- Returns JSON — handles partial failures per file

---

### 3. PRODUCTS & REVIEWS
**Product management:**
- Concessionaires manage products at `/concessionaire/products`
- CRUD: create, edit, delete, toggle availability
- Images stored: `storage/app/public/products/{concessionaire_id}/`
- Categories: food, beverage, snack

**Public browsing:**
- `/products` — all products, filterable by concessionaire/category/sort
- `/products/{id}` — individual product with reviews
- `/concessionaires` — browse all active concessionaires
- `/concessionaires/{id}` — public concessionaire profile

**Two separate review systems:**

1. **Product Reviews** (ProductReview model)
   - Tied to products + users
   - Left on `/products/{id}` page
   - One review per user per product

2. **Store/Customer Reviews** (ConcessionaireReview model)
   - Tied to concessionaires + users
   - Left on `/concessionaires/{id}` page → Customer Reviews tab
   - One review per user per concessionaire

**Review restrictions:**
- Concessionaires cannot review their own products/store
- Admins cannot leave reviews
- Guests can read but not write reviews

**Public concessionaire page tabs:**
- Overview (about + ratings snapshot)
- Products (product grid)
- Customer Reviews (store-level reviews)
- Product Reviews (aggregated product reviews)
- NO MOA tab on public page

---

### 4. UNIFORM STOCKS ⭐ NEW MODULE
**Purpose:** Display available campus uniform/item stocks to students on the public products page.

**Admin management:** `/admin/stocks`
- Add new stock items with name, image upload, quantity, visibility toggle
- Edit existing item quantities using a dedicated modal
- Toggle visibility (show/hide on public page)
- All changes logged to Activity Log

**Public display:** Bottom of `/products` page under "Campus Available Items" banner
- Shows items where `is_visible = true`
- Stock status indicators:
  - Green + "Available" → quantity > 10
  - Orange + "Low Stock" → quantity 1–10
  - Red + "Out of Stock" → quantity = 0 (card dimmed)
- Grid layout matching the product cards above it

**Key files:**
- `app/Models/UniformStock.php`
- `app/Http/Controllers/AdminController.php` (stocks methods)
- `resources/views/admin/stocks.blade.php`
- `resources/views/products/index.blade.php` (stocks display section)
- `database/migrations/2026_03_22_120000_create_uniform_stocks_table.php`
- `database/migrations/2026_03_22_130000_add_image_to_uniform_stocks_table.php`
- `database/seeders/UniformStockSeeder.php`

**Database table: uniform_stocks**
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| item_name | string | |
| image | string nullable | storage path |
| quantity | integer | default 0 |
| is_visible | boolean | default true |
| timestamps | | |

**Image storage:** `storage/app/public/stocks/`

**Routes:**
- `GET /admin/stocks` — list all stocks
- `POST /admin/stocks` — add new item
- `PATCH /admin/stocks/{id}` — update quantity
- `DELETE /admin/stocks/{id}` — archive/restore behavior via controller flow (no hard delete)

**Seeded default items:** Polo, Blouse, Slacks, PE Shirt, PE Pants, NSTP Shirt, Books

---

### 5. USER PROFILE & SETTINGS
**Settings pages (Livewire/Volt):**
- `/settings/profile` — name, profile photo upload (email shown as read-only)
- `/settings/password` — password change

**Standalone application page:**
- `/application` — NOT inside settings tabs; standalone page with status and document uploads

**Removed settings tabs:**
- Two-factor auth — removed
- Appearance — removed
- Notifications — removed (notification_preferences column still exists in DB for mail checks)
- Delete account button — removed

**Profile photos:**
- Stored: `storage/app/public/avatars/{user_id}/`
- Column: `users.profile_photo`
- Fallback: initials avatar everywhere

**IMPORTANT:** Email field is read-only in settings — do not allow editing as role detection depends on email domain.

**Notification preferences (JSON column on users — used by mail only):**
```json
{
  "email_partnership_updates": true,
  "email_contract_expiry": true,
  "in_app_notifications": true
}
```

---

### 6. QR CODES (Static)
Generate via: `php artisan qr:generate`
Stored in: `public/qrcodes/`
- `qr_home.png` → https://eba.cvsutrece.com/
- `qr_products.png` → https://eba.cvsutrece.com/products
- `qr_concessionaires.png` → https://eba.cvsutrece.com/concessionaires
- `qr_partnership_status.png` → https://eba.cvsutrece.com/partnership/status

Package: `chillerlan/php-qrcode`

---

### 7. ACTIVITY LOG
- Logs all significant actions with user, action, subject, details, timestamp
- Used across: partnerships, products, contract expiry, stocks, reviews, payments
- Admin panel: `/admin/activity-logs`

---

### 8. DEV TEST TOOL ⚠️ DELETE BEFORE DEPLOYING
- Route: `/dev/test-accounts`
- Creates test accounts for any role
- Protected by `LocalEnvOnlyMiddleware` (403 in production)
- Files: `DevController.php`, `test-accounts.blade.php`

---

### 9. UNIFIED PUBLIC NAVIGATION
- Extracted all guest nav links into a single shared partial
- File: resources/views/partials/public-nav-links.blade.php
- Included in: welcome.blade.php, products/index.blade.php, products/show.blade.php, concessionaires/index.blade.php, concessionaires/show.blade.php
- Consistent guest nav on all public pages:
  Home · About · Products · Concessionaires · Partner With Us · Log in
- Route-aware active classes highlight current page
- Authenticated users still get profile dropdown instead of Log in

---

### 10. PAYMENT MODULE ⭐ NEW

**Purpose:** Record and track concessionaire payments. Cashiers record payments on behalf of concessionaires. Admins view all records. Concessionaires view their own history.

**New database table: concessionaire_payments**
| Column                     | Type            | Notes                    |
|----------------------------|-----------------|--------------------------|
| id                         | bigint PK       |                          |
| partnership_application_id | FK nullable     | links to applications    |
| concessionaire_id          | FK → users      | the concessionaire       |
| recorded_by                | FK → users      | the cashier              |
| amount                     | decimal(10,2)   |                          |
| payment_date               | date            |                          |
| payment_type               | string          | cash/check/bank_transfer |
| or_number                  | string nullable | OR number from AF No. 51-C |
| notes                      | text nullable   |                          |
| timestamps                 |                 |                          |

**New model:** app/Models/ConcessionairePayment.php

**New routes:**
- GET  /cashier/payments  → CashierController@paymentsIndex
- POST /cashier/payments  → CashierController@storePayment
- GET  /concessionaire/payments → ConcessionaireController@paymentsIndex
- GET  /admin/payments    → AdminController@paymentsIndex

**New controller:** app/Http/Controllers/Cashier/CashierController.php

**New views:**
- resources/views/cashier/payments.blade.php
- resources/views/concessionaire/payments.blade.php
- resources/views/admin/payments.blade.php

**New mailable:** PaymentRecordedMail
- Sent to concessionaire when cashier records a payment
- Wrapped in try/catch

**Sidebar links added:**
- Admin sidebar: Payments (under Partnerships)
- Concessionaire nav: Payments

**ActivityLog action:** 'payment_recorded'

**Currency format:** ₱X,XXX.XX (Philippine Peso)

---

#### Payment Module — Phase 2 ⭐ NEW

**Receipt PDF Generation**
- Cashier can download a PDF receipt for any recorded payment
- Route: `GET /cashier/payments/{payment}/receipt` → `CashierController@downloadReceipt`
- Concessionaire can also download their own receipts
- Route: `GET /concessionaire/payments/{payment}/receipt` → `ConcessionaireController@downloadReceipt`
- PDF generated server-side (Blade → PDF via DomPDF or similar)
- Receipt includes: concessionaire name, amount, payment date, type, OR number, recorded-by cashier, EBA logo

**Overdue Payment Flags + Monthly Fee Config**
- Admin can set a `monthly_fee` amount per concessionaire
- Route: `PATCH /admin/users/{id}/monthly-fee` → `AdminController@updateMonthlyFee`
- Field: `users.monthly_fee` (decimal, nullable — null = no fee configured)
- Admin dashboard flags concessionaires with overdue payments (no payment recorded in current month and `monthly_fee` is set)
- Overdue indicator shown in `/admin/payments` and `/admin/users` panel

**Payment Recording Limit (max 1/month)**
- Cashier cannot record more than 1 payment per concessionaire per calendar month
- Enforced in `CashierController@storePayment` — returns validation error if limit reached
- Prevents duplicate or excess payment entries

**Permanent Cashier Payment History Table**
- `/cashier/payments` now shows a persistent table of all payments recorded by the logged-in cashier (below the record payment form)
- Table columns: Concessionaire, Amount, Date, Type, OR Number, Receipt
- Paginated, most recent first

**Payment Status Logic (Four-State)**
- Cashier and admin status logic uses `paid`, `due_soon`, `overdue`, `no_contract`
- `due_soon` applies on days 25-31 if unpaid; `overdue` applies on days 1-24 if unpaid
- Status badges appear on `admin/payments.blade.php` and `cashier/payments.blade.php`

**Concessionaire Payments Status Banner**
- `resources/views/concessionaire/payments.blade.php` shows green/yellow/red banner states for paid, due soon, and overdue

---

### 11. STAFF ACCOUNT CREATION ⭐ NEW

**Purpose:** Admin creates cashier accounts directly from the admin panel. Cashiers are campus staff and do not self-register publicly.

**How it works:**
- Admin goes to /admin/users
- Clicks "Create Staff Account" button
- Fills modal: Full Name, Email, Role (Cashier), Password, Confirm Password
- Account created instantly, welcome email sent with credentials
- New cashier appears in users list immediately

**New route:** POST /admin/staff/create
             → AdminController@createStaffAccount
             → name: admin.staff.create

**New mailable:** StaffWelcomeMail
- Subject: "Your EBA Staff Account Has Been Created"
- Contains: login email, temporary password, login URL (/login)
- Reminder to change password after first login

**New email view:** resources/views/emails/staff-welcome.blade.php

**Important constraints:**
- Cashier accounts have is_approved = true on creation
- No partnership_application is created for cashier accounts
- Public registration flow (CreateNewUser.php) is NOT used
- Only admin can create cashier accounts

---

### 12. FACULTY ROLE ⭐ NEW

**Purpose:** Campus faculty/staff who handle partnership application reviews and concessionaire record management. They do not approve or reject — they review and recommend, with admin making the final call.

**Account creation:** Same flow as cashier — admin creates faculty accounts from `/admin/users` via the Create Staff Account modal (Faculty is now a role option alongside Cashier).

**New middleware:** `app/Http/Middleware/FacultyMiddleware.php`
- Blocks non-faculty users from `/staff/*` with 403
- Registered in `bootstrap/app.php` as `faculty` alias

**New controller:** `app/Http/Controllers/Faculty/FacultyController.php`

**New views:**
- `resources/views/faculty/layout.blade.php` — faculty layout with dark sidebar shell
- `resources/views/faculty/dashboard.blade.php` — faculty dashboard with stats + ApexCharts (Applications Per Month + Application Status Distribution)
- `resources/views/faculty/partnerships/index.blade.php` — all applications with status badges
- `resources/views/faculty/partnerships/show.blade.php` — single application review page
- `resources/views/faculty/concessionaires/index.blade.php` — list approved concessionaires
- `resources/views/faculty/concessionaires/edit.blade.php` — update business info form
- `resources/views/faculty/history.blade.php` — faculty's own activity log entries

**New migration:** `2026_04_10_120000_add_faculty_review_fields_to_partnership_applications.php`

New columns on `partnership_applications`:
| Column | Type | Notes |
|---|---|---|
| `faculty_recommendation` | enum nullable | `recommend_approval` / `recommend_rejection` |
| `faculty_notes` | text nullable | Faculty's reasoning |
| `reviewed_by` | FK → users nullable | Which faculty member reviewed it |

**PartnershipApplication model:** updated `$fillable` + `reviewer()` belongsTo relation added.

**Admin panel update (`admin/partnerships.blade.php`):**
- Faculty Review panel added above Approve/Reject buttons
- Shows: "No faculty review yet" if `reviewed_by` is null
- If reviewed: reviewer name, recommendation badge (green = Recommend Approval, red = Recommend Rejection), faculty notes

**ActivityLog actions:**
- `faculty_recommendation_submitted` — faculty submits recommendation
- `faculty_document_uploaded` — faculty uploads a document
- `faculty_concessionaire_updated` — faculty updates a concessionaire record

**Blade role fallbacks updated:**
- `public-profile-dropdown.blade.php` — faculty case added
- `desktop-user-menu.blade.php` — faculty case added
- `sidebar.blade.php` — faculty case added
- `admin/users.blade.php` — faculty role badge added

**What faculty CAN do:**
- View all partnership applications
- Upload MOA, Contract, LOI on behalf of review
- Submit recommendation (Recommend Approval / Recommend Rejection) + notes
- Update concessionaire records (business name, description, location)
- View their own action history

**What faculty CANNOT do:**
- Approve or reject applications — admin only
- Manage user accounts or change roles
- Access `/admin/*` — 403
- View system or activity logs (only their own history)
- Record or view payments
- Manage uniform stocks

---

## DATABASE — KEY TABLES & FIELDS

### users
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| name | string | |
| email | string | unique |
| role | string | admin/cashier/concessionaire/student |
| business_name | string nullable | concessionaires only |
| profile_photo | string nullable | avatar path |
| cover_photo | string nullable | concessionaire cover |
| location | string nullable | concessionaire location |
| description | text nullable | concessionaire bio |
| is_approved | boolean | default false |
| is_active_concessionaire | boolean | default false |
| notification_preferences | json nullable | email/notification toggles |

### partnership_applications
| Column | Type | Notes |
|---|---|---|
| user_id | FK nullable | null = Path B walk-in |
| status | enum | pending/approved/registered/rejected/expired |
| rejection_reason | text nullable | admin rejection reason shown to applicant |
| moa_path | string nullable | |
| contract_path | string nullable | |
| letter_of_intent_path | string nullable | optional |
| contract_period_start | date nullable | |
| contract_period_end | date nullable | |
| warning_30_sent | boolean | duplicate email prevention |
| warning_7_sent | boolean | |
| warning_1_sent | boolean | |
| first_name | string nullable | applicant first name |
| last_name | string nullable | applicant last name |
| phone_number | string nullable | applicant phone |
| business_proposal | text nullable | applicant business proposal |
| faculty_recommendation | enum nullable | recommend_approval / recommend_rejection |
| faculty_notes | text nullable | faculty reviewer's notes |
| reviewed_by | FK → users nullable | faculty member who reviewed |
| contract_period_edit_count | integer | default 0 — tracks yearly edit count |
| contract_period_last_edited_year | integer nullable | year of last edit for yearly reset logic |

### products
| Column | Type | Notes |
|---|---|---|
| concessionaire_id | FK → users | |
| name | string | |
| description | text nullable | |
| price | decimal(10,2) | |
| category | string | food/beverage/snack |
| image | string nullable | storage path |
| is_available | boolean | default true |

### uniform_stocks
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| item_name | string | |
| image | string nullable | storage path |
| quantity | integer | default 0 |
| is_visible | boolean | default true |

### product_reviews
| Column | Type | Notes |
|---|---|---|
| user_id | FK | |
| product_id | FK | |
| rating | tinyint | 1-5 |
| comment | text nullable | |
| unique | user_id + product_id | one review per user |

### concessionaire_reviews
| Column | Type | Notes |
|---|---|---|
| user_id | FK | reviewer |
| concessionaire_id | FK → users | |
| rating | tinyint | 1-5 |
| comment | text nullable | |
| unique | user_id + concessionaire_id | one review per user |

### concessionaire_payments
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| partnership_application_id | FK nullable | links to applications |
| concessionaire_id | FK → users | the concessionaire |
| recorded_by | FK → users | the cashier |
| amount | decimal(10,2) | |
| payment_date | date | |
| payment_type | string | cash/check/bank_transfer |
| or_number | string nullable | OR number from AF No. 51-C |
| notes | text nullable | |
| timestamps | | |

---

## EMAIL NOTIFICATIONS

### SMTP Config
**Local (.env):**
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=aac936a361a6f9
MAIL_PASSWORD=5065f01ae3f360
MAIL_ENCRYPTION=tls
APP_ADMIN_EMAIL=marcdwightamorosa@gmail.com
QUEUE_CONNECTION=sync
```

**Production (Hostinger .env):**
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=marcdwightamorosa@gmail.com
MAIL_PASSWORD=[Gmail app password]
MAIL_ENCRYPTION=tls
APP_ADMIN_EMAIL=marcdwightamorosa@gmail.com
QUEUE_CONNECTION=sync
```

### All Mailables
| Class | Trigger | Recipient |
|---|---|---|
| WelcomeMail | Registration | New user |
| AdminNewPartnershipMail | New application | Admin |
| PartnershipReceivedMail | App submitted | Applicant |
| PartnershipApprovedMail | App approved | Applicant |
| PartnershipRejectedMail | On admin rejection (includes rejection reason + resubmit link) | Concessionaire |
| PartnershipRegisteredMail | Account upgraded | Applicant |
| PartnershipDocumentUploadedMail | Applicant uploads doc | Admin |
| PartnershipDocumentsReceivedMail | Applicant doc received notice | Applicant |
| AdminDocumentUploadedMail | Admin uploads doc | Applicant |
| ContractPeriodSavedMail | Contract dates saved | Applicant |
| ContractExpiryWarningMail | 30/7/1 day warning | Concessionaire |
| ContractExpiredMail | Contract expired | Concessionaire |
| PaymentRecordedMail | Cashier records payment | Concessionaire |
| PaymentDueReminderMail | 12th of every month, unpaid concessionaires only | Concessionaire |
| StaffWelcomeMail | Admin creates staff account | New cashier |
| Resubmission (no email) | Applicant resubmits — admin sees it reappear in pending queue | — |

**Important:** All mail sends are wrapped in try/catch.
Failures log as warnings and never crash the page.

---

## ARTISAN COMMANDS
```bash
php artisan qr:generate           # Generate static QR PNGs
php artisan contract:check-expiry # Check expired contracts (runs daily)
php artisan migrate --force       # Run migrations
php artisan config:clear          # Clear config cache
php artisan cache:clear           # Clear app cache
php artisan storage:link          # Create storage symlink
php artisan db:seed --class=UniformStockSeeder  # Seed default stock items
```

---

## HOSTINGER DEPLOYMENT NOTES ⭐ NEW

```
Live URL:     https://eba.cvsutrece.com
Server:       /home/u542188151/domains/eba.cvsutrece.com/
Laravel root: /home/u542188151/domains/eba.cvsutrece.com/eba_capstone/
Public root:  /home/u542188151/domains/eba.cvsutrece.com/public_html/
SSH:          ssh -p 65002 u542188151@145.79.25.104
Database:     u542188151_eba_capstone
```

### index.php paths (already updated):
```php
require __DIR__.'/../eba_capstone/vendor/autoload.php';
$app = require_once __DIR__.'/../eba_capstone/bootstrap/app.php';
```

### Storage symlink command (use this — exec() is disabled on Hostinger):
```bash
ln -s /home/u542188151/domains/eba.cvsutrece.com/eba_capstone/storage/app/public \
      /home/u542188151/domains/eba.cvsutrece.com/public_html/storage
```

### Cron job (set up in Hostinger hPanel → Cron Jobs):
```
* * * * * php /home/u542188151/domains/eba.cvsutrece.com/eba_capstone/artisan schedule:run >> /dev/null 2>&1
```

### Deployment checklist (every update):
1. Make changes locally and test
2. Run `npm run build` in cmd at project root
3. Zip changed files and upload via Hostinger File Manager
4. Upload `public/build/` folder to `public_html/build/`
5. SSH in and run:
   ```bash
   cd /home/u542188151/domains/eba.cvsutrece.com/eba_capstone
   php artisan config:clear
   php artisan cache:clear
   ```
6. If new migrations: `php artisan migrate --force`
7. If new seeders: `php artisan db:seed --class=ClassName`

---

## ROUTES SUMMARY

### Public (no auth)
- `GET /` — landing page
- `GET /products` — product listing (includes stocks section)
- `GET /products/{id}` — product detail
- `GET /concessionaires` — browse concessionaires
- `GET /concessionaires/{id}` — public concessionaire profile

### Auth — non-admin main site users
- `GET/POST /partnership/apply` — compatibility endpoint, redirects/flows to status
- `GET /partnership/status` — legacy path, redirects to `/application`
- `POST /partnership/documents` — upload documents
- `GET /application` — standalone My Application page
- `POST /application/loi` — submit LOI step in application wizard
- `POST /application/form` — submit application form step in wizard
- `POST /application/receipt` — submit receipt upload step in wizard
- `PATCH /application/info` — update applicant info
- `POST /application/resubmit` — resubmit rejected application
- `GET /settings/application` — legacy alias that redirects to `/application`

### Auth — all non-admin users
- `GET /settings/profile` — profile settings
- `GET /settings/password` — password settings

### Auth — concessionaire
- `GET /concessionaire` — dashboard
- `GET /concessionaire/products` — manage products
- `POST /concessionaire/products` — add product
- `GET /concessionaire/products/{id}/edit` — edit product
- `DELETE /concessionaire/products/{id}` — delete product
- `GET /concessionaire/moa` — MOAs & Contracts page
- `GET /concessionaire/payments` — own payment history
- `GET /concessionaire/payments/{payment}/receipt` — download own payment receipt PDF

### Auth — cashier
- `GET /cashier/payments` → record and view payments
- `POST /cashier/payments` → submit payment record
- `GET /cashier/history` → payment history page
- `GET /cashier/payments/history/view` → view all filtered payment history as PDF in browser
- `GET /cashier/payments/history/pdf` → download all filtered payment history PDF
- `GET /cashier/payments/{concessionaire}/history/view` → view single concessionaire payment history PDF in browser
- `GET /cashier/payments/{concessionaire}/history/pdf` → download single concessionaire payment history PDF
- `GET /cashier/payments/{payment}/receipt` → download payment receipt PDF

### Auth — staff (faculty role)
- `GET /staff/dashboard` → faculty dashboard landing page
- `GET /staff/partnerships` → list all partnership applications
- `GET /staff/partnerships/{id}` → review single application
- `POST /staff/partnerships/{id}/recommend` → submit recommendation + notes
- `POST /staff/partnerships/{id}/upload-document` → upload MOA, Contract, or LOI
- `GET /staff/concessionaires` → list approved concessionaires
- `PATCH /staff/concessionaires/{id}` → update concessionaire business info
- `GET /staff/history` → faculty's own activity log entries

### Admin (/admin/*)
- Login: `/admin/login`
- Dashboard, Users, Partnerships, Stocks, Logs, Activity Logs
- `GET /admin/stocks` — manage uniform stocks
- `POST /admin/stocks` — add stock item
- `PATCH /admin/stocks/{id}` — update quantity
- `POST /admin/partnerships/{id}/upload-document`
- `POST /admin/partnerships/{id}/reject`
- `PATCH /admin/partnerships/{id}/contract-period`
- `GET /admin/partnerships/{application}/document/{type}` — view partnership document
- `POST /admin/partnerships/{application}/wizard/approve-loi` — wizard LOI approve
- `POST /admin/partnerships/{application}/wizard/reject-loi` — wizard LOI reject
- `POST /admin/partnerships/{application}/wizard/approve-form` — wizard form approve
- `POST /admin/partnerships/{application}/wizard/reject-form` — wizard form reject
- `PATCH /admin/partnerships/{application}/wizard/tick-doc` — mark wizard document checklist
- `POST /admin/partnerships/{application}/wizard/final-approve` — wizard final approval
- `PATCH /admin/users/{id}/business-name`
- `GET /admin/payments` — all payment records
- `GET /admin/payments/history/view` — view all-concessionaire payment history PDF in browser
- `GET /admin/payments/history/pdf` — download all-concessionaire payment history PDF
- `GET /admin/payments/{payment}/receipt` — download payment receipt PDF
- `POST /admin/staff/create` — create cashier or faculty account
- `POST /admin/partnerships/{id}/upload-all-documents` — bulk doc upload
- `PATCH /admin/users/{id}/monthly-fee` — set concessionaire monthly fee
- `GET /admin/reviews` — reviews overview with per-concessionaire ratings and recent feed
- `DELETE /admin/reviews/product/{id}` — delete a product review
- `DELETE /admin/reviews/store/{id}` — delete a store review

### Dev Only — DELETE BEFORE DEPLOY
- `GET/POST /dev/test-accounts`

---

## KNOWN ISSUES / NOTES
1. Pre-existing static analyzer warning in web.php about `auth()->logout()` in concessionaire logout closure — unrelated, ignore
2. Mailtrap free tier: 1 email/second limit — test actions one at a time
3. Some legacy concessionaire accounts may have empty `business_name` — admin can set via `/admin/users` panel
4. `php artisan contract:check-expiry` needs a cron job on Hostinger — see deployment notes above
5. `/dev/test-accounts` MUST be deleted before client deployment
6. Gmail app password should be regenerated (was exposed in chat session)
7. `exec()` is disabled on Hostinger shared hosting — always use manual `ln -s` for storage symlink instead of `php artisan storage:link`
8. `public/build/` folder must be manually uploaded to `public_html/build/` after every `npm run build`
9. Users with stale roles ('user', 'student' from old test data) were discovered displaying incorrectly as Admin in the UI. Fixed via:
   * Migration: NormalizeInvalidUserRoles — reassigns unknown roles to concessionaire, preserves student
   * Migration: CorrectStudentRoleReassignment — restores mis-assigned students (concessionaire with no partnership_application → student)
   * EnforceValidRole middleware blocks unknown roles with 403
   * Admin dropdown now shows ⚠ Invalid Role for unrecognized values
   * All Blade role match() and @if branches now have explicit student and invalid fallback cases
  * AdminController::updateRole() validates via Rule::in() with all 5 valid roles
10. Favicon updated — new EBA favicon applied to all pages via `public/favicon.ico` replacement; `<link rel="icon">` in main layout confirmed pointing to the correct path.
11. Admin panel sidebar logo was invisible (white logo on white background) — fixed by applying correct dark background to the sidebar container and verifying logo asset path in `resources/views/admin/layouts/sidebar.blade.php`.
12. Contract period edit-limit enforcement is no longer active in the admin save flow, but legacy columns (`contract_period_edit_count`, `contract_period_last_edited_year`) and reset references still exist in `AdminController` for backward compatibility cleanup.
13. Inter font files are in `public/fonts/web/` — used on concessionaire products page. If deploying to Hostinger, upload the `public/fonts/` folder manually alongside `public/build/`.
14. If setting up on a new laptop/local environment, run `php artisan storage:link` so uploaded files and documents resolve correctly.
15. Payment due status currently follows the 1st-cycle rule (`due_soon` on days 25-31, `overdue` on days 1-24 when unpaid). If business policy changes again, move this to a config-driven due-date setting.
16. Blade editor may show false-positive JS diagnostics for `@json()` in script blocks (e.g., "Decorators are not valid here"). Runtime compilation is valid; verify via `php artisan view:cache`.
17. Admin stocks visibility toggle currently still shows a `Visible/Hidden` text label next to the switch; remove if a label-free toggle is desired for tighter table layout.

---

## PENDING WORK
| Task                                          | Priority        | Status     |
|-----------------------------------------------|-----------------|------------|
| Fix /cashier/payments ambiguous user_id error | Immediate       | ✅ Done    |
| Cashier redirect to /cashier/payments on login| High            | ✅ Done    |
| Add Payments link to cashier profile dropdown | High            | ✅ Done    |
| Faculty role — all 8 steps                    | High            | ✅ Done    |
| Admin partnerships modal + reject flow refresh| High            | ✅ Done    |
| Contract period direct-save flow simplification| High           | ✅ Done    |
| Admin payments instant-search refactor        | Medium          | ✅ Done    |
| Admin reviews full client-side filter refactor| Medium          | ✅ Done    |
| Admin stocks redesign + modal edit flow       | High            | ✅ Done    |
| Landing page animation + dotted fade pass     | Medium          | ✅ Done    |
| Mobile dropdown visual refinement             | Low             | still pending |
| Mark-as-read for concessionaire notifications | Low             | still pending |
| Payment receipt PDF generation (Phase 2)      | Future          | ✅ Done    |
| Overdue payment flags in admin dashboard      | Future          | ✅ Done    |
| Automated payment due date reminders (Phase 2)| Future          | ✅ Done    |
| Monthly fee config per concessionaire         | Future          | ✅ Done    |
| Payment recording limit (max 1/month)         | Future          | ✅ Done    |
| Permanent cashier payment history table       | Future          | ✅ Done    |
| Update staff (faculty) dashboard              | High            | ✅ Done    |
| Add 4 real campus concessionaires             | High            | pending    |
| Remove legacy contract edit-counter references| Medium          | pending    |
| Optional: remove stocks toggle text label     | Low             | pending    |
| Set up cron on Hostinger (contract expiry)    | At deploy time  |            |
| Delete /dev/test-accounts before deploy       | Before deploy   |            |
| Regenerate Gmail app password                 | Before deploy   |            |
| Audit stale reservation/refund references     | Medium          | still pending |

---

## RECALL LIST

| # | Name | Status | What it does |
|---|---|---|---|
| 1 | 🛡️ Access Control | ✅ Done | Current role boundaries: admin, cashier, concessionaire, student; admin isolation from main site |
| 2 | 🔑 Registration Model Rework | ✅ Done | New signups default to pending concessionaire with `is_approved=false` |
| 3 | 🤝 Partnership Status-First Flow | ✅ Done | Auto-created pending applications and status-centric onboarding |
| 4 | 📄 My Application Standalone Page | ✅ Done | `/settings/application` introduced as standalone document upload page |
| 5 | 🔔 Pending Banner UX | ✅ Done | Dismissible guidance banner linking to Settings -> My Application |
| 6 | 🏪 Public Concessionaire Visibility Gate | ✅ Done | Only approved concessionaires are shown publicly |
| 7 | 📑 MOAs & Contracts Consolidation | ✅ Done | Combined MOA + Contract visibility in `/concessionaire/moa` |
| 8 | 🛡️ Public Profile Hardening | ✅ Done | Removed public MOA tab and sanitized About fallback content |
| 9 | 📧 Mail Hardening | ✅ Done | try/catch across mail sends; failures are logged without 500s |
| 10 | 🏛️ Uniform Stocks Module | ✅ Done | Admin-managed uniform stocks integrated in public products page |
| 11 | 🚀 Hostinger Deployment | ✅ Done | Live deployment process and environment notes documented |
| 12 | 🔐 Role Security Hardening | ✅ Done | 5-role system enforced, stale roles normalized, Blade fallbacks fixed |
| 13 | 📋 Application Page Redesign | ✅ Done | Two-section application page with applicant info form + document checklist |
| 14 | 📤 Admin Upload All Documents | ✅ Done | Bulk document upload via single AJAX submission in admin modal |
| 15 | 💰 Payment Module Phase 1 | ✅ Done | Cashier records payments, concessionaire views history, admin views all |
| 16 | 👤 Staff Account Creation | ✅ Done | Admin creates cashier accounts directly from admin panel |
| 17 | 🌐 Unified Public Navigation | ✅ Done | Single shared nav partial across all public pages |
| 18 | 🔀 Cashier Post-Login Redirect | ✅ Done | LoginResponse + FortifyServiceProvider binding routes cashier to /cashier/payments on login |
| 19 | 🧭 Cashier Navigation Dropdown | ✅ Done | Cashier profile dropdown shows Home · Payments · Settings · Log out |
| 20 | 🧾 Payment Receipt PDF | ✅ Done | Cashier and concessionaire can download PDF receipts per payment |
| 21 | 🚩 Overdue Flags + Monthly Fee | ✅ Done | Admin sets per-concessionaire monthly_fee; overdue flags appear in admin payments and users panels |
| 22 | 🔒 Payment Recording Limit | ✅ Done | Max 1 payment per concessionaire per calendar month enforced in CashierController |
| 23 | 🖼️ Favicon + Admin Sidebar Logo | ✅ Done | Favicon replaced site-wide; admin sidebar logo visibility fixed via dark background on sidebar container |
| 24 | 🎓 Faculty Role — Middleware + Redirect | ✅ Done | FacultyMiddleware registered; LoginResponse routes faculty to /staff/dashboard on login |
| 25 | 📝 Faculty Partnership Review | ✅ Done | Faculty can upload docs and submit Recommend Approval/Rejection + notes; admin sees recommendation panel before final call |
| 26 | 🏢 Faculty Concessionaire Management | ✅ Done | Faculty can update concessionaire business name, description, location via /staff/concessionaires |
| 27 | 📜 Faculty History + Blade Fallbacks | ✅ Done | Faculty history page shows own activity log entries; faculty case added to all Blade role match/if fallbacks |
| 28 | ⭐ Review Permissions Fix | ✅ Done | Pending concessionaires can now submit reviews; block only applies to approved concessionaires reviewing their own store/products |
| 29 | 📊 Admin Reviews Module | ✅ Done | /admin/reviews page with stat cards, per-concessionaire ratings table, paginated review feed, inline delete moderation, and full filter controls |
| 30 | 🪟 Admin Partnerships Modal Redesign | ✅ Done | Sectioned modal layout with identity header, grouped info cards, documents checklist, faculty review panel, and separated action footer |
| 31 | 📅 Contract Period Edit Flow | ✅ Done | Edit button for approved/registered only; max 2 edits/year enforced via DB columns; ISO date parsing fixed; page reloads on save |
| 32 | 🏠 Concessionaire Dashboard Redesign | ✅ Done | Full overhaul with stats row, ratings snapshot, recent reviews, recent products; shared nav partial applied |
| 33 | 🐛 Customer Reviews Tab Fix | ✅ Done | ConcessionaireReview query mismatch fixed; tab now shows correct reviews |
| 34 | 🛍️ Concessionaire Products Page Redesign | ✅ Done | Profile shell layout, card redesign, search/filter bar, Inter font, server-side filtering |
| 35 | 🧭 Concessionaire Settings Route Fix | ✅ Done | Fixed sidebar settings route mismatch (`settings.profile` → `profile.edit`) preventing page load |
| 36 | 🧹 Concessionaire Products Legacy Shell Cleanup | ✅ Done | Removed leftover cover/profile/tabs shell from products page after sidebar-layout migration |
| 37 | 🪟 Concessionaire Layout Topbar Removal | ✅ Done | Removed topbar strip and related CSS so content starts directly in main content area |
| 38 | ⭐ Concessionaire Reviews Table + Unified Feed | ✅ Done | Redesigned reviews page to admin-style table and merged store+product review data with filters |
| 39 | 📈 Admin Dashboard ApexCharts (4 Charts) | ✅ Done | Added 4 chart cards + controller datasets for status, payments, application trend, and top concessionaires |
| 40 | 💳 Cashier Payment History + Filters + PDF | ✅ Done | Added global and per-concessionaire payment history view/download with server-side filtering on cashier payments page |
| 41 | 🧩 Cashier Payments UI Restructure | ✅ Done | Split PAYMENTS and ACTION controls and separated Payment History into a dedicated section |
| 42 | 🔐 Payment Limit Revision (1 per Month) | ✅ Done | Updated cashier recording rule from max 2/month to max 1/month per concessionaire |
| 43 | 🪟 Success Modals (Cashier + Login) | ✅ Done | Converted cashier payment success and registration success alerts into modal-based confirmation UX |
| 44 | 🔁 Partnership Status Route Consolidation | ✅ Done | Redirected `/partnership/status` to `/settings/application` and normalized upload redirects to `settings.application` |
| 45 | 🎨 Application Page + File Picker Enhancements | ✅ Done | Upgraded settings-application page visual design and styled file chooser controls |
| 46 | 🧭 Concessionaire Sidebar Back Link | ✅ Done | Added Back to Main Site link in concessionaire sidebar for faster public-site navigation |
| 47 | 📅 Fixed 15th Due-Date Status System (Superseded) | ✅ Done | Historical milestone; active logic now uses 1st-cycle due windows (`due_soon` 25-31, `overdue` 1-24) |
| 48 | 🧑‍🏫 Faculty Dashboard + Route | ✅ Done | Added `/staff/dashboard` with summary stats, Applications Per Month chart, and Application Status Distribution chart |
| 49 | 🧭 Faculty Sidebar Shell Redesign | ✅ Done | Rebuilt `faculty/layout.blade.php` to concessionaire-style dark sidebar with active nav + logout/footer actions |
| 50 | 🪪 Staff Dropdown Consolidation | ✅ Done | Faculty profile menus now use a single Dashboard link instead of separate Partnerships and Concessionaires links |
| 51 | 🏠 Landing Hero Refresh | ✅ Done | Replaced welcome hero with updated EBA marketplace headline, right-side vector image, and Products/Concessionaires CTAs |
| 52 | 🎨 Public Background Tone Refresh | ✅ Done | Shifted public page-level backgrounds to warm neutral stone tones while keeping cards white for contrast |
| 53 | 🦶 Landing Footer Link + Logo Refresh | ✅ Done | Updated footer quick links/services routes, retained dynamic year, and fixed footer logo aspect ratio |
| 54 | 🧾 Concessionaire Payment Status Banner + Bell | ✅ Done | Banner and sidebar bell driven by due-soon/overdue flags and current 1st-cycle due windows |
| 55 | 🧩 Auth Split-Screen + Fortify Reset Views | ✅ Done | Login/register/forgot/reset split-screen layouts with new Fortify forgot/reset views |
| 56 | 🔁 Partnership Soft Rejection + Resubmission | ✅ Done | Rejection reason, resubmit flow, routes, and mail update |
| 57 | 👤 Registration Split Name Fields | ✅ Done | First/Last name fields with `users.name` derived from both |
| 58 | ⭐ Landing Features Section Redesign | ✅ Done | Features section updated with elevated center card and icon containers |
| 59 | 📅 Payment Due Reminder Scheduler | ✅ Done | monthlyOn(12, 08:00) in console.php triggers SendPaymentDueReminders on the 12th; sends PaymentDueReminderMail to all unpaid concessionaires with a monthly_fee set |
| 60 | 📂 Partnership Document Upload Modal | ✅ Done | Single "Upload Files" button opens animated modal with all document types, status badges, and smooth transitions to success modal using sessionStorage tracking |
| 61 | ✅ Registration Success Modal Redesign | ✅ Done | Compact 400px modal with circular checkmark icon, centered layout, and bounce entrance animations matching system-wide success pattern |
| 62 | 📋 Cashier Dashboard Button Icons | ✅ Done | Added 📋 clipboard emoji to View/View History buttons and 💾 floppy disk emoji to Download buttons across cashier payment interface |
| 63 | ⭐ Product Review Section Complete Redesign | ✅ Done | Replaced inline buttons with "Your Review Display" card + "Edit Review" button; edit modal with animations; delete confirmation modal; success modal with transitions; fixed star rating display bug; compact 500px modal width |
| 64 | 🪟 Admin Partnerships Modal Refresh (May 22) | ✅ Done | Modal widened to 640px, identity header redesigned, section cards refined, documents list uses pill Download buttons, and footer actions are sticky |
| 65 | 📅 Contract Period Direct Save Flow (May 22) | ✅ Done | Removed edit-toggle mode and now always shows start/end inputs with direct Save for pending/under_review/approved/registered statuses |
| 66 | ⛔ Separate Reject Modal Flow (May 22) | ✅ Done | Reject action now opens dedicated `#rejectModal` with required reason and applicant context while keeping Application Details modal open underneath |
| 67 | 💳 Admin Payments Instant Search UI (May 22) | ✅ Done | Replaced multi-control filter bar with instant client-side search input (`Business name or concessionaire`) |
| 68 | ⭐ Admin Reviews Client-Side Feed Filters (May 22) | ✅ Done | Recent Reviews now uses instant client-side filtering for comment, concessionaire, type, and exact 1-5 star rating with live count badge updates |
| 69 | 🏛️ Admin Stocks Redesign + Modal Edit (May 22) | ✅ Done | Added stat cards, low-stock indicator, refined table/controls, and modal-based stock editing with instant search |
| 70 | 🎬 Landing Animation + Dot-Blend Pass (May 22) | ✅ Done | Added motion/easing improvements, hero and concessionaire dotted-section fade blending, and removed section-level scroll-lift artifact causing moving line effect |
| 71 | 🔁 Application Route Canonicalization (Current State) | ✅ Done | Canonical onboarding path is now `/application` with `/settings/application` and `/partnership/status` redirecting to `/application` |

---

## HOW TO CONTINUE DEVELOPMENT
1. Upload this file + project files to your AI agent
2. Say: "Read the handoff document and review the project"
3. Run `php artisan migrate --fresh --seed` if setting up fresh
4. Run `php artisan storage:link` to set up file storage
5. Copy `.env.production.example` to `.env` and fill in credentials
6. Run `php artisan qr:generate` to regenerate QR codes
7. Use `/dev/test-accounts` to create test accounts for each role

---

## QUICK ROLE TEST ACCOUNTS (create via /dev/test-accounts)
| Role | Email format | Password |
|---|---|---|
| Admin | anything@gmail.com | any |
| Cashier | anything@gmail.com | any |
| Concessionaire | anything@gmail.com | any (then approve via admin) |
| Student | anything@gmail.com | any |
