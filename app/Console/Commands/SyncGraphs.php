<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Kayer\Monitoring\MonitoringInterface;

class SyncGraphs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:graph {user : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync user graphs';

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
    public function handle(MonitoringInterface $monitoring)
    {
        DB::beginTransaction();
		try {
			$monitoring->graph()
				 ->sync($this->argument('user'))
				 ;
			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			return 1;
		}
		return 0;
    }
}
