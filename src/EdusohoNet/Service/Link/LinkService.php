<?php

namespace EdusohoNet\Service\Link;

interface LinkService
{
    public function searchLinks($conditions, $orderBy, $start, $limit);

    public function searchLinksCount($conditions);

    public function searchLinksByTitleOrTagCount($conditions);

    public function searchLinksByTitleOrTag($conditions, $orderBy, $start, $limit);

    public function addLink($conditions);

    public function updateHits($id, $fileds);
}
