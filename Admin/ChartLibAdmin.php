<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChartLibAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('idLibChart', TextType::class, [
                'label' => 'idLibChart',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add(
                'type',
                null,
                [
                    'required' => true,
                    'query_builder' =>
                        function($er) {
                            $qb = $er->createQueryBuilder('p');
                            $qb->orderBy('p.name', 'ASC');
                            return $qb;
                        }
                ]
            );
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('idLibChart')
            ->add('name')
            ->add('type');
    }
}
