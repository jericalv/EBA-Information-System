# EBA Information System

A Laravel-based information system for the External and Business Affairs (EBA) office at Cavite State University – Trece Martires City Campus. It manages campus concessionaire partnerships, product listings, uniform/book stock and point-of-sale, and payment tracking — all in one platform.

**Live:** [eba.cvsutrece.com](https://eba.cvsutrece.com)

## About

EBA replaces manual, paper-based processes for onboarding and managing campus concessionaires. It handles the full partnership lifecycle (application → document review → approval), gives concessionaires a dashboard for products and reviews, tracks monthly fee payments with automated reminders, and runs a cashier-style checkout for campus uniform and book sales — printing receipts directly onto pre-printed carbon booklets.

## Features

**Partnerships & onboarding**
- Multi-step application wizard (Letter of Intent → application form → receipt) with per-step admin/faculty review and approval
- Multi-document uploads (valid ID, business permit, MOA, contract) with resubmission on rejection
- Faculty can review and recommend; admin makes the final approve/reject call

**Products & reviews**
- Concessionaires manage their own product listings with image galleries
- Public browsing and search across products and concessionaires
- Two independent review systems — product reviews and store/concessionaire reviews

**Uniform & book stock + point of sale**
- Per-size stock and pricing (JSON-backed), low-stock thresholds, and a full stock movement ledger (restock, correction, sale)
- Counter-style checkout (admin/faculty) that deducts stock per sale and generates an itemized ledger
- Booklet receipt printing — a PDF sized and calibrated to overlay values onto a pre-printed 90×130mm receipt booklet, with a `?preview=1` calibration mode

**Payments**
- Centralized monthly fee status logic (paid / due / due soon / overdue) per concessionaire contract
- Multi-month payment recording, automated due-date reminder emails, and contract expiry checks
- Signed, no-login-required invoice download links sent via email

**Other**
- Concessionaire banner/media galleries
- System-wide activity log for auditing key actions
- Static QR codes (for print materials, linking to public pages)
- Dark mode across all four portals, each with its own theme and persisted preference

## User Roles

| Role | Access |
|---|---|
| **Admin** | Full system management via `/admin` (partnerships, users, stocks, payments, logs, site settings) |
| **Faculty/Staff** | Partnership review, uniform checkout, stock management, own action history, via `/staff` |
| **Cashier** | Payment recording and history, via `/cashier` |
| **Concessionaire** | Product/media/profile management and payment history, via `/concessionaire` |
| **Student** | Public browsing and review writing (auto-assigned to `@cvsu.edu.ph` registrants) |

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade, Livewire, Tailwind CSS 4, Alpine.js, Flowbite
- **Database:** MySQL
- **Auth:** Laravel Fortify
- **PDF generation:** DomPDF (`barryvdh/laravel-dompdf`)
- **QR codes:** `chillerlan/php-qrcode`
- **Charts:** ApexCharts
- **Testing:** Pest
- **Build tooling:** Vite

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL

### Installation

```bash
git clone https://github.com/jericalv/EBA-Information-System.git
cd EBA-Information-System

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your database and mail settings in `.env` (a `.env.production.example` is included as a reference for production values), then:

```bash
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
```

For local development with hot-reloading and the queue listener running together:

```bash
composer run dev
```

### Generating static QR codes

```bash
php artisan qr:generate
```

## Deployment

This project runs on Hostinger shared hosting, which has a few quirks that shape the deployment process:

- The public web root (`public_html`) is separate from the Laravel project root — `index.php` is adjusted accordingly
- `exec()` is disabled, so the storage symlink must be created manually via `ln -s` rather than `php artisan storage:link`
- Compiled frontend assets (`public/build/`) must be built locally with `npm run build` and uploaded manually — they can't be built over SSH
- The task scheduler (payment reminders, contract expiry checks) runs via a Hostinger cron job calling `artisan schedule:run` every minute

## Project Status

This is an active capstone project. A few things worth knowing if you're picking up the code:

- The `/dev/test-accounts` route is a local-only tool for creating test accounts of any role — it's blocked in production but must be removed entirely before a final client handoff
- Uniform booklet receipt printer offsets are not yet calibrated against a physical printer (use `?preview=1` on a receipt to check alignment)
- A concessionaire-facing booklet receipt (mirroring the uniform one) is planned but not yet built

## License

*Add your license here.*

## Acknowledgments

Developed by John Eric Alvarado as a capstone project for Cavite State University – Trece Martires City Campus.
