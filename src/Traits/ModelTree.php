<?php

namespace Encore\AdminRbac\Traits;

use Encore\Admin\Traits\ModelTree AS BaseTree;
use Illuminate\Database\Eloquent\Model;

trait ModelRelationTree
{
    use BaseTree;

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
        return $this->buildMoreNestedArray($this->getRelationNodeModel());
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
        if (!property_exists($relation,'relationNodes') || is_null($relation->relationNodes)) {
            $this->relationNodes = [];
            return;
        }
        array_unshift($relation->relationNodes,[$relation,$relationField]);
    }

    public function getRelationNodeModel()
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
                        if ($relationId === $relationNodeId && $modelNode[$nodeModels[$sort]->getParentColumn()] == $parentId) {
                            $children = $this->buildMoreNestedArray($nodes, $modelNode[$nodeModels[$sort]->getKeyName()],$relationNodeId,$sort);
                            if ($children) {
                                $node['children'] = $children;
                            }
                            $branch[] = $node;
                        }
                    }
                }
            }
            if ($sort == $nodeSort+1) {
                foreach ($node as $relationId => $modelNodes) {
                    if ($relationId === $parentId) {
                        $children = $this->buildMoreNestedArray($nodes, 0,$parentId,$sort);
                        if ($children) {
                            $node['children'] = $children;
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
                    $selfRelationNodes[0] = $node;
                    continue;
                }
                if (isset($node[$relationField]) && !empty($node[$relationField])) {
                    $selfRelationNodes[$node[$relationField]] = $node;
                    continue;
                }
            }
            $nodes[$sort] = $selfRelationNodes;
        }
        return $nodes;
    }

}
