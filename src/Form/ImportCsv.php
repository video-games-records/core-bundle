<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportCsv extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'csv',
            FileType::class,
            [
                'label' => 'CSV FILE',
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                'help' => "Exemple de fichier: <br />
groupe_en;groupe_fr;chart_en;chart_fr;type_id<br />
Total Time;Temps Total;Red Circuit;Circuit rouge;1<br />
Total Time;Temps Total;Green Circuit;Circuit vert;1<br />
Total Time;Temps Total;Blue Circuit;Circuit bleu;1<br />
Total Time;Temps Total;Black Circuit;Circuit noir;1<br />
Best Lap;Meilleur Tour;Red Circuit;Circuit rouge;2<br />
Best Lap;Meilleur Tour;Green Circuit;Circuit vert;2<br />
Best Lap;Meilleur Tour;Blue Circuit;Circuit bleu;2<br />
Best Lap;Meilleur Tour;Black Circuit;Circuit noir;2",
                'help_html' => true,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        /*'mimeTypes' => [
                            'text/csv',
                        ],*/
                        'mimeTypesMessage' => 'Please upload a valid CSV document',
                    ])
                ],
            ]
        )
            ->add('submit', SubmitType::class);
    }
}
