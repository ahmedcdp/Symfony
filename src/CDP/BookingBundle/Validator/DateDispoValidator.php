<?php

namespace CDP\BookingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;


// declarer en tant que service pour avoir acces a la bdd
class DateDispoValidator extends ConstraintValidator
{
  	private $requestStack;
  	private $em;
  	private $maxBillet;

  	public function __construct(RequestStack $requestStack, EntityManagerInterface $em, $maxBillets)
  	{
    	$this->requestStack = $requestStack;
    	$this->em = $em;
    	$this->maxBillet = $maxBillets;
  	}

  	public function validate($value, Constraint $constraint)
  	{

		// on interroge la bdd pour savoir si il reste des billets pour cette date
    	$repository = $this->em->getRepository('CDPBookingBundle:Ticket');

 		$nbBillets = $repository->countByDate($value);
        $ticket = $this->context->getRoot()->getData();
        $nbBilletDesire = $ticket->getNumber();
 		$nbBilletDispo = $this->maxBillet - $nbBillets;
 		if($nbBilletDesire > $nbBilletDispo )
    	{
            $nb_errors = $this->context->getViolations()->count();

            if ($nb_errors === 0) {
                $this->context->addViolation($constraint->message);
            }
    	}
	}
}