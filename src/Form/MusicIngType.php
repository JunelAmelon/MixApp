<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MusicIngType extends AbstractType
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         ->add('projetId', HiddenType::class)
         ->add('files', FileType::class, [
            'label' => 'Fichiers Audio',
            'multiple' => false,
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '1024M',
                    'mimeTypes' => [
                        'audio/mpeg',
                        'audio/x-wav',
                        'audio/aiff',
                        'audio/flac',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger un fichier audio valide (MP3, WAV, AIFF, FLAC, etc.)',
                    'maxSizeMessage' => 'Le fichier est trop volumineux. La taille maximale autorisée est {{ limit }} {{ suffix }}.',
                    'disallowEmptyMessage' => 'Veuillez télécharger un fichier audio.',
                    'extensions' => ['mp3'],
                    'extensionsMessage' => 'Veuillez télécharger un fichier MP3 valide.',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
