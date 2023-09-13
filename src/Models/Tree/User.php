<?php

namespace Encore\OrgRbac\Models\Tree;


use Encore\OrgRbac\Facades\OrgRbac;
use Encore\OrgRbac\Models\User AS BaseModel;
use Encore\OrgRbac\Traits\ModelRelationTree;
use Illuminate\Support\Facades\DB;

class User extends BaseModel
{
    use ModelRelationTree;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTitleColumn('name');
        $this->setOrderColumn('sort');
    }

    public function allNodes()
    {
        DB::connection()->enableQueryLog();
        $dutyModel = config('org.database.duties_model');
        $result = $dutyModel::with(['user'=>function($query) {
            $query->select(['id','name']);
        }])->select([
                'id',
                'user_id',
                'department_id',
                DB::raw("0 AS parent_id"),
//                'user.name',
                'sort'
            ])
            ->orderBy($this->orderColumn)
            ->get()
            ->toArray();
        return $this->assembleArray($result,[
            'id' => 'id',
            'name' => 'user.name',
            'parent_id' => 'parent_id',
            'department_id' => 'department_id',
            'sort' => 'sort'
        ]);
    }

    protected function assembleArray($data,$select)
    {
        $result = [];
        foreach ($data as $value) {
            $array = [];
            foreach ($select as $field => $valueName) {
                $valueNameArray = explode('.',$valueName);
                if (count($valueNameArray) === 1) {
                    $array[$field] = $value[$valueName];
                    continue;
                }
                $fieldData = $value;
                foreach ($valueNameArray as $name) {
                    if (is_null($fieldData)) {
                        break;
                    }
                    $fieldData = $fieldData[$name];
                }
                $array[$field] = $fieldData;
            }
            array_push($result,$array);
        }
        return $result;
    }
}
