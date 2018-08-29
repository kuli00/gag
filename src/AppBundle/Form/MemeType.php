<?php
/**
 * Created by PhpStorm.
 * User: kulin
 * Date: 26.08.2018
 * Time: 22:10
 */

namespace AppBundle\Form;


use AppBundle\Entity\Meme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemeType extends AbstractType
{
    public function buildForm(formBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class, ["label" => "Tytuł"])
            ->add("image", FileType::class, ["label" => "Obrazek"])
            ->add("submit", SubmitType::class, ["label" => "Dodaj"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => Meme::class]);
    }
}