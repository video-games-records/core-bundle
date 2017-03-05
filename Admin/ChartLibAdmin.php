<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class ChartLibAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('idLibChart', 'text', [
                'label' => 'idLibChart',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', 'text', [
                'label' => 'Name',
                'required' => true,
            ])
            //->add('chart', null, array('required' => true))
            ->add('type', null, ['required' => true]);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idLibChart')
            ->add('name')
            ->add('type');
    }
}
