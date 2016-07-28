<?php
namespace EdusohoNet\Service\User;

interface UserService
{

    public function searchLoginUser($conditions, $orderBy, $start, $limit);

    public function getUser($id);


}
