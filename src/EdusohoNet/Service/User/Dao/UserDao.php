<?php

namespace EdusohoNet\Service\User\Dao;

interface UserDao
{
    public function searchLoginUser($conditions, $orderBy, $start, $limit);

    public function getUser($id);
}
