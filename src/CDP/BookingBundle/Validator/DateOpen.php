<?php


namespace CDP\BookingBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class DateDispo extends Constraint

{
  public $message = "Plus de place disponible pour cette date";
}