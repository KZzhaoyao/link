<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Request::setTrustedProxies(array('127.0.0.1'));

// $app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

$app->boot();

$app['DashboardController'] = $app->share(function () use ($app) {
    return new AdminUI\Controller\DashboardController();
});

$app['DemoController'] = $app->share(function () use ($app) {
    return new AdminUI\Controller\DemoController();
});

$app['LinkController'] = $app->share(function () use ($app) {
    return new AdminUI\Controller\LinkController();
});

$app['UserController'] = $app->share(function () use ($app) {
    return new AdminUI\Controller\UserController();
});

$app->get('/', 'DashboardController:indexAction')->bind('homepage');
$app->get('/demo', 'DemoController:indexAction')->bind('demo');

$app->get('/add/link', 'LinkController:addLinkAction')
->method('GET|POST')
->bind('add_link');

$app->get('/add/category', 'LinkController:addCategoryAction')
->method('GET|POST')
->bind('add_category');

$app->get('/update/category', 'LinkController:updateCategoryAction')
->method('GET|POST')
->bind('update_category');

$app->get('/delete/category', 'LinkController:deleteCategoryAction')
->method('GET|POST')
->bind('delete_category');

$app->get('/add/plugin/link', 'LinkController:addPluginLinkAction')
->method('GET|POST')
->bind('add_plugin_link');

$app->get('/search', 'LinkController:searchLinksAction')
->method('GET|POST')
->bind('search_input');

$app->get('/link', 'LinkController:linkAction')
->method('GET|POST')
->bind('search_link');

// $app->get('/link', 'LinkController:linkAction')
// ->method('GET|POST')
// ->bind('technology_link');

$app->get('/link/url', 'LinkController:linkUrl')
->method('GET|POST')
->bind('url_link');

$app->get('/link', 'LinkController:searchTagAction')
->method('GET')
->bind('search_tag');

$app->get('/user/link', 'UserController:userLink')
->method('GET|POST')
->bind('user_link');

$app->get('/admin/category', 'LinkController:adminAction')
->method('GET|POST')
->bind('admin');

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('Login/login.html.twig', array());
});

$app->get('/login/check', 'UserController:checkLoginAction')
->method('GET|POST')
->bind('login_check');

$app->get('/logout', function (Request $request) use ($app) {
    session_destroy();
    return $app['twig']->render('Login/login.html.twig', array());
});

// $app->mount('/hosts', include __DIR__.'/Routing/HostManage.php');
// $app->mount('/admins', include __DIR__.'/Routing/Admins.php');
// $app->mount('/hostauths', include __DIR__.'/Routing/HostAuth.php');
// $app->mount('/hostbackups', include __DIR__.'/Routing/HostBackup.php');
// $app->mount('/jobs', include __DIR__.'/Routing/Job.php');
// $app->mount('/script', include __DIR__.'/Routing/Script.php');

$app->before(function (Request $request) use ($app) {
     $uri = $request->server->get('REQUEST_URI');
      if ($uri == '/login' || $uri == '/login/check') {
          return;
      }
      // if ($uri == '/logout') {
      //     session_destroy();

      //     return $app->redirect('/login');
      // }
      if ($uri == '/admin') {
          $userId = $request->getSession()->get('id');
          if ($userId == 1) {
              return;
          } else {
              return $app->redirect('/');
          }
      }
    $userName = $request->getSession()->get('username');
    if ($userName == null) {
        return $app->redirect('/login');
    }

    return;

});

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $app['logger']->error("Throw Exception: {$e->getMessage()}");

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
