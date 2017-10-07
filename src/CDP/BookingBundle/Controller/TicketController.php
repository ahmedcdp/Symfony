<?php

// src/CDP/BookingBundle/Controller/TicketController.php

namespace CDP\BookingBundle\Controller;

use CDP\BookingBundle\Entity\Ticket;
use CDP\BookingBundle\Form\TicketType;

// N'oubliez pas ce use :
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TicketController extends Controller
{
  public function indexAction()
  {
    $content = $this->get('templating')->render('CDPBookingBundle:Ticket:index.html.twig');
    
    return new Response($content);
  }

  public function newAction(Request $request)
  {

  	$ticket = new Ticket();

    $form = $this->get('form.factory')->create(TicketType::class, $ticket);

	if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

		$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

		// on interroge la bdd pour savoir si il reste des billets pour cette date

		
		// return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $form->createView()));

    }

	return $this->render('CDPBookingBundle:Ticket:new.html.twig', array('form' => $form->createView()));
  }
}