<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckContractExpiry;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('qr:generate', function () {
    $targetDir = public_path('qrcodes');
    File::ensureDirectoryExists($targetDir);

    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_L,
        'scale' => 10,
        'imageBase64' => false,
        'imageTransparent' => false,
        'bgColor' => [255, 255, 255],
    ]);

    $items = [
        [
            'url' => 'https://eba.cvsutrece.com/',
            'filename' => 'qr_home.png',
        ],
        [
            'url' => 'https://eba.cvsutrece.com/partnership/apply',
            'filename' => 'qr_partnership_apply.png',
        ],
    ];

    foreach ($items as $item) {
        $pngData = (new QRCode($options))->render($item['url']);
        file_put_contents($targetDir . DIRECTORY_SEPARATOR . $item['filename'], $pngData);

        $this->line("  \u{2714} {$item['filename']} \u{2192} public/qrcodes/{$item['filename']}");
    }
})->purpose('Generate static page-redirect QR PNG files in public/qrcodes');

Schedule::command(CheckContractExpiry::class)->daily();
Schedule::command('payment:send-due-reminders')->monthlyOn(1, '08:00');
