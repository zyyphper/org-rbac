<?php

namespace Encore\OrgRbac;

use Encore\OrgRbac\Services\DatabasePrimaryKeyGenerateService;
use Illuminate\Support\ServiceProvider;

class OrgRbacServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\MenuCommand::class,
        Console\InitCommand::class
    ];
    /**
     * {@inheritdoc}
     */
    public function boot(OrgRbac $extension)
    {
        if (! OrgRbac::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'org_rbac');
        }


        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/org_rbac')],
                'org_rbac'
            );
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'laravel-admin-config');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'laravel-admin-migrations');
        }

        $this->app->booted(function () {
            OrgRbac::routes(__DIR__.'/../routes/web.php');
        });

        $this->app->singleton('primaryKeyGenerate',function () {
            return new DatabasePrimaryKeyGenerateService();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
