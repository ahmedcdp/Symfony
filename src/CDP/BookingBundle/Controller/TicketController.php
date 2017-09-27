<?php

// src/CDP/BookingBundle/Controller/TicketController.php

namespace CDP\BookingBundle\Controller;

// N'oubliez pas ce use :
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
  public function indexAction()
  {
    $content = $this->get('templating')->render('CDPBookingBundle:Ticket:index.html.twig');
    
    return new Response($content);
  }
}