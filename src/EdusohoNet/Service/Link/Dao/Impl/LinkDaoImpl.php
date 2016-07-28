<?php

namespace EdusohoNet\Service\Link\Dao\Impl;

use EdusohoNet\Service\Common\BaseDao;
use EdusohoNet\Service\Link\Dao\LinkDao;

class LinkDaoImpl extends BaseDao implements LinkDao
{
    protected $table = 'link';

    public function searchLinksCount($conditions)
    {
        $builder = $this->createLinkQueryBuilder($conditions)
            ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchLinks($conditions, $orderBy, $start, $limit)
    {
        $builder = $this->createLinkQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ?: array();
    }

    public function searchLinksByTitleOrTagCount($conditions)
    {
        $builder = $this->createLikeLinkQueryBuilder($conditions)
            ->select('COUNT(id)');

        return $builder->execute()->fetchColumn(0);
    }

    public function searchLinksByTitleOrTag($conditions, $orderBy, $start, $limit)
    {
        $builder = $this->createLikeLinkQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);

        return $builder->execute()->fetchAll() ?: array();
    }

    // public function searchTagLinks($conditions, $orderBy, $start, $limit)
    // {
    //     $builder = $this->createLinkQueryBuilder($conditions)
    //         ->select('*')
    //         ->orderBy($orderBy[0], $orderBy[1])
    //         ->setFirstResult($start)
    //         ->setMaxResults($limit);

    //     return $builder->execute()->fetchAll() ?: array();
    // }

    public function addLink($conditions)
    {
        $affected = $this->getConnection()->insert($this->table, $conditions);

        if ($affected <= 0) {
            throw $this->createDaoException('Insert gift error.');
        }

        return $this->getLink($this->getConnection()->lastInsertId());
    }

    public function updateHits($id, $fileds)
    {
        $this->getConnection()->update($this->table, $fileds, array('id' => $id));

        return $this->getLink($id);
    }

    public function getLink($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";

        return $this->getConnection()->fetchAssoc($sql, array($id)) ?: null;
    }

    private function createLikeLinkQueryBuilder($conditions)
    {
        foreach (array('title') as $key) {
            if (isset($conditions[$key])) {
                $conditions[$key] = "%{$conditions[$key]}%";
            }
        }

        if (empty($conditions['title'])) {
            return $this->createDynamicQueryBuilder($conditions)
            ->from($this->getTable(), 'link');
        }
        $builder = $this->createDynamicQueryBuilder($conditions)
            ->from($this->getTable(), 'link')
            ->orWhere('title LIKE :title');
        if (empty($conditions['tags'])) {
            return $builder;
        }
        foreach ($conditions['tags'] as $tag) {
            $tag = '|'.$tag.'|';
            $builder->orWhere("tags LIKE '%{$tag}%'");

            // var_dump($builder->getSQL());exit();
        }
         return $builder;
    }

    private function createLinkQueryBuilder($conditions)
    {
        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->getTable(), 'link')
            ->andWhere('id = :id')
            ->andWhere('userId = :userId')
            ->andWhere('title = :title')
            ->andWhere('url = :url')
            ->andWhere('hits = :hits')
            ->andWhere('categoryId = :categoryId')
            ->andWhere('tags LIKE :tags');
    }
}
