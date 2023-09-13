<?php

namespace Encore\OrgRbac\Console;

use Encore\OrgRbac\Facades\OrgRbac;
use Illuminate\Console\Command;

class MenuCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'orgRbac:menu';

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
        $menu = OrgRbac::menu();

        echo json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\r\n";
    }
}
