<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class trialReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial:expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset of Membership Credits for Trial Users';

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
     * @return int
     */
    public function handle()
    {
        $users = User::where('mem_type', 0)->get();
        foreach ($users as $log) {
            if (Carbon::now() > $log->mem_exp) {
                $log->update(['mem_credit' => 0]);
		info('Trial Expiry');
            }
        }
    }
}
