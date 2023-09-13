<?php

namespace Encore\OrgRbac\Traits;

use Encore\Admin\Traits\ModelTree AS BaseTree;
use Illuminate\Database\Eloquent\Model;

trait ModelRelationTree
{
    use BaseTree;

    protected $titleColumn;

    /**
     * 关联节点模型池
     * @var array
     */
    protected $relationNodeModel = [];
    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree()
    {
        $this->setRelationModelTree(self::class,'');
        return $this->buildMoreNestedArray($this->getRelationModel());
    }

    /**
     * @param $relationModel
     * @param $relationField
     * @throws \Exception
     */
    public function setRelationModelTree($relationModel,$relationField) :void
    {
        $relation = new $relationModel();

        if (!$relation instanceof Model) {
            throw new \Exception("unable to associate illegal model");
        }
        if (!method_exists($relation,'allNodes')) {
            throw new \Exception("please use model tree");
        }
        $node[] = [$relation,$relationField];
        if (!property_exists($relation,'relationNodeModel') || empty($relationNodeModel = $relation->getRelationNodeModel())) {
            $this->setRelationNodeModel($node);
            return;
        }
//        array_unshift($relationNodeModel,$node);

        foreach ($relationNodeModel as $nodeModel) {
            array_push($node,$nodeModel);
        }
        $this->setRelationNodeModel($node);
    }

    public function setRelationNodeModel($modelList)
    {
        $this->relationNodeModel = $modelList;
    }

    public function getRelationNodeModel()
    {
        return $this->relationNodeModel;

    }

    public function getRelationModel()
    {
        $nodeModel = [];
        foreach ($this->relationNodeModel as $sort=>$relationNode) {
            list($model, $relationField) = $relationNode;
            $nodeModel[$sort] = $model;
        }
        return $nodeModel;
    }

    /**
     * Build More Nested array.
     *
     * @param array $nodeModels
     * @param array $nodes
     * @param int   $parentId
     * @param int $relationNodeId
     * @param int $nodeSort
     * @return array
     */
    protected function buildMoreNestedArray(array $nodeModels,array $nodes = [], $parentId = 0, $relationNodeId = 0,$nodeSort = 0)
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodesByNodeSort();
        }
        //对数据递归判断
        foreach ($nodes as $sort => $node) {
            if ($sort == $nodeSort) {
                foreach ($node as $relationId => $modelNodes) {
                    foreach ($modelNodes as $modelNode) {
                        if ($relationId == $relationNodeId && $modelNode[$nodeModels[$sort]->getParentColumn()] == $parentId) {
                            $modelNode['type'] = $sort;
                            //相同父级ID 获取当前类的子集 获取当前类关联类
                            $children = $this->buildMoreNestedArray($nodeModels,$nodes, $modelNode[$nodeModels[$sort]->getKeyName()],$relationNodeId,$sort);
                            $relationChildren = $this->buildMoreNestedArray($nodeModels,$nodes, 0,$modelNode[$nodeModels[$sort]->getKeyName()],$sort+1);
                            if ($children || $relationChildren) {
                                $modelNode['children'] = array_merge($children,$relationChildren);
                            }
                            $branch[] = $modelNode;
                        }
                    }
                }
            }
        }
        return $branch;
    }

    public function allNodesByNodeSort()
    {
        //通过当前关联模型 获取模型下的所有数据集合 按照 sort relationId 整合为三维数组 $nodes[$sort][$relationId] = allNodes()
        $nodes = [];
        //获取当前树的数据
        foreach ($this->relationNodeModel as $sort=>$relationNode) {
            $selfRelationNodes = [];
            list($model,$relationField) = $relationNode;
            $relationNodes = $model->allNodes();
            //按照关联字段的值分组保存
            foreach ($relationNodes as $node) {
                //无需关联或存在关联字段数据
                if (empty($relationField)) {
                    $selfRelationNodes[0][] = $node;
                    continue;
                }
                if (isset($node[$relationField]) && !empty($node[$relationField])) {
                    $selfRelationNodes[$node[$relationField]][] = $node;
                    continue;
                }
            }
            $nodes[$sort] = $selfRelationNodes;
        }
        return $nodes;
    }

    /**
     * Build options of select field in form.
     *
     * @param array  $nodes
     * @param int    $parentId
     * @param string $prefix
     * @param string $space
     *
     * @return array
     */
    protected function buildSelectOptions(array $nodes = [], $parentId = 0, $prefix = '', $space = '&nbsp;')
    {
        $prefix = $prefix ?: '┝'.$space;

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $index => $node) {
            if ($node[$this->getParentColumn()] == $parentId) {
                $node[$this->getTitleColumn()] = $prefix.$space.$node[$this->getTitleColumn()];

                $childrenPrefix = str_replace('┝', str_repeat($space, 6), $prefix).'┝'.str_replace(['┝', $space], '', $prefix);

                $children = $this->buildSelectOptions($nodes, $node[$this->getKeyName()], $childrenPrefix);

                $options[$node[$this->getKeyName()]] = $node[$this->getTitleColumn()];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }

    /**
     * Get options for Select field in form.
     *
     * @param \Closure|null $closure
     * @param string        $rootText
     *
     * @return array
     */
    public static function selectOptions(\Closure $closure = null, $rootText = 'ROOT')
    {
        $options = (new static())->withQuery($closure)->buildSelectOptions();

        return collect($options)->prepend($rootText, 0)->all();
    }

    public static function selectRelationOptions(\Closure $closure = null,$sort, $rootText = 'ROOT')
    {

    }


}
