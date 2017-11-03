<?php

namespace Tests\CDP\BookingBundle\Entity;

use CDP\BookingBundle\Entity\Visitor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Date;

class VisitorTest extends TestCase
{
    public function testcalcAgeValid()
    {
        $visitor = new visitor();
        $date = new \Datetime("1979-01-21");
        $visitor->setBirthdate($date);
        $this->assertSame("38", $visitor->calcAge());
    }
}
