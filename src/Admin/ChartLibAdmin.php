<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChartLibAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('idLibChart', TextType::class, [
                'label' => 'label.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'required' => false,
            ])
            ->add(
                'type',
                null,
                [
                    'label' => 'label.type',
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
            ->addIdentifier('idLibChart', null, ['label' => 'label.id'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('type', null, ['label' => 'label.type']);
    }
}
