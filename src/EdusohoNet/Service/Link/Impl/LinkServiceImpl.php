<?php

namespace EdusohoNet\Service\Link\Impl;

use EdusohoNet\Service\Common\BaseService;
use EdusohoNet\Service\Link\LinkService;

class LinkServiceImpl extends BaseService implements LinkService
{

    public function searchLinksCount($conditions)
    {
        return $this->getLinkDao()->searchLinksCount($conditions);
    }

    public function searchLinks($conditions, $orderBy, $start, $limit)
    {
        return $this->getLinkDao()->searchLinks($conditions, $orderBy, $start, $limit);
    }

    public function searchLinksByTitleOrTagCount($conditions)
    {
        return $this->getLinkDao()->searchLinksByTitleOrTagCount($conditions);
    }

    public function searchLinksByTitleOrTag($conditions, $orderBy, $start, $limit)
    {
        return $this->getLinkDao()->searchLinksByTitleOrTag($conditions, $orderBy, $start, $limit);
    }

    public function addLink($conditions)
    {
        return $this->getLinkDao()->addLink($conditions);
    }
    
    public function updateHits($id, $fileds)
    {
        return $this->getLinkDao()->updateHits($id, $fileds);
    }
    
    protected function getLinkDao()
    {
        return $this->createDao('Link.LinkDao');
    }

}
