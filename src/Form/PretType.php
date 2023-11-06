<?php

namespace App\Form;

use App\Entity\Pret;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PretType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'placeholder' => 'Choisir une princesse',
                // On peut se choisir soi-même (= s'auto-réserver le truc)
                //'query_builder' => fn($repo) => $repo->createQueryBuilder('u')
                //    ->andWhere('u.id != :user')
                //    ->setParameter('user', $this->security->getUser()),
            ])
            ->add('dateDebut', null, [
                'widget' => 'single_text',
                'disabled' => true,
            ])
            ->add('commentaire')
            ->add('dateFin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pret::class,
        ]);
    }
}
