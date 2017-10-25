<?php

namespace CDP\BookingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


// declarer en tant que service pour avoir acces a la bdd
class DateDispoValidator extends ConstraintValidator
{
  	private $requestStack;
  	private $em;

  	public function __construct(RequestStack $requestStack, EntityManagerInterface $em, ContainerInterface $container)
  	{
    	$this->requestStack = $requestStack;
    	$this->em = $em;
    	$this->container = $container;
  	}

  	public function validate($value, Constraint $constraint)
  	{

		// on interroge la bdd pour savoir si il reste des billets pour cette date
    	$maxBillets = $this->container->getParameter('max-billets');
    	$repository = $this->em->getRepository('CDPBookingBundle:Ticket');

 		$nbBillets = $repository->countByDate($value);
 		$nbBilletDispo = $maxBillets - $nbBillets;
 		if($nbBilletDispo <= 0)
    	{
       		$this->context->addViolation($constraint->message);
    	}
	}
}