<?php

namespace CDP\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use CDP\BookingBundle\Validator\DateDispo;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @Assert\GreaterThanOrEqual("today", message = "Veuillez entrer une date valide")
     * @DateDispo()
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
     * @Assert\Range(
     *      min = 1,
     *      max = 1000,
     *      minMessage = "Vous devez selectionner au moins un billet",
     *      maxMessage = "Max 1000 billets"
     * )
     */
    private $number=0;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Length(max=255, maxMessage = "Max 255 caracteres")
     * @Assert\Email(message = "Veuillez entrer une adresse email valide")
     */
    private $email;

    /**
    * @ORM\ManyToMany(targetEntity="CDP\BookingBundle\Entity\Visitor", cascade={"persist"})
     * @Assert\Valid
     */
    private $visitors;

    /**
     * @var int
     *
     * @ORM\Column(name="prix", type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 1000000)
     */
    private $prixTotal=0;

    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
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

    //calcul du prix total des billets
    public function calcPrixTotal()
    {
        foreach ($this->visitors as $visitor)
        {
            $age = $visitor->getAge();

            
            if($age<4){$prix = 0;}
            else if($age>=4 && $age<12){$prix = 8;}
            else{
                if($visitor->getHalfprice() === true){$prix=10;}
                else{
                    if($age>=12 && $age<=60){$prix = 16;}
                    else if($age>60){$prix = 12;}
                }
            }
            if($this->halfday === true){$prix = $prix/2;}
            $this->prixTotal+=$prix;
        }
    
    }
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;
    }


    /**
    * @Assert\Callback
    */
    public function isDateValid(ExecutionContextInterface $context) 
    {
        date_default_timezone_set('Europe/Paris');
        
         //verifie si jour ouvert (ferme les mardis, 01/05, 01/11, 25/12)       
        $sDate=date_format($this->date, 'd-m-Y');
        $tDate = explode('-', $sDate);
        $currentDate = date("d-m-Y");
        $heure = date("H");
        $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

        //date('m'),date('d'),date('Y')
        $day = $days[date('w', mktime(0, 0, 0, $tDate[1], $tDate[0], $tDate[2]))];
        if( $day === "Tuesday" )
        {
            $resultat = "tuesday";
        }
        else if( ( ($tDate[0]==='01') && ( ($tDate[1]==='05') || ($tDate[1]==='11') ) ) || ( ($tDate[0]==='25') && ($tDate[1]==='12') ) )
        {
            $resultat = "holiday";
        }
        else if(($sDate === $currentDate ) && ($heure > 18)){
            $resultat = "close";
        }
        else
        {
            $resultat = "ok";
        }

        if ($resultat != "ok" )
        {
            $context->buildViolation('Musée fermée')->atPath('date')->addViolation();
        }

        // test si il est plus de 14h pour un billet commande pour le meme jour en option pleine journee
        if (($resultat === "ok") && ($sDate === $currentDate ) && ($heure >= 14) && !$this->getHalfday()) {
            $context->buildViolation('Il est plus de 14h veuillez sélectionner demi-journée')->atPath('halfday')->addViolation();
        }
    }
}

