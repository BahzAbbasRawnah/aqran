<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-expired-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired projects and announcements to maintain performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // 1. Cleanup Projects
        $deletedProjectsCount = Project::where('semester_end_date', '<', $now)->delete();

        // 2. Cleanup Announcements
        $deletedAnnouncementsCount = Announcement::where('expires_at', '<', $now)->delete();

        // 3. Logging results
        $message = "Cleanup Task Completed: Deleted {$deletedProjectsCount} projects and {$deletedAnnouncementsCount} announcements.";
        
        Log::info($message);
        $this->info($message);

        return Command::SUCCESS;
    }
}
