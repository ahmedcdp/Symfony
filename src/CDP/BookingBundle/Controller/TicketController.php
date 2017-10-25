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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use CDP\BookingBundle\Form\VisitorType;
use CDP\BookingBundle\Form\TicketVisitorsType;
use CDP\BookingBundle\Form\TicketPrixType;

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
  //      $session = $request->getSession();
   //     $session->set('etape1', $ticket);
    
        // ajout colletor a form

		    $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
		    return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket, 'haveErrors' =>"false"));
    }     
    return $this->render('CDPBookingBundle:Ticket:new.html.twig', array('form' => $form->createView()));
  }


  public function resumeAction(Request $request)
  {

    $ticket = new Ticket();

    $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
    if ($request->isMethod('POST') && $formVisitor->handleRequest($request)->isValid()) {
      $ticket->calcPrixTotal();
      $form = $this->get('form.factory')->create(TicketPrixType::class, $ticket);
      
      return $this->render('CDPBookingBundle:Ticket:resume.html.twig', array('form' => $form->createView(), 'ticket' =>$ticket));
    }
    return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket, 'haveErrors' =>"true"));
  }

  public function saveAction(Request $request)
  {

    $ticket = new Ticket();

    $form = $this->get('form.factory')->create(TicketPrixType::class, $ticket);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

    	$ticket->calcPrixTotal();
      	//sauvegarde en bdd
      	$em = $this->getDoctrine()->getManager();
      	$em->persist($ticket);
      	$em->flush();
      	$request->getSession()->getFlashBag()->add('notice', 'Nous vous remercions pour votre commande');
      	return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('ticket' =>$ticket));
    }
    return $this->render('CDPBookingBundle:Ticket:resume.html.twig', array('form' => $form->createView(), 'ticket' =>$ticket));
  }


}