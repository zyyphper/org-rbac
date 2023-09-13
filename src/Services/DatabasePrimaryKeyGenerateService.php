<?php


namespace Encore\OrgRbac\Services;



class DatabasePrimaryKeyGenerateService
{
   public function load($driver)
   {
       return $driver::load();
   }
}
