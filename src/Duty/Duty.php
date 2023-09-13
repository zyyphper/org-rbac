<?php

namespace Encore\OrgRbac\Duty;

use Encore\Admin\Facades\Admin;
use Encore\OrgRbac\Duty\Driver\RedisDriver;
use Encore\OrgRbac\Duty\Driver\SessionDriver;

class Duty
{
    private static $instance = null;
    protected $driver = null;
    protected $prefix = null;

    private function __construct()
    {
        $config = $this->getConfig();
        $driver = $config['driver'] ?? 'session';
        $this->driver = $this->getDriver($driver);
        $this->prefix = $config['prefix'] ?? 'org';
    }

    public static function load()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    protected function getConfig()
    {
        return config('org.duty');
    }

    public function init()
    {
        $mainDutyId = Admin::user()->mainDuty()->id;
        $this->setId($mainDutyId);
        return $mainDutyId;
    }

    public function getId()
    {
        $id = $this->driver->get($this->prefix.":duty:id");
        if ($id) return $id;
        return $this->init();
    }

    public function setId($id)
    {
        $this->driver->set($this->prefix.":duty:id",$id);
    }

    public function getDriver($driver)
    {
        switch ($driver) {
            case 'session': return new SessionDriver();
            case 'redis': return new RedisDriver();
        }
    }
}
