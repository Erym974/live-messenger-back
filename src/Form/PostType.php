<?php

namespace App\Form;

use App\Entity\Post;
use Ehyiah\QuillJsBundle\DTO\Fields\BlockField\HeaderField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\BoldInlineField;
use Ehyiah\QuillJsBundle\DTO\Fields\InlineField\ItalicInlineField;
use Ehyiah\QuillJsBundle\DTO\QuillGroup;
use Ehyiah\QuillJsBundle\Form\QuillType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('short_description')
            ->add('content', QuillType::class, [
                // 'quill_extra_options' => [
                //     'height' => '780px',
                //     'theme' => 'snow',
                //     'placeholder' => 'Hello Quill WYSIWYG',
                // ],
                // 'quill_options' => [
                //     QuillGroup::build(
                //         new BoldInlineField(),
                //         new ItalicInlineField(),
                //     ),
                //     QuillGroup::build(
                //         new HeaderField(HeaderField::HEADER_OPTION_1),
                //         new HeaderField(HeaderField::HEADER_OPTION_2),
                //     )
                // ]
            ])
            // ->add('File')
            ->add('image', FileType::class, [
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
