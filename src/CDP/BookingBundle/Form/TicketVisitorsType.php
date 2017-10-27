<?php

namespace CDP\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TicketVisitorsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('visitors', CollectionType::class, array(
                'entry_type'=> VisitorType::class,
                "label" => "Visiteurs",
                'allow_add' =>true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false))
                ->remove('date')
                ->remove('halfday')
                ->remove('number')
                ->remove('email');
    }

    /**
     * {@inheritdoc}
     */
    public function  getParent()
    {
        return TicketType::class;
    }

}
