<?php

namespace App\Console\Commands;

use App\Http\Controllers\SnappyShop\NotificationController;
use Illuminate\Console\Command;

class NotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to SnappyShop users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $Rasd = app(NotificationController::class);
       $Rasd->sendNotifications();
    }
}
