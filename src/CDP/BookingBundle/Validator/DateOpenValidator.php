<?php

namespace CDP\BookingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateDispoValidator extends ConstraintValidator
{

  public function validate($value, Constraint $constraint)
  {

  	//on verifie si jour ouvert (ferme les mardis, 01/05, 01/11, 25/12)
    date_default_timezone_set('Europe/Paris');
    	
    //verifie si jour ouvert (ferme les mardis, 01/05, 01/11, 25/12)       
	$sDate=date_format($value, 'd-m-Y');
	$tDate = explode('-', $sDate);
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
	else
	{
		$resultat = "ok";
	}

	if ($resultat != "ok" )
	{
      $this->context->addViolation($constraint->message);
    }
  }

}