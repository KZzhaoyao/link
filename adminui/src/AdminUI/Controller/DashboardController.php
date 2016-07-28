<?php

namespace AdminUI\Controller;

use Silex\Application;
use EdusohoNet\Service\Common\ServiceKernel;
use Symfony\Component\HttpFoundation\Request;
use EdusohoNet\Common\ArrayToolkit;
use EdusohoNet\Common\Paginator;

class DashboardController
{
    public function indexAction(Application $app, Request $request)
    {
        $paginator = new Paginator(
            $request,
            $this->getLinkService()->searchLinksCount(array()),
            20
        );
        $links = $this->getLinkService()->searchLinks(array(), array('createdTime', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount());
        foreach ($links as &$link) {
            $link['sub_title'] = $this->sub_title($link['title']);
            $link['tags'] = substr($link['tags'], 1, strlen($link['tags']) - 2);
            $tags = explode('|', $link['tags']);
            $tagString = null;
            foreach ($tags as $tag) {
                $tagid = $this->getTagService()->getTag($tag);
                $tagString = $tagid['tags'].' '.$tagString;
            }
            $link['tags'] = $tagString;
            $link['user'] = $this->getUserService()->getUser($link['userId']);
        }
        $categoryIds = ArrayToolkit::column($links, 'categoryId');
        $category = $this->getCategoryService()->findCategorysByIds($categoryIds);
        $tags = $this->getTagService()->getTags(array('tags' => null), array('createdTime', 'DESC'), 0, 99);
        $categorys = $this->getCategoryService()->searchCategorys(array(), array('createdTime', 'DESC'), 0, 99);
        return $app['twig']->render('index.html.twig', array(
            'category' => ArrayToolkit::index($category, 'id'),
             'links' => $links,
              'tags' => $tags,
            'paginator' => $paginator,
            'categorys' => $categorys,
            ));
    }
    public function sub_title($title)
    {
        $len = 20;

        return mb_strlen($title, 'utf-8') <= $len ? $title : (mb_substr($title, 0, $len, 'utf-8').'...');
    }
    protected function getLinkService()
    {
        return ServiceKernel::instance()->createService('Link.LinkService');
    }
    protected function getUserService()
    {
        return ServiceKernel::instance()->createService('User.UserService');
    }

    protected function getTagService()
    {
        return ServiceKernel::instance()->createService('Tag.TagService');
    }
    
    protected function getCategoryService()
    {
        return ServiceKernel::instance()->createService('Category.CategoryService');
    }
}
