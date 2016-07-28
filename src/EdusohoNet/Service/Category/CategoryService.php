<?php

namespace EdusohoNet\Service\Category;

interface CategoryService
{
    public function searchCategorys($conditions, $orderBy, $start, $limit);

    public function searchCategorysCount($conditions);

    public function addCategory($conditions);

    public function updateCategory($id, $fileds);

    public function getCategoryById($id);

    public function deleteCategoryById($id);

    public function findCategorysByIds(array $ids);
}
