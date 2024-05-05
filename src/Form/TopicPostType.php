<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use App\Form\TopicType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('topic', TopicType::class, [
                'label' => false,
            ])
            ->add('post', PostType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'topic' => new Topic(),
            'post' => new Post(),
        ]);
    }
}
