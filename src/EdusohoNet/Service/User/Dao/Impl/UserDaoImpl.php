<?php

namespace EdusohoNet\Service\User\Dao\Impl;

use EdusohoNet\Service\Common\BaseDao;
use EdusohoNet\Service\User\Dao\UserDao;
use EdusohoNet\Service\Common\DaoEvent;

class UserDaoImpl extends BaseDao implements UserDao
{
    protected $table = 'user';
    
    public function searchLoginUser($conditions, $orderBy, $start, $limit)
    {
      $builder = $this->createUserQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);

            return $builder->execute()->fetchAll() ?: array();
    }

    public function getUser($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";

        return $this->getConnection()->fetchAssoc($sql, array($id)) ?: null;
    }  

    private function createUserQueryBuilder($conditions)
    {
      
        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->getTable(), 'user')
            ->andWhere('id = :id')
            ->andWhere('username = :username')
            ->andWhere('password = :password')
            ->andWhere('email = :email');
    }


}
