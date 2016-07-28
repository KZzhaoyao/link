<?php

namespace EdusohoNet\Service\Tag\Dao\Impl;

use EdusohoNet\Service\Common\BaseDao;
use EdusohoNet\Service\Tag\Dao\TagDao;

class TagDaoImpl extends BaseDao implements TagDao
{
    protected $table = 'tag';

    public function searchTags($conditions, $orderBy, $start, $limit)
    {
        $builder = $this->createLikeTagsQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);

        return $builder->execute()->fetchAll() ?: array();
    }

   public function getTags($conditions, $orderBy, $start, $limit)
    {
        $builder = $this->createTagsQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);

        return $builder->execute()->fetchAll() ?: array();
    }

    public function getTagByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tags = ? LIMIT 1";

        return $this->getConnection()->fetchAssoc($sql, array($name)) ?: null;
    }

    public function addTag($conditions)
    {
        $affected = $this->getConnection()->insert($this->table, $conditions);

        if ($affected <= 0) {
            throw $this->createDaoException('Insert gift error.');
        }

        return $this->getTag($this->getConnection()->lastInsertId());
    }

    public function getTag($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";

        return $this->getConnection()->fetchAssoc($sql, array($id)) ?: null;
    }

    private function createTagsQueryBuilder($conditions)
    {
               return $this->createDynamicQueryBuilder($conditions)
            ->from($this->getTable(), 'tag');
        }

    private function createLikeTagsQueryBuilder($conditions)
    {
        $conditions = array_filter($conditions, function ($value) {
                        if ($value === '' || $value === null) {
                            return false;
                        }

                        return true;
                    });
        foreach (array('title') as $key) {
            if (isset($conditions[$key])) {
                $conditions[$key] = "%{$conditions[$key]}%";
            }
        }

        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->getTable(), 'tag')
            ->andWhere('tags LIKE :title');
    }
}
