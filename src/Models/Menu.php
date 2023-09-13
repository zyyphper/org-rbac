<?php

namespace Encore\OrgRbac\Models;


use Encore\Admin\Models\Menu AS BaseMenu;
use Encore\OrgRbac\Models\Enums\MenuType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Menu extends BaseMenu
{
    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'uri','type'];

    public function child()
    {
        return $this->hasMany(get_class($this), 'parent_id', $this->getKeyName())->where('type',MenuType::BTN);
    }

    public function platforms()
    {
        $pivotTable = config('org.database.platform_menu_table');
        $relatedModel = config('org.database.platforms_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'platform_id');
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        $pivotTable = config('org.database.role_menu_table');
        $relatedModel = config('org.database.roles_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'role_id');
    }

    /**
     * @return array
     */
    public function allNodes(): array
    {
        $connection = config('admin.database.connection') ?: config('database.default');
        $orderColumn = DB::connection($connection)->getQueryGrammar()->wrap($this->getOrderColumn());

        $byOrder = 'ROOT ASC,'.$orderColumn;

        $query = static::query();
        $query = $query->where('type',MenuType::MENU);

        if (config('admin.check_menu_roles') !== false) {
            $query->with('roles');
        }


        return $query->selectRaw('*, '.$orderColumn.' ROOT')->orderByRaw($byOrder)->get()->toArray();
    }
}
