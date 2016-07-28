<?php

namespace AdminUI\Controller;

use Silex\Application;
use EdusohoNet\Service\Common\ServiceKernel;
use Symfony\Component\HttpFoundation\Request;
use EdusohoNet\Common\ArrayToolkit;
use EdusohoNet\Common\Paginator;

class LinkController
{
    public function linkAction(Application $app, Request $request)
    {
        $conditions = $request->query->all();
        if (empty($conditions['tags'])) {
            $paginator = new Paginator(
            $request,
            $this->getLinkService()->searchLinksCount($conditions),
            20
        );
        } else {
            $conditions['tags'] = '%|'.$conditions['tags'].'|%';
            $paginator = new Paginator(
            $request,
            $this->getLinkService()->searchLinksCount($conditions),
            20
        );
        }

        $links = $this->getLinkService()->searchLinks($conditions, array('createdTime', 'DESC'),
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

        return $app['twig']->render('Link/link.html.twig', array(
            'category' => ArrayToolkit::index($category, 'id'),
             'links' => $links,
              'tags' => $tags,
              'categorys' => $categorys,
            'paginator' => $paginator,
            ));
    }

    public function adminAction(Application $app, Request $request)
    {
        $tags = $this->getTagService()->getTags(array('tags' => null), array('createdTime', 'DESC'), 0, 99);
        $paginator = new Paginator(
            $request,
            $this->getCategoryService()->searchCategorysCount(array()),
            20
        );
        $categorys = $this->getCategoryService()->searchCategorys(array(), array('createdTime', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount());

        return $app['twig']->render('Admin/admin.html.twig', array(
              'tags' => $tags,
            'categorys' => $categorys, ));
    }
    public function searchLinksAction(Application $app, Request $request)
    {
        $conditions = $request->query->all();

        if (!empty($conditions['title'])) {
            $tagIds = $this->getTagService()->searchTags($conditions, array('createdTime', 'DESC'), 0, 99);
            $tagString = array();
            foreach ($tagIds as $tagId) {
                array_push($tagString, $tagId['id']);
            }
            $conditions['tags'] = $tagString;
        }
        $paginator = new Paginator(
            $request,
            $this->getLinkService()->searchLinksByTitleOrTagCount($conditions),
            20
        );
        $links = $this->getLinkService()->searchLinksByTitleOrTag($conditions, array('createdTime', 'DESC'),
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
        return $app['twig']->render('Link/link.html.twig', array(
            'category' => ArrayToolkit::index($category, 'id'),
             'links' => $links,
              'conditions' => '',
              'tags' => $tags,
            'paginator' => $paginator,
            'categorys' => $categorys,
            ));
    }

    public function linkUrl(Application $app, Request $request)
    {
        $conditions = $request->query->all();
        $link = $this->getLinkService()->searchLinks($conditions, array('createdTime', 'DESC'), 0, 20
        );
        $hits = array('hits' => $link[0]['hits'] + 1);
        $this->getLinkService()->updateHits($conditions['id'], $hits);

        return $app->redirect($link[0]['url']);
    }

    public function addLinkAction(Application $app, Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $hiddenTag = $request->request->get('hiddenTag', 'false');
            if ($hiddenTag == 'true') {
                $conditions = $request->request->all();
                unset($conditions['hiddenTag']);
                $link = 'http://';
                if (!strstr($conditions['url'], $link)) {
                    $conditions['url'] = $link.''.$conditions['url'];
                }
                if (!empty($conditions['tags'])) {
                    $tags = split(' ', $conditions['tags']);
                    $tagString = null;
                    foreach ($tags as $tag) {
                        $tagid = $this->getTagService()->getTagByName($tag);
                        if (empty($tagid)) {
                            $tagid = $this->getTagService()->addTag(array('tags' => $tag));
                        }
                        $tagString = $tagid['id'].'|'.$tagString;
                    }
                    $conditions['tags'] = '|'.$tagString;
                }
                $conditions['userId'] = $request->getSession()->get('id');
                $conditions['createdTime'] = time();
                $this->getLinkService()->addLink($conditions);
                $result = ['status' => 'false'];

                return $app->json($result);
            }
            $url = $request->request->all();
            $title = $this->resolveURL($url['url']);
            $categorys = $this->getCategoryService()->searchCategorys(array(), array('createdTime', 'DESC'), 0, 99);
            $category = array();
            foreach ($categorys as $key) {
                $category[$key['id']] = $key['name'];
            }
            // var_dump($category);
            // exit();
            $result = $app['twig']->render('Link/add-link.html.twig', array(
                'title' => $title,
                'category' => $category,
                ));

            return $app->json(['html' => $result]);
        }

        return $app['twig']->render('Link/add-link-modal.html.twig', array());
    }

    public function addCategoryAction(Application $app, Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $conditions = $request->request->all();
            $this->getCategoryService()->addCategory($conditions);
            $result = ['status' => 'false'];

            return $app->json($result);
        }

        return $app['twig']->render('Admin/add-category.html.twig');
    }
    public function updateCategoryAction(Application $app, Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $conditions = $request->request->all();
            $this->getCategoryService()->updateCategory($conditions['id'], array('name' => $conditions['name']));
            $result = ['status' => 'false'];

            return $app->json($result);
        }

        $conditions = $request->query->all();
        $categoryName = $this->getCategoryService()->getCategoryById($conditions['categoryId']);

        return $app['twig']->render('Admin/update-category.html.twig', array('categoryName' => $categoryName));
    }

