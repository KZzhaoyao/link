<?php

namespace EdusohoNet\Service\Category\Dao;

interface CategoryDao
{    
    public function searchCategorys($conditions, $orderBy, $start, $limit);

    public function searchCategorysCount($conditions);
    
    public function addCategory($conditions);

    public function updateCategory($id, $fileds);

    public function getCategoryById($Id);
    
    public function deleteCategoryById($Id);

    public function findCategorysByIds($ids);
}
