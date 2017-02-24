<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PlatformAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export');
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('idPlatform', 'text', array(
                'label' => 'idPlatform',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('libPlatform', 'text', array(
                'label' => 'Name',
                'required' => true,
            ))
            ->add('picture', 'text', array(
                'label' => 'Picture',
                'required' => true,
            ))
            ->add(
                'status',
                ChoiceType::class,
                array(
                    'label' => 'Status',
                    'choices' => array(
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    )
                )
            )
            ->add('class', 'text', array(
                'label' => 'Class',
                'required' => true,
            ));
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libPlatform');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idPlatform')
            ->add('libPlatform', null, array('editable' => false))
            ->add(
                'picture',
                'text',
                array(
                    'label' => 'Picture',
                    'editable' => true
                )
            )
            ->add(
                'status',
                'choice',
                array(
                    'label' => 'Status',
                    'editable' => false,
                    'choices' => array(
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    )
                )
            )
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ));
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('idPlatform')
            ->add('libPlatform')
            ->add('picture')
            ->add('status')
            ->add('class');
    }
}
