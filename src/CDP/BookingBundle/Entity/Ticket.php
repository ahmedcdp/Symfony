<?php

namespace CDP\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ticket
 *
 * @ORM\Table(name="Ticket")
 * @ORM\Entity(repositoryClass="CDP\BookingBundle\Repository\TicketRepository")
 */
class Ticket
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
     /*
      @Assert\GreaterThanOrEqual("today")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="halfday", type="boolean")
     */
    private $halfday = false;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="smallint")
     */
    private $number=0;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
    * @ORM\ManyToMany(targetEntity="CDP\BookingBundle\Entity\Visitor", cascade={"persist"})
    */
    private $visitors;


    public function __construct()
    {
        $this->date = new \Datetime();
        $this->visitors = new ArrayCollection();
    }

    /**
    * @param Visitor $visitor
    */
    public function addVisitor(Visitor $visitor)
    {
        $this->visitors[] = $visitor;
    }

    /**
    * @param Visitor $visitor
    */
    public function removeVisitor(Visitor $visitor)
    {
        $this->visitors->removeElement($visitor);
    }

    /**
    * @return ArrayCollection
    */
    public function getVisitors()
    {
        return $this->visitors;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ticket
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set halfday
     *
     * @param boolean $halfday
     *
     * @return ticket
     */
    public function setHalfday($halfday)
    {
        $this->halfday = $halfday;

        return $this;
    }

    /**
     * Get halfday
     *
     * @return bool
     */
    public function getHalfday()
    {
        return $this->halfday;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return ticket
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ticket
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}

