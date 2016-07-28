<?php

namespace EdusohoNet\Service\Category\Dao\Impl;

use EdusohoNet\Service\Common\BaseDao;
use EdusohoNet\Service\Category\Dao\CategoryDao;
use EdusohoNet\Service\Common\DaoEvent;

class CategoryDaoImpl extends BaseDao implements CategoryDao
{
    protected $table = 'category';

    public function searchCategorysCount($conditions)
    {
        $builder = $this->createCategoryQueryBuilder($conditions)
            ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchCategorys($conditions, $orderBy, $start, $limit)
    {
        $builder = $this->createCategoryQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ?: array();
    }
    public function addCategory($conditions)
    {
        $affected = $this->getConnection()->insert($this->table, $conditions);

        if ($affected <= 0) {
            throw $this->createDaoException('Insert gift error.');
        }

        return $this->getaddCategory($this->getConnection()->lastInsertId());
    }

    public function updateCategory($id, $fileds)
    {
        $this->getConnection()->update($this->table, $fileds, array('id' => $id));

        return $this->getaddCategory($id);
    }

    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";

        return $this->getConnection()->fetchAssoc($sql, array($id)) ?: null;
    }

    public function getaddCategory($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";

        return $this->getConnection()->fetchAssoc($sql, array($id)) ?: null;
    }

    public function deleteCategoryById($id)
    {
        return $this->getConnection()->delete($this->table, array('id' => $id));
    }

    public function findCategorysByIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        $marks = str_repeat('?,', count($ids) - 1).'?';
        $sql = "SELECT * FROM {$this->getTable()} WHERE id IN ({$marks});";

        return $this->getConnection()->fetchAll($sql, $ids);
    }
    
    private function createCategoryQueryBuilder($conditions)
    {
        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->getTable(), 'category')
            ->andWhere('id = :id')
            ->andWhere('name = :name');
    }
}