    public function deleteCategoryAction(Application $app, Request $request)
    {
        $conditions = $request->query->all();
        $this->getCategoryService()->deleteCategoryById($conditions['categoryId']);

        return $app->json(array('status' => 'ok'));
    }
    public function addPluginLinkAction(Application $app, Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $conditions = $request->request->all();
            $link = 'http://';
            if (!strstr($conditions['url'], $link)) {
                $conditions['url'] = $link.''.$conditions['url'];
            }
            if (!empty($conditions['tags'])) {
                $tags = split(' ', $conditions['tags']);
                $tagString = null;
                foreach ($tags as $tag) {
                    $tagid = $this->getTagService()->getTagByName($tag);
                    if (empty($tagid)) {
                        $tagid = $this->getTagService()->addTag(array('tags' => $tag));
                    }
                    $tagString = $tagid['id'].'|'.$tagString;
                }
                $conditions['tags'] = '|'.$tagString;
            }
            $conditions['userId'] = $request->getSession()->get('id');
            $conditions['createdTime'] = time();

            $this->getLinkService()->addLink($conditions);
            $result = ['status' => 'false'];

            return $app->json($result);
        }
        $url = $request->query->all();
        $title = $this->resolveURL($url['link']);
        $tags = $this->getTagService()->getTags(array('tags' => null), array('createdTime', 'DESC'), 0, 99);
        $categorys = $this->getCategoryService()->searchCategorys(array(), array('createdTime', 'DESC'), 0, 99);
        $category = array();
        foreach ($categorys as $key) {
            $category[$key['id']] = $key['name'];
        }

        return $app['twig']->render('Link/add-plugin-link.html.twig', array(
            'url' => $url,
            'title' => $title,
            'tags' => $tags,
            'categorys' => $categorys,
            'category' => $category,
            ));
    }

    protected function resolveURL($url)
    {
        $useragent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/47.0.2526.106 Chrome/47.0.2526.106 Safari/537.36';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_USERAGENT, $useragent);
        $data = curl_exec($c);
        // var_dump($data);exit();
        curl_close($c);
        $pos = strpos($data, 'utf-8');
        $pos1 = strpos($data, 'charset');
        if ($pos1) {
        preg_match("/<title>([\S\s]*?)<\/title>/", $data, $title);
        // var_dump($title);exit();
        return $title[1];
        }
        elseif ($pos1) {
            $wcharset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i",$data,$temp) ? strtolower($temp[1]):""; 
            $data = iconv($temp[1], 'utf-8', $data);
        }
        preg_match("/<title>([\S\s]*?)<\/title>/", $data, $title);
        // var_dump($title);exit();
        return $title[1];
    }

    public function sub_title($title)
    {
        $len = 20;

        return mb_strlen($title, 'utf-8') <= $len ? $title : (mb_substr($title, 0, $len, 'utf-8').'...');
    }

    protected function getCategoryService()
    {
        return ServiceKernel::instance()->createService('Category.CategoryService');
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
}
