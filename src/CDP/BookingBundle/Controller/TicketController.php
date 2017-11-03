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

/*
  public function resumeAction(Request $request)
  {
      $session = $request->getSession();
      if(!$session->has('etape2')){
          return $this->redirectToRoute('cdp_booking_new');
      }
      $ticket = $session->get('etape2');

      $ticket->calcPrixTotal();
      $prix = $ticket->getPrixTotal();
      if($prix ===0){
            $session->getFlashBag()->add('notice', 'Veuillez ajouter un adulte pour accompagner les enfants');
            return $this->redirectToRoute('cdp_booking_new');
      }
        $session->set('etape3', $ticket);
        return $this->render('CDPBookingBundle:Ticket:resume.html.twig', array('ticket' =>$ticket));
  }

*/
    public function resumeAction(Request $request)
    {
        $session = $request->getSession();
        if (!$session->has('etape2')) {
            return $this->redirectToRoute('cdp_booking_new');
        }
        $ticket = $session->get('etape2');

        $ticket->calcPrixTotal();
        $prix = $ticket->getPrixTotal();
        if ($prix === 0) {
            $session->getFlashBag()->add('notice', 'Veuillez ajouter un adulte pour accompagner les enfants');
            return $this->redirectToRoute('cdp_booking_new');
        }
        $session->set('etape3', $ticket);
        $ticket->generatedTicketId();

        $message = \Swift_Message::newInstance()
            ->setSubject('Some Subject')
            ->setFrom('admin@cdpdev.fr')
            ->setTo('ahmedsim.amk@gmail.com');
        $cid = $message->embed(\Swift_Image::fromPath('images/logo-louvre.jpg'));
        $message->setBody($this->renderView('CDPBookingBundle:Emails:email.html.twig', array('ticket' => $ticket, 'cid'=>$cid)), 'text/html');
        $this->get('mailer')->send($message);
        $title = "Confirmation de paiement";
        $msg = "Payement validé, Nous vous remercions pour votre commande";
        return $this->redirectToRoute('cdp_booking_new');
      //  return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('title' => $title, 'msg' => $msg, 'isOk' => 'true'));
    }




  public function saveAction(Request $request)
  {

      $session = $request->getSession();
      if(!$session->has('etape3')){
          return $this->redirectToRoute('cdp_booking_new');
      }
      $ticket = $session->get('etape3');

      //recuperation de la key
      \Stripe\Stripe::setApiKey("sk_test_goZbtaGRcbkF1Zx3gXkNX4XF");

      // Get the credit card details submitted by the form
      $token = $_POST['stripeToken'];
      $prix = $ticket->getPrixTotal() * 100;

      // Create a charge: this will charge the user's card
      try {
          $charge = \Stripe\Charge::create(array(
              "amount" => $prix,
              "currency" => "eur",
              "source" => $token,
              "description" => "Paiement Stripe - OpenClassrooms"
          ));
          $title = "Confirmation de paiement";
          $msg = "Payement validé, Nous vous remercions pour votre commande";
      } catch(\Stripe\Error\Card $e) {
          $title = "Probleme lors du paiement";
          $msg = "Payement refusé, données bancaires non valide";
          return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('title' =>$title, 'msg' =>$msg, 'isOk'=> 'false'));
          // The card has been declined
      }



      $ticket->generatedTicketId();
      //sauvegarde en bdd
      $em = $this->getDoctrine()->getManager();
      $em->persist($ticket);
      $em->flush();
      $session->clear();
      $emailFrom = $this->container->getParameter('mailer_user');
      $emailTo = $ticket->getEmail();
      $message = \Swift_Message::newInstance()
          ->setSubject('Votre billet')
          ->setFrom($emailFrom)
          ->setTo($emailTo);
      $cid = $message->embed(\Swift_Image::fromPath('images/logo-louvre.jpg'));
      $message->setBody($this->renderView('CDPBookingBundle:Emails:email.html.twig', array('ticket' => $ticket, 'cid'=>$cid)), 'text/html');
      $this->get('mailer')->send($message);
      return $this->render('CDPBookingBundle:Ticket:save.html.twig', array('title' =>$title, 'msg' =>$msg, 'isOk'=>'true'));

  }

}