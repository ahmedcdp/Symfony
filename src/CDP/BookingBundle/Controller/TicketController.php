<?php

// src/CDP/BookingBundle/Controller/TicketController.php

namespace CDP\BookingBundle\Controller;

use CDP\BookingBundle\Entity\Ticket;
use CDP\BookingBundle\Form\TicketType;
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
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $session->set('etape1', $ticket);
          return $this->redirectToRoute('cdp_booking_add');
      }
      return $this->render('CDPBookingBundle:Ticket:new.html.twig', array('form' => $form->createView()));
  }



  public function addAction(Request $request)
  {
      $session = $request->getSession();
      if(!$session->has('etape1')){
          return $this->redirectToRoute('cdp_booking_new');
      }
      $ticket = $session->get('etape1');
      $formVisitor = $this->get('form.factory')->create(TicketVisitorsType::class, $ticket);
      if ($request->isMethod('POST') && $formVisitor->handleRequest($request)->isValid()) {
        $session->set('etape2', $ticket);
        return $this->redirectToRoute('cdp_booking_resume');
      }
      return $this->render('CDPBookingBundle:Ticket:add.html.twig', array('form' => $formVisitor->createView(), 'ticket' => $ticket));
  }


  public function resumeAction(Request $request)
  {
      $session = $request->getSession();
      if(!$session->has('etape2')){
          return $this->redirectToRoute('cdp_booking_new');
      }
      $ticket = $session->get('etape2');


      $price = $this->get("cdp_booking.price");
      $totalPrice = $price->calcTotalPrice();
       $ticket->setPrixTotal($totalPrice);
      if($totalPrice ===0){
            $session->getFlashBag()->add('notice', 'Veuillez ajouter un adulte pour accompagner les enfants');
            return $this->redirectToRoute('cdp_booking_new');
      }
        $session->set('etape3', $ticket);
        return $this->render('CDPBookingBundle:Ticket:resume.html.twig', array('ticket' =>$ticket));
  }
    

  public function saveAction(Request $request)
  {

      $session = $request->getSession();
      if(!$session->has('etape3')){
          return $this->redirectToRoute('cdp_booking_new');
      }
      $stripePayment = $this->get("cdp_booking.stripepayment");
      $result = $stripePayment->payment();
      if($result === false)
      {
          $title = "Probleme lors du paiement";
          $msg = "Payement refusé, données bancaires non valide";
          return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('title' =>$title, 'msg' =>$msg, 'isOk'=> false));
          // The card has been declined
      }
      else {

          $validPayment = $this->get("cdp_booking.validpayment");
          // save in bdd end send mail
          $validPayment->valide();
          $title = "Confirmation de paiement";
          $msg = "Payement validé, Nous vous remercions pour votre commande";
          return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('title' => $title, 'msg' => $msg, 'isOk' => true));
      }

  }

}