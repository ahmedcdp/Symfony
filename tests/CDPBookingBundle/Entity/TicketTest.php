<?php

namespace Tests\CDP\BookingBundle\Entity;

use CDP\BookingBundle\Entity\Visitor;
use CDP\BookingBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class TitcketTest extends TestCase
{
    public function testcalcPrixTotalValid()
    {
        //calcul le prix en fct de l'age du demi-tarif et du type de billet(demi-jour)
        $visitor = new visitor();
        $date = new \Datetime("1979-01-21");
        $visitor->setBirthdate($date);
        $visitor->setHalfprice(false);
        $ticket = new Ticket();
        $ticket->addVisitor($visitor);
        $ticket->setHalfday(false);
        $this->assertSame(16, $ticket->calcPrixTotal());
    }
}