<?php

namespace Encore\OrgRbac\Models;


use Encore\Admin\Models\Menu;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\OrgRbac\Models\Enums\DepartmentType;
use Encore\OrgRbac\Traits\HasPermissions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Storage;

class User extends Model implements AuthenticatableContract
{
    use HasPermissions;
    use Authenticatable;
    use DefaultDatetimeFormat;
    protected $fillable = ['id','platform_id','username', 'password','name', 'is_admin'];

    protected $primaryKey = 'id';
    public $incrementing = false;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('org.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('org.database.users_table'));

        parent::__construct($attributes);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if (url()->isValidUrl($avatar)) {
            return $avatar;
        }

        $disk = config('admin.upload.disk');

        if ($avatar && array_key_exists($disk, config('filesystems.disks'))) {
            return Storage::disk(config('admin.upload.disk'))->url($avatar);
        }

        $default = config('admin.default_avatar') ?: '/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg';

        return admin_asset($default);
    }

    /**
     * If User can see menu item.
     *
     * @param Menu $menu
     *
     * @return bool
     */
    public function canSeeMenu($menu)
    {
        return true;
    }

    /**
     * If user can access route.
     *
     * @param Route $route
     *
     * @return bool
     */
    public function canAccessRoute(Route $route)
    {
        return true;
    }

    public function departments()
    {
        $pivotTable = config('org.database.duties_table');
        $relatedModel = config('org.database.departments_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'department_id')->withPivot('id');
    }

    public function mainDuty()
    {
        return $this->duties()->where('department_type',DepartmentType::MAIN)->first();
    }

    public function duties()
    {
        $dutyModel = config('org.database.duties_model');
        return $this->hasMany($dutyModel,'user_id');
    }

    public function rolesByDuty()
    {
        $dutyId = \Encore\OrgRbac\Duty\Duty::load()->getId();
        $dutyModel = config('org.database.duties_model');
        return $dutyModel::find($dutyId)->roles();
    }

    public function buttonPermissionsByDuty()
    {
        $idArray = $this->rolesByDuty()->pluck('id')->toArray();
        $menuModel = config('org.database.menu_model');
        return $menuModel::whereHas('roles',function($query) use($idArray) {
            $query->whereIn('role_id',$idArray);
        })->get();
    }

    public function info()
    {
        $userInfoModel = config('org.database.user_infos_model');
        return $this->hasOne($userInfoModel,'user_id');
    }

}
