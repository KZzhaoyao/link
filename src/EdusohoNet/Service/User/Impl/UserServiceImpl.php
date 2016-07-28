<?php
namespace EdusohoNet\Service\User\Impl;

use EdusohoNet\Service\Common\BaseService;
use EdusohoNet\Service\User\UserService;

class UserServiceImpl extends BaseService implements UserService
{
   

    public function searchLoginUser($conditions, $orderBy, $start, $limit)
    {
         return $this->getUserDao()->searchLoginUser($conditions, $orderBy, $start, $limit);
    }

    public function getUser($id)
    {
        return $this->getUserDao()->getUser($id);
    }

    protected function getUserDao()
    {
        return $this->createDao('User.UserDao');
    }

}
