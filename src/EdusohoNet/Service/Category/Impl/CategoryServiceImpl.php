<?php

namespace EdusohoNet\Service\Category\Impl;

use EdusohoNet\Service\Common\BaseService;
use EdusohoNet\Common\ArrayToolkit;
use EdusohoNet\Service\Category\CategoryService;

class CategoryServiceImpl extends BaseService implements CategoryService
{
    public function searchCategorysCount($conditions)
    {
        return $this->getCategoryDao()->searchCategorysCount($conditions);
    }

    public function searchCategorys($conditions, $orderBy, $start, $limit)
    {
        return $this->getCategoryDao()->searchCategorys($conditions, $orderBy, $start, $limit);
    }

    public function addCategory($conditions)
    {
        return $this->getCategoryDao()->addCategory($conditions);
    }

    public function updateCategory($id, $fileds)
    {
        return $this->getCategoryDao()->updateCategory($id, $fileds);
    }

    public function deleteCategoryById($id)
    {
        return $this->getCategoryDao()->deleteCategoryById($id);
    }

    public function findCategorysByIds(array $ids)
    {
        return ArrayToolkit::index($this->getCategoryDao()->findCategorysByIds($ids), 'id');
    }
    
    public function getCategoryById($Id)
    {
        return $this->getCategoryDao()->getCategoryById($Id);
    }

    protected function getCategoryDao()
    {
        return $this->createDao('Category.CategoryDao');
    }
}
