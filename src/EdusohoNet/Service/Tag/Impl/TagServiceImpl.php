<?php

namespace EdusohoNet\Service\Tag\Impl;

use EdusohoNet\Service\Common\BaseService;
use EdusohoNet\Service\Tag\TagService;

class TagServiceImpl extends BaseService implements TagService
{
    public function getTag($id)
    {
        return $this->getTagDao()->getTag($id);
    }
    
    public function getTagByName($name)
    {
        return $this->getTagDao()->getTagByName($name);
    }

    public function addTag($conditions)
    {
        return $this->getTagDao()->addTag($conditions);
    }


    public function searchTags($conditions, $orderBy, $start, $limit)
    {
        return $this->getTagDao()->searchTags($conditions, $orderBy, $start, $limit);
    }

    public function getTags($conditions, $orderBy, $start, $limit)
    {
        return $this->getTagDao()->getTags($conditions, $orderBy, $start, $limit);
    }
    
    protected function getTagDao()
    {
        return $this->createDao('Tag.TagDao');
    }

}
