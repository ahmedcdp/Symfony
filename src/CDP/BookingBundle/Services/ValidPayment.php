<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 10/11/2017
 * Time: 16:43
 */
namespace CDP\BookingBundle\Services;

use CDP\BookingBundle\Entity\Ticket;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;


class ValidPayment
{
    private $ticket;
    private $em;

    public function __construct(Session $session, EntityManagerInterface $em, $mailer_user, \Swift_Mailer $mailer,$templating)
    {
        $this->session = $session;
        $this->ticket = $session->get('etape3');
        $this->em = $em;
        $this->mailerUser = $mailer_user;
        $this->mailer    = $mailer;
        $this->templating = $templating;
    }

    public function save()
    {
        $this->ticket->generatedTicketId();
        //sauvegarde en bdd
        $this->em->persist($this->ticket);
        $this->em->flush();
        $this->session->clear();
    }

    public function sendMail()
    {
        $emailFrom = $this->mailerUser;
        $emailTo = $this->ticket->getEmail();
        $message = \Swift_Message::newInstance()
            ->setSubject('Votre billet')
            ->setFrom($emailFrom)
            ->setTo($emailTo);
        $cid = $message->embed(\Swift_Image::fromPath('images/logo-louvre.jpg'));
        $message->setBody($this->templating->render('CDPBookingBundle:Emails:email.html.twig', array('ticket' => $this->ticket, 'cid'=>$cid)), 'text/html');
        $this->mailer->send($message);
    }
    public function valide()
    {
        $this->save();
        $this->sendMail();
    }
}