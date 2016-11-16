<?php

namespace DcD\RestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BasketItemController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
