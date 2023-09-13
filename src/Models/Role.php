<?php

namespace Encore\OrgRbac\Models;


use Encore\Admin\Facades\Admin;
use Encore\OrgRbac\Models\Enums\IsAdmin;
use Encore\OrgRbac\Models\Enums\MenuType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    protected $fillable = ['platform_id','name','slug'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('org.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('org.database.roles_table'));

        parent::__construct($attributes);
    }

    public function duties()
    {
        $pivotTable = config('org.database.role_duty_table');
        $relatedModel = config('org.database.duties_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'duty_id');
    }

    /**
     * @return BelongsTo
     */
    public function platform() : BelongsTo
    {
        $platformModel = config('org.database.platforms_model');
        return $this->belongsTo($platformModel,'platform_id','id');
    }

    public function buttonPermissions()
    {
        $pivotTable = config('org.database.role_menu_table');
        $relatedModel = config('org.database.menu_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'menu_id')->where('type',MenuType::BTN);
    }

    /**
     * 管理员无需分配 获取平台下的非管理员的角色
     * @param $platformId
     * @return \Illuminate\Support\Collection
     */
    public static function getPlatformRole($platformId = '')
    {
        if (empty($platformId)) $platformId = Admin::user()->platform_id;
        return self::where('platform_id',$platformId)->pluck('name', 'id');
    }

    public function getAdministratorRole()
    {
        return self::where('is_admin',IsAdmin::YES)->pluck('name','id');
    }
}
