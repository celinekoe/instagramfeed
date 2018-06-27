<?php

namespace App\Console\Commands;

use App\Traits\Database;
use Illuminate\Console\Command;

class updateGalleryItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gallery:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update gallery items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Database::updateDatabase();
    }
}
