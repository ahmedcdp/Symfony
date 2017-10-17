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
use CDP\BookingBundle\Form\TicketVisitorsType;

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

      //on verifie si jour ouvert (ferme les mardis, 01/05, 01/11, 25/12)
    	date_default_timezone_set('Europe/Paris');
    	
    	
    	if( $ticket->dateValid() === "tuesday" )
    	{
    		$request->getSession()->getFlashBag()->add('notice', 'Le musée est fermé le mardi');
    		return $this->redirectToRoute('cdp_booking_new');
    	}

     	else if( $ticket->dateValid() === "holiday" )
      	{
       		$request->getSession()->getFlashBag()->add('notice', 'Jour férié, le musée est fermé');
        	return $this->redirectToRoute('cdp_booking_new');
      	}

      	// test si il est plus de 14h pour un billet commande pour le meme jour en option pleine journee
     	else if($ticket->dateValid() === "halfday")
      	{
       	 	$request->getSession()->getFlashBag()->add('notice', 'Après 14h, votre billet sera un billet demi-journée');
        	$ticket->setHalfday(true);
      	}

		// on interroge la bdd pour savoir si il reste des billets pour cette date
      	$maxBillets = $this->container->getParameter('max-billets');
      	$repository = $this->getDoctrine()->getManager()->getRepository('CDPBookingBundle:Ticket');

 		$nbBillets = $repository->countByDate($ticket->getDate());
 		$nbBilletDispo = $maxBillets - $nbBillets;
 		if($nbBilletDispo <= 0)
      	{
       	 	$request->getSession()->getFlashBag()->add('notice', 'Plus de place disponible pour cette date');
        	return $this->redirectToRoute('cdp_booking_new');
      	}

		 $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
		 return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket, 'haveErrors' =>"false"));

    }
        return $this->render('CDPBookingBundle:Ticket:new.html.twig', array('form' => $form->createView()));
  }


  public function saveAction(Request $request)
  {

    $ticket = new Ticket();

    $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
    if ($request->isMethod('POST') && $formVisitor->handleRequest($request)->isValid()) {
      //sauvegarde en bdd
      $em = $this->getDoctrine()->getManager();
      $em->persist($ticket);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée');
      return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('ticket' =>$ticket));
    }
    return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket, 'haveErrors' =>"true"));
  }


}