<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 12/11/2017
 * Time: 14:07
 */

namespace Tests\CDP\BookingBundle\Services;

use CDP\BookingBundle\Entity\Visitor;
use CDP\BookingBundle\Entity\Ticket;
use CDP\BookingBundle\Services\Price;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;


class PriceTest extends TestCase
{
    private $session;

    public function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
    }

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
        $this->session->set('etape2', $ticket);
        $price = new Price($this->session);
        $this->assertSame(16, $price->calcTotalPrice());
    }

    public function testcalcAgeValid()
    {
        $visitor = new visitor();
        $date = new \Datetime("1979-01-21");
        $visitor->setBirthdate($date);
        $price = new Price($this->session);
        $this->assertSame("38", $price->calcAge($visitor));
    }
}