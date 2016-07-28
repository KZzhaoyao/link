<?php

namespace EdusohoNet\Service\Link\Dao;

interface LinkDao
{
    public function searchLinks($conditions, $orderBy, $start, $limit);

    public function searchLinksCount($conditions);

    public function searchLinksByTitleOrTag($conditions, $orderBy, $start, $limit);

    public function searchLinksByTitleOrTagCount($conditions);

    public function addLink($conditions);

    public function updateHits($id, $fileds);

}
