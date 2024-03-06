<?php

// src/Form/AudiosType.php

namespace App\Form;

use App\Entity\Audios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\OptionsResolver\OptionsResolver;

class AudioType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('files', FileType::class, [
            'label' => 'Fichiers Audio',
            'multiple' => false, // Permettre le téléchargement de plusieurs fichiers
            'required' => true, // Assurez-vous que le champ est requis
            'constraints' => [
                new File([
                    'maxSize' => '1024M', // Taille maximale du fichier
                    'mimeTypes' => [
                        'audio/mpeg', // MP3
                        'audio/x-wav', // WAV
                        'audio/aiff', // AIFF
                        'audio/flac', // FLAC
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger un fichier audio valide (MP3, WAV, AIFF, FLAC, etc.)',
                    'maxSizeMessage' => 'Le fichier est trop volumineux. La taille maximale autorisée est {{ limit }} {{ suffix }}.',
                    'disallowEmptyMessage' => 'Veuillez télécharger un fichier audio.',
                    'extensions' => ['mp3'], // Extensions autorisées
                    'mimeTypesMessage' => 'Veuillez télécharger un fichier MP3 valide.',
                ]),
            ],
        ]);
}



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Audios::class,
        ]);
    }
}

