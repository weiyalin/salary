<?php

namespace App\Console\Commands;

use App\Models\TaskStat;
use Illuminate\Console\Command;

class Stat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command stat description';

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
        //
        TaskStat::paper();
        TaskStat::stat();
    }
}
