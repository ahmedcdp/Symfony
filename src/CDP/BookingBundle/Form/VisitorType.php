<?php

namespace CDP\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class VisitorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastname', TextType::class, array("label" => "Nom"))
        ->add('firstname', TextType::class, array("label" => "Prenom"))
        ->add('birthdate', BirthdayType::class, array("label" => "Date de naissance", 'format' => 'dd MM yyyy', 'data' =>new \Datetime()))
        ->add('country', CountryType::class, array("label" => "Pays",  "preferred_choices" => array("FR")))
        ->add('halfprice', CheckboxType::class, array("label" => "Demi-tarif (pièces justificatives à présenter le jour de la visite)", 'required' => false));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CDP\BookingBundle\Entity\Visitor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cdp_bookingbundle_visitor';
    }


}
