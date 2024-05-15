<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;

class GameDayAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_game_day';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_page'] = 1;
        $sortValues['_sort_order'] = 'DESC';
        $sortValues['_sort_by'] = 'day';
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $options = ['label' => 'label.day'];
        if (($this->hasRequest()) && ($this->isCurrentRoute('create'))) {
            $em = $this->getModelManager()
                ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\GameDay');
            $lastDay = $em->getRepository('VideoGamesRecords\CoreBundle\Entity\GameDay')->getMaxDay();
            $date = new \DateTime($lastDay);
            $date->add(new \DateInterval('P1D'));
            $options['data'] = $date;
        }

        $form
            ->add(
                'game',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => true,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => true,
                    'label' => 'label.game',
                ]
            )
            ->add('day', null, $options);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('day', null, ['label' => 'label.day'])
            ->add(
                'game',
                ModelFilter::class,
                [
                    'label' => 'label.game',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => ['property' => 'libGameEn'],
                ]
            );
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('day', null, ['label' => 'label.day'])
            ->add('game', null, [
                'associated_property' => 'libGameEn',
                'label' => 'Game',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'label.id'])
            ->add('day', null, ['label' => 'label.day'])
            ->add('game', null, ['label' => 'label.game']);
    }
}
