<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database and upload to google drive.';

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
        $name = Str::random(10) . '_';
        $date = Carbon::today()->format('d-m-Y');
        Artisan::call("db:backup --database=mysql --destination=local --destinationPath=". $name ." --timestamp=d-m-Y --compression=null");

        $name = $name . $date;
        Storage::move($name, $name . '.sql');
        $name = $name . '.sql';
        $file = Storage::get($name);
        Storage::cloud()->put($name, $file);
        Storage::delete($name);

        $link = env('GOOGLE_DRIVE_LINK');
        $this->info('File Name: ' . $name);
        $this->info('File Link: ' . $link);
    }
}
