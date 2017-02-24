<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SerieAdmin extends AbstractAdmin
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
            ->add('idSerie', 'text', array(
                'label' => 'idSerie',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('libSerie', 'text', array(
                'label' => 'Name'
            ));
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libSerie');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idSerie')
            ->add('libSerie');
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('idSerie')
            ->add('libSerie');
    }
}
