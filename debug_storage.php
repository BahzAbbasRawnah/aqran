<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "Storage URL for 'tutoring_videos/UHdFYdV7HTIiRwvZ2qoWjSuFF0wUKebwgzg991iE.mp4':\n";
echo Storage::url('tutoring_videos/UHdFYdV7HTIiRwvZ2qoWjSuFF0wUKebwgzg991iE.mp4') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
