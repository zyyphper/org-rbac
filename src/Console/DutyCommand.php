<?php

namespace Encore\OrgRbac\Console;

use Encore\OrgRbac\Facades\OrgRbac;
use Illuminate\Console\Command;

class DutyCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'orgRbac:duty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the org admin menu';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $duty = OrgRbac::duty();

        echo json_encode($duty, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\r\n";
    }
}
