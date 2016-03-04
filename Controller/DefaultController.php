<?php

namespace BlueSteel42\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BlueSteel42SettingsBundle:Default:index.html.twig');
    }
}
