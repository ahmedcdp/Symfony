<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 10/11/2017
 * Time: 18:20
 */

namespace CDP\BookingBundle\Services;

use CDP\BookingBundle\Entity\Ticket;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;


class StripePayment
{

    private $ticket;
    private $skey;


    public function __construct(Session $session, RequestStack $requestStack, $skey)
    {
        $this->session = $session;
        $this->ticket = $session->get('etape3');
        $this->requestStack = $requestStack;
        $this->skey = $skey;
    }

    public function payment()
    {
        //recuperation de la key
        \Stripe\Stripe::setApiKey($this->skey);

        // Get the credit card details submitted by the form
        $request = $this->requestStack->getCurrentRequest();
        if($request === null)
        {
            return false;
        }
        $token = $request->request->get('stripeToken');
        $prix = $this->ticket->getPrixTotal() * 100;

        // Create a charge: this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => $prix,
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - OpenClassrooms"
            ));
            return true;
        } catch(\Stripe\Error\Card $e) {
            return false;
            // The card has been declined
        }
    }

}