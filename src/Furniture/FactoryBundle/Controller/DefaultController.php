<?php

namespace Furniture\FactoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FurnitureFactoryBundle:Default:index.html.twig', array('name' => $name));
    }
}
