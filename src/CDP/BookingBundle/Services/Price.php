<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 08/11/2017
 * Time: 22:36
 */

namespace CDP\BookingBundle\Services;
use CDP\BookingBundle\Entity\Ticket;
use CDP\BookingBundle\Entity\Visitor;
use Symfony\Component\HttpFoundation\Session\Session;

class Price
{
    private $ticket;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->ticket = $session->get('etape2');
    }

    public function calcAge(Visitor $visitor)
    {
        date_default_timezone_set('Europe/Paris');
        $date = new \Datetime();
        $dateAge = $date->diff($visitor->getBirthdate());
        $age = $dateAge->format('%y');
        return $age;
    }

    //calcul du prix total des billets
    public function calcTotalPrice()
    {
        $totalPrice=0;
        $visitors = $this->ticket->getVisitors();
        foreach ($visitors as $visitor)
        {
            $age = $this->calcAge($visitor);


            if($age<4){$price = 0;}
            else if($age>=4 && $age<12){$price = 8;}
            else{
                if($visitor->getHalfprice() === true){$price=10;}
                else{
                    if($age>=12 && $age<=60){$price = 16;}
                    else if($age>60){$price = 12;}
                }
            }
            if($this->ticket->getHalfday() === true){$price = $price/2;}
            $totalPrice+=$price;
        }
        return $totalPrice;

    }

}