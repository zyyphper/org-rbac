<?php

namespace Encore\OrgRbac\Console;

use Encore\OrgRbac\Facades\OrgRbac;
use Illuminate\Console\Command;

class InitCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'orgRbac:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init organization rbac system';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('migrate');

        $platformModel = config('org.database.platforms_model');

        if ($platformModel::count() == 0) {
            $this->call('db:seed', ['--class' => \Encore\OrgRbac\Models\OrgRbacTablesSeeder::class]);
        }
    }
}
