<?php

// Cek rencana (tanpa menulis file):
// php artisan tenrusl:scaffold-views --dry-run

// Generate file (tidak menimpa yang sudah ada):
// php artisan tenrusl:scaffold-views

// Paksa timpa file yang sudah ada:
// php artisan tenrusl:scaffold-views --force

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ScaffoldViewsCommand extends Command
{
    protected $signature = 'tenrusl:scaffold-views
                            {--force : Timpa file yang sudah ada}
                            {--dry-run : Hanya tampilkan rencana pembuatan, tanpa menulis file}';

    protected $description = 'Generate empty Blade views for TenRusl UI (no vendor, no welcome, no assets)';

    public function handle(Filesystem $fs): int
    {
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $base = resource_path('views');

        // Direktori yang akan dibuat (urut agar tidak bentrok)
        $dirs = [
            $base,
            $base.'/layout',
            $base.'/main',
            $base.'/main/partials',
            $base.'/main/payments',
            $base.'/main/payments/_partials',
            // NOTE: sengaja TIDAK membuat resources/views/vendor
        ];

        // Pastikan direktori ada
        foreach ($dirs as $dir) {
            if ($dry) {
                $this->line("DIR  - $dir");

                continue;
            }
            $fs->ensureDirectoryExists($dir, 0755, true);
        }

        // Daftar provider dari config (fallback ke tiga awal agar aman)
        $providers = (array) config('tenrusl.providers_allowlist', ['mock', 'xendit', 'midtrans']);
        $providers = array_values(array_unique(array_map(static fn ($p) => (string) $p, $providers)));

        // File static (kosongan / stub ringan)
        $files = [
            // Layout
            $base.'/layout/meta.blade.php' => "@php /* meta SEO/OG/Twitter */ @endphp\n",
            $base.'/layout/nav.blade.php' => "@php /* nav */ @endphp\n",
            $base.'/layout/header.blade.php' => "{{-- layout header --}}\n@yield('head')\n",
            $base.'/layout/footer.blade.php' => "{{-- layout footer --}}\n@stack('body')\n",

            // Wrapper & halaman utama
            $base.'/main.blade.php' => "{{-- main wrapper --}}\n@extends('layout.header')\n@section('content') @include('main.index') @endsection\n@include('layout.footer')\n",
            $base.'/main/index.blade.php' => "{{-- halaman utama (merangkai partials) --}}\n@include('main.partials._hero')\n@include('main.partials._features')\n@include('main.partials._endpoints')\n@include('main.partials._providers')\n@include('main.partials._signature')\n@include('main.partials._tooling')\n",

            // Partials utama
            $base.'/main/partials/_hero.blade.php' => "{{-- hero --}}\n",
            $base.'/main/partials/_features.blade.php' => "{{-- features --}}\n",
            $base.'/main/partials/_endpoints.blade.php' => "{{-- endpoints table --}}\n",
            $base.'/main/partials/_providers.blade.php' => "{{-- providers grid --}}\n",
            $base.'/main/partials/_signature.blade.php' => "{{-- signature matrix --}}\n",
            $base.'/main/partials/_tooling.blade.php' => "{{-- swagger & postman --}}\n",

            // Payments partials umum
            $base.'/main/payments/_partials/header.blade.php' => "{{-- payments header --}}\n",
            $base.'/main/payments/_partials/signature-note.blade.php' => "{{-- signature note --}}\n",
            $base.'/main/payments/_partials/example-json.blade.php' => "{{-- example json --}}\n",
            $base.'/main/payments/_card.blade.php' => "{{-- reusable card for provider --}}\n",
        ];

        // File per provider (mengikuti slug di allowlist)
        foreach ($providers as $p) {
            $files[$base."/main/payments/{$p}.blade.php"] = "{{-- {$p} view --}}\n@include('main.payments._partials.header')\n";
        }

        // Tulis file
        $created = 0;
        $skipped = 0;
        foreach ($files as $path => $content) {
            if ($dry) {
                $this->line("FILE - $path");

                continue;
            }

            if ($fs->exists($path) && ! $force) {
                $this->warn("skip: $path (exists)");
                $skipped++;

                continue;
            }

            $fs->put($path, $content);
            $this->info("make: $path");
            $created++;
        }

        if (! $dry) {
            $this->line("Done. created={$created}, skipped={$skipped}");
        }

        return Command::SUCCESS;
    }
}
