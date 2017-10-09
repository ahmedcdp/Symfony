<?php

// src/CDP/BookingBundle/Controller/TicketController.php

namespace CDP\BookingBundle\Controller;

use CDP\BookingBundle\Entity\Ticket;
use CDP\BookingBundle\Form\TicketType;

// N'oubliez pas ce use :
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use CDP\BookingBundle\Form\VisitorType;

class TicketController extends Controller
{
  public function indexAction()
  {
    $content = $this->get('templating')->render('CDPBookingBundle:Ticket:index.html.twig');
    
    return new Response($content);
  }

  public function newAction()
  {

  	$ticket = new Ticket();

    $form = $this->get('form.factory')->create(TicketType::class, $ticket);

	return $this->render('CDPBookingBundle:Ticket:new.html.twig', array('form' => $form->createView()));
  }

  public function addAction(Request $request)
  {
  	$ticket = new Ticket();
  	$form = $this->get('form.factory')->create(TicketType::class, $ticket);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

		// on interroge la bdd pour savoir si il reste des billets pour cette date
		$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

		$formVisitor = $this->get('form.factory')->create(TicketType::class, $ticket);
		$formVisitor->add('visitors', CollectionType::class, array(
            'entry_type'=> VisitorType::class,
            "label" => "Visiteurs",
            'allow_add' =>true,
            'allow_delete' => true))
		->remove('date')
		->remove('halfday')
		->remove('number')
		->remove('email');

		 return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket));

    }
    return $this->redirectToRoute('cdp_booking_new');
  }

}