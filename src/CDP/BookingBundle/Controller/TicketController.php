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
    	$sDate=date_format($ticket->getDate(), 'd-m-Y');
    	 $tDate = explode('-', $sDate);
    	 $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

       $currentDate = date("d-m-Y");
       $heure = date("H");

    	 //date('m'),date('d'),date('Y')
    	 $day = $days[date('w', mktime(0, 0, 0, $tDate[1], $tDate[0], $tDate[2]))];
    	if( $day === "Tuesday" )
    	{
    		$request->getSession()->getFlashBag()->add('notice', 'Le musée est fermé le mardi');
    		return $this->redirectToRoute('cdp_booking_new');
    	}

     if( ( ($tDate[0]==='01') && ( ($tDate[1]==='05') || ($tDate[1]==='11') ) ) || ( ($tDate[0]==='25') && ($tDate[1]==='12') ) )
      {
        $request->getSession()->getFlashBag()->add('notice', 'Jour férié, le musée est fermé');
        return $this->redirectToRoute('cdp_booking_new');
      }

      // test si il est plus de 14h pour un billet commande pour le meme jour en option pleine journee
      else if(($sDate == $currentDate ) && ($heure >= 14))
      {
         $request->getSession()->getFlashBag()->add('notice', 'il est plus de 14h');
          $ticket->setHalfday(true);
      }

		// on interroge la bdd pour savoir si il reste des billets pour cette date
		
		$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

		$formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);

		 return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket));

    }
    return $this->redirectToRoute('cdp_booking_new');
  }
  public function saveAction(Request $request)
  {

    $ticket = new Ticket();

    $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
    if ($request->isMethod('POST') && $formVisitor->handleRequest($request)->isValid()) {

      return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('ticket' =>$ticket));
    }
    return $this->redirectToRoute('cdp_booking_new');
  }


}