<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteOldPelangganFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-pelanggan-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus file di storage/app/public/exports/tagihan yang lebih dari 3 hari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $folderPath = storage_path('app/public/exports/pelanggan');
        $files = File::files($folderPath);

        $now = Carbon::now();

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));

            // Jika file lebih dari 7 hari, hapus
            if ($lastModified->diffInDays($now) > 3) {
                File::delete($file);
                $this->info("Deleted: " . $file->getFilename());
            }
        }

        $this->info("Pembersihan file tagihan lama selesai.");
    }
}
