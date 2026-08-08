<?php

namespace App\Shared\Jobs;

use App\Central\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Sesuaikan namespace model Tenant lu kalo beda

class CreateFrameworkDirectoriesForTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Tenant $tenant) {}

    public function handle(): void
    {
        $this->tenant->run(function ($tenant) {
            $storage_path = storage_path();

            $suffixBase = config('tenancy.filesystem.suffix_base');

            if (!is_dir(public_path($suffixBase))) {
                mkdir(public_path($suffixBase), 0775, true);
            }

            $dirs = [
                $storage_path,
                "$storage_path/app/public",
                "$storage_path/framework/cache",
                "$storage_path/framework/views",
                "$storage_path/framework/sessions",
            ];

            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
            }

            $symlinkTarget = public_path("$suffixBase$tenant->id");
            if (!file_exists($symlinkTarget) && !is_link($symlinkTarget)) {
                symlink("$storage_path/app/public", $symlinkTarget);
            }
        });
    }
}
