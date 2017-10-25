<?php


namespace CDP\BookingBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class DateDispo extends Constraint

{
  public $message = "Plus de place disponible pour cette date";

  public function validatedBy()
  {
    return 'cdp_booking_datedispo'; //on fait appel à l'alias du service
  }
}