<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author',HiddenType::class)
            ->add('authorId',HiddenType::class)
            ->add('content', TextType::class, ['label' => ' '])
            // ->add('createdAt')
            // ->add('article')
            // ->add('user',  EntityType::class, [
            //     'class'=> User::class,
            //     'choice_label' => 'username'
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
