<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;

class PlayerChartLibAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $form): void
    {
        /** @var PlayerChartLib $playerChartLib */
        $playerChartLib = $this->getSubject();
        $chart = $playerChartLib->getPlayerChart()->getChart();

        $form
            ->add('id', TextType::class, [
                'label' => 'id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add(
                'libChart',
                null,
                [
                    'required' => true,
                    'query_builder' =>
                        function ($er) use ($chart) {
                            $qb = $er->createQueryBuilder('l');
                            $qb->where('l.chart = :chart');
                            $qb->setParameter('chart', $chart);
                            return $qb;
                        }
                ]
            )
            ->add('value', TextType::class, [
                'label' => 'Value',
                'required' => true,
            ]);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('value');
    }
}
