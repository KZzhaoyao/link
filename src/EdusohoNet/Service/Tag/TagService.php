<?php
namespace EdusohoNet\Service\Tag;

interface TagService
{
    public function getTagByName($name);

    public function addTag($conditions);

    public function getTag($id);

    public function searchTags($conditions, $orderBy, $start, $limit);

    public function getTags($conditions, $orderBy, $start, $limit);


}
