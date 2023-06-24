<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\DoctrineORMAdminBundle\Filter\NullFilter;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlayerAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_player';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('export')
            ->remove('delete');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'pseudo',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('status', null, ['label' => 'label.status'])
            ->add('boolMaj', CheckboxType::class, [
                'label' => 'Maj ?',
                'required' => false,
            ])
            ->add('team', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'label.country',
            ])
            ->add('country', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'label.country',
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('pseudo', null, ['label' => 'label.pseudo'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('twitch', NullFilter::class, ['label' => 'label.twitch_is_null'])
            ->add('youtube', NullFilter::class, ['label' => 'label.youtube_is_null'])
            ->add('website', NullFilter::class, ['label' => 'label.website_is_null'])
            ->add('boolMaj', null, ['label' => 'label.boolMaj']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('pseudo', null, ['label' => 'label.pseudo'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('twitch', null, ['label' => 'label.twitch'])
            ->add('youtube', null, ['label' => 'label.youtube'])
            ->add('slug', null, ['label' => 'label.slug'])
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
            ->with('Player', ['class' => 'col-md-6', 'label' => 'label.player'])
                ->add('id', null, ['label' => 'label.id'])
                ->add('pseudo', null, ['label' => 'label.pseudo'])
                ->add('status', null, ['label' => 'label.status'])
                ->add('country', null, ['label' => 'label.country'])
                ->add('team', null, ['label' => 'label.team'])
                ->add('website', null, ['label' => 'label.website'])
                ->add('youtube', null, ['label' => 'label.youtube'])
                ->add('twitch', null, ['label' => 'label.twitch'])
            ->end()
            ->with('Ranking', ['class' => 'col-md-6', 'label' => 'label.ranking'])
                ->add('rankPointChart', null, ['label' => 'label.rankPointChart'])
                ->add('rankPointGame', null, ['label' => 'label.rankPointGame'])
                ->add('rankMedal', null, ['label' => 'label.rankMedal'])
                ->add('rankProof', null, ['label' => 'label.rankProof'])
                ->add('rankBadge', null, ['label' => 'label.rankBadge'])
                ->add('rankCup', null, ['label' => 'label.rankCup'])
                ->add('rankCountry', null, ['label' => 'label.rankCountry'])
            ->end()
            ->with('Points', ['class' => 'col-md-6', 'label' => 'label.points'])
                ->add('pointChart', null, ['label' => 'label.pointChart'])
                ->add('pointGame', null, ['label' => 'label.pointGame'])
                ->add('pointBadge', null, ['label' => 'label.pointBadge'])
            ->end()
            ->with('stats', ['class' => 'col-md-6', 'label' => 'label.stats'])
                ->add('nbGame', null, ['label' => 'label.nbGame'])
                ->add('nbChart', null, ['label' => 'label.nbChart'])
                ->add('nbChartProven', null, ['label' => 'label.nbChartProven'])
                ->add('nbChartDisabled', null, ['label' => 'label.nbChartDisabled'])
            ->end()
            ->with('Medals', ['class' => 'col-md-6', 'label' => 'label.medals'])
                ->add('chartRank0', null, ['label' => 'label.chartRank0'])
                ->add('chartRank1', null, ['label' => 'label.chartRank1'])
                ->add('chartRank2', null, ['label' => 'label.chartRank2'])
                ->add('chartRank3', null, ['label' => 'label.chartRank3'])
            ->end()
            ->with('Cups', ['class' => 'col-md-6', 'label' => 'label.cups'])
                ->add('gameRank0', null, ['label' => 'label.gameRank0'])
                ->add('gameRank1', null, ['label' => 'label.gameRank1'])
                ->add('gameRank2', null, ['label' => 'label.gameRank2'])
                ->add('gameRank3', null, ['label' => 'label.gameRank3'])
            ->end();
    }
}
