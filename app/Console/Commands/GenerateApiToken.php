<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

class GenerateApiToken extends Command
{
    protected $signature = 'api:token:generate
                            {name : Label / nama token}
                            {--expires= : Tanggal kadaluarsa, format Y-m-d (opsional)}';

    protected $description = 'Generate API token baru untuk akses dokumen eksternal';

    public function handle(): int
    {
        $name      = $this->argument('name');
        $expiresAt = null;

        if ($this->option('expires')) {
            $expiresAt = \Carbon\Carbon::parse($this->option('expires'));
            if ($expiresAt->isPast()) {
                $this->error("Tanggal kadaluarsa '$expiresAt' sudah lewat.");
                return self::FAILURE;
            }
        }

        $result = ApiToken::generate($name, $expiresAt);

        $this->info('Token berhasil dibuat.');
        $this->line('');
        $this->table(
            ['Field', 'Value'],
            [
                ['Name',       $result['model']->name],
                ['Token',      $result['plain']],
                ['Expires At', $expiresAt ? $expiresAt->toDateTimeString() : 'Tidak ada (permanen)'],
                ['Created At', $result['model']->created_at],
            ]
        );
        $this->line('');
        $this->warn('Simpan token di atas! Token tidak bisa dilihat lagi setelah ini.');

        return self::SUCCESS;
    }
}
