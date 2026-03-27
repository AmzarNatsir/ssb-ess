<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attendence;

$records = Attendence::orderBy('id', 'desc')->take(5)->get();
foreach ($records as $r) {
    echo "ID: {$r->id}, Status: [{$r->status}], Jam: {$r->jam}\n";
}
