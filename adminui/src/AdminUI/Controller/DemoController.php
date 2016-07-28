<?php

namespace AdminUI\Controller;

use Silex\Application;

class DemoController
{
    public function indexAction(Application $app)
    {
        return $app['twig']->render('Demo/index.html.twig', array());
    }
}
