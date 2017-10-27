<?php

// src/CDP/BookingBundle/Controller/TicketController.php

namespace CDP\BookingBundle\Controller;

use CDP\BookingBundle\Entity\Ticket;
use CDP\BookingBundle\Form\TicketType;
use CDP\BookingBundle\Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CDP\BookingBundle\Form\TicketVisitorsType;


class TicketController extends Controller
{
  public function indexAction()
  {
    $content = $this->get('templating')->render('CDPBookingBundle:Ticket:index.html.twig');
    
    return new Response($content);
  }

  public function newAction(Request $request)
  {
      $session = $request->getSession();
      $ticket = new Ticket();
      $form = $this->get('form.factory')->create(TicketType::class, $ticket);
      $session->set('etape1', $ticket);
      return $this->render('CDPBookingBundle:Ticket:new.html.twig', array('form' => $form->createView()));
  }



  public function addAction(Request $request)
  {
      $session = $request->getSession();
      if(!$session->has('etape1')){
          return $this->redirectToRoute('cdp_booking_homepage');
      }
      $ticket = $session->get('etape1');
  	$form = $this->get('form.factory')->create(TicketType::class, $ticket);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $session = $request->getSession();
        $session->set('etape2', $ticket);

        $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
        return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket, 'haveErrors' =>"false"));
    }     
    return $this->render('CDPBookingBundle:Ticket:new.html.twig', array('form' => $form->createView()));
  }


  public function resumeAction(Request $request)
  {
      $session = $request->getSession();
      if(!$session->has('etape2')){
          return $this->redirectToRoute('cdp_booking_homepage');
      }
      $ticket = $session->get('etape2');


      $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
      if ($request->isMethod('POST') && $formVisitor->handleRequest($request)->isValid()) {
          $ticket->calcPrixTotal();
          $prix = $ticket->getPrixTotal();
        if($prix ===0){
            $session->getFlashBag()->add('notice', 'Veuillez ajouter un adulte pour accompagner les enfants');
            return $this->redirectToRoute('cdp_booking_new');
        }
        $session->set('etape3', $ticket);
        return $this->render('CDPBookingBundle:Ticket:resume.html.twig', array('ticket' =>$ticket));
      }
    return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' =>$ticket, 'haveErrors' =>"true"));
  }

  public function saveAction(Request $request)
  {

      $session = $request->getSession();
      if(!$session->has('etape3')){
          return $this->redirectToRoute('cdp_booking_homepage');
      }
      $ticket = $session->get('etape3');

      //recuperation de la key
      \Stripe\Stripe::setApiKey("sk_test_goZbtaGRcbkF1Zx3gXkNX4XF");

      // Get the credit card details submitted by the form
      $token = $_POST['stripeToken'];
      // Create a charge: this will charge the user's card
      try {
          $charge = \Stripe\Charge::create(array(
              "amount" => 1000, // Amount in cents
              "currency" => "eur",
              "source" => $token,
              "description" => "Paiement Stripe - OpenClassrooms Exemple"
          ));
          $session->getFlashBag()->add('notice', 'paiement validé');
          return $this->redirectToRoute("cdp_booking_resume");
      } catch(\Stripe\Error\Card $e) {
          $session->getFlashBag()->add('notice', 'paiement refusé');
          return $this->redirectToRoute("cdp_booking_resume");
          // The card has been declined

      }




      //sauvegarde en bdd
      $em = $this->getDoctrine()->getManager();
      $em->persist($ticket);
      $em->flush();
      $session->getFlashBag()->add('notice', 'Nous vous remercions pour votre commande');
      return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('ticket' =>$ticket));
  }


}