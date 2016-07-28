<?php

namespace AdminUI\Controller;

use Silex\Application;
use EdusohoNet\Service\Common\ServiceKernel;
use Symfony\Component\HttpFoundation\Request;
use EdusohoNet\Common\ArrayToolkit;
use EdusohoNet\Common\Paginator;

class UserController
{
    public function checkLoginAction(Application $app, Request $request)
    {
        $conditions = $request->request->all();

        $user = $this->getUserService()->searchLoginUser(
            $conditions, array('createdTime', 'DESC'), 0, 20
        );
        if ($user == null) {
            return $app['twig']->render('Login/login.html.twig');
        }
        $session = $request->getSession();

        if ($session->get('username') == null) {
            $session->set('id', $user[0]['id']);
            $session->set('username', $user[0]['username']);
            $session->set('password', $user[0]['password']);
            $session->set('email', $user[0]['email']);
        }

        return $app->redirect('/');
    }

    public function userLink(Application $app, Request $request)
    {
        $conditions = array('userId' => $session = $request->getSession()->get('id'));
        $paginator = new Paginator(
            $request,
            $this->getLinkService()->searchLinksCount($conditions),
            20
        );
        $links = $this->getLinkService()->searchLinks($conditions, array('createdTime', 'DESC'), 0, 99);
        foreach ($links as &$link) {
            $link['sub_title'] = $this->sub_title($link['title']);
            $link['tags'] = substr($link['tags'], 1, strlen($link['tags'])-2);
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
        return $app['twig']->render('Link/link.html.twig', array(
            'category' => ArrayToolkit::index($category, 'id'),
             'links' => $links,
              'conditions' => '',
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

    protected function getUserService()
    {
        return ServiceKernel::instance()->createService('User.UserService');
    }

    protected function getLinkService()
    {
        return ServiceKernel::instance()->createService('Link.LinkService');
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
