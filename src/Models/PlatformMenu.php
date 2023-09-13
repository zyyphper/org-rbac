<?php

namespace Encore\OrgRbac\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PlatformMenu extends Model
{
    protected $fillable = ['menu_id', 'platform_id'];
    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.platform_menu_table'));

        parent::__construct($attributes);
    }
    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        $pivotTable = config('org.database.role_menu_table');
        $relatedModel = config('org.database.roles_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'role_id','menu_id');
    }
    /**
     * @return BelongsTo
     */
    public function platform() : BelongsTo
    {
        $platformModel = config('org.database.platforms_model');
        return $this->belongsTo($platformModel,'platform_id','id');
    }

    public function platformConfigs() : BelongsToMany
    {
        $pivotTable = config('org.database.platform_menu_table');
        $relatedModel = config('org.database.platforms_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'platform_menu_id', 'platform_id');
    }

}
