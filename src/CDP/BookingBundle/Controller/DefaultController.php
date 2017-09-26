<?php

namespace CDP\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CDPBookingBundle:Default:index.html.twig');
    }
}
