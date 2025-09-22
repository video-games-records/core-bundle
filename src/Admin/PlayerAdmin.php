<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\NullFilter;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlayerAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_player';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('create')
            ->remove('export')
            ->remove('delete');
        $collection
            ->add('maj', $this->getRouterIdParameter() . '/maj');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'player.form.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'player.form.pseudo',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('status', null, ['label' => 'player.form.status'])
            ->add('boolMaj', CheckboxType::class, [
                'label' => 'player.form.boolMaj',
                'required' => false,
            ])
            ->add('hasDonate', CheckboxType::class, [
                'label' => 'player.form.hasDonate',
                'required' => false,
            ])
            ->add('team', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'player.form.team',
            ])
            ->add('country', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'player.form.country',
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'player.filter.id'])
            ->add('pseudo', null, ['label' => 'player.filter.pseudo'])
            ->add('status', null, ['label' => 'player.filter.status'])
            ->add('twitch', NullFilter::class, ['label' => 'player.filter.twitch'])
            ->add('youtube', NullFilter::class, ['label' => 'player.filter.youtube'])
            ->add('website', NullFilter::class, ['label' => 'player.filter.website'])
            ->add('boolMaj', null, ['label' => 'player.filter.boolMaj']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'player.list.id'])
            ->add('pseudo', null, ['label' => 'player.list.pseudo'])
            ->add('status', null, ['label' => 'player.list.status'])
            ->add('twitch', null, ['label' => 'player.list.twitch'])
            ->add('youtube', null, ['label' => 'player.list.youtube'])
            ->add('slug', null, ['label' => 'player.list.slug'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'maj' => [
                        'template' => '@VideoGamesRecordsCore/Admin/object_maj_link.html.twig'
                    ]
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Player', ['class' => 'col-md-6', 'label' => 'player.sections.player'])
                ->add('id', null, ['label' => 'player.show.id'])
                ->add('pseudo', null, ['label' => 'player.show.pseudo'])
                ->add('status', null, ['label' => 'player.show.status'])
                ->add('country', null, ['label' => 'player.show.country'])
                ->add('team', null, ['label' => 'player.show.team'])
                ->add('website', null, ['label' => 'player.show.website'])
                ->add('youtube', null, ['label' => 'player.show.youtube'])
                ->add('twitch', null, ['label' => 'player.show.twitch'])
                ->add('lastDisplayLostPosition', null, ['label' => 'player.show.lastDisplayLostPosition'])
            ->end()
            ->with('Ranking', ['class' => 'col-md-6', 'label' => 'player.sections.ranking'])
                ->add('rankPointChart', null, ['label' => 'player.show.rankPointChart'])
                ->add('rankPointGame', null, ['label' => 'player.show.rankPointGame'])
                ->add('rankMedal', null, ['label' => 'player.show.rankMedal'])
                ->add('rankProof', null, ['label' => 'player.show.rankProof'])
                ->add('rankBadge', null, ['label' => 'player.show.rankBadge'])
                ->add('rankCup', null, ['label' => 'player.show.rankCup'])
                ->add('rankCountry', null, ['label' => 'player.show.rankCountry'])
            ->end()
            ->with('Points', ['class' => 'col-md-6', 'label' => 'player.sections.points'])
                ->add('pointChart', null, ['label' => 'player.show.pointChart'])
                ->add('pointGame', null, ['label' => 'player.show.pointGame'])
                ->add('pointBadge', null, ['label' => 'player.show.pointBadge'])
            ->end()
            ->with('stats', ['class' => 'col-md-6', 'label' => 'player.sections.stats'])
                ->add('nbGame', null, ['label' => 'player.show.nbGame'])
                ->add('nbChart', null, ['label' => 'player.show.nbChart'])
                ->add('nbChartProven', null, ['label' => 'player.show.nbChartProven'])
                ->add('nbChartDisabled', null, ['label' => 'player.show.nbChartDisabled'])
            ->end()
            ->with('Medals', ['class' => 'col-md-6', 'label' => 'player.sections.medals'])
                ->add('chartRank0', null, ['label' => 'player.show.chartRank0'])
                ->add('chartRank1', null, ['label' => 'player.show.chartRank1'])
                ->add('chartRank2', null, ['label' => 'player.show.chartRank2'])
                ->add('chartRank3', null, ['label' => 'player.show.chartRank3'])
            ->end()
            ->with('Cups', ['class' => 'col-md-6', 'label' => 'player.sections.cups'])
                ->add('gameRank0', null, ['label' => 'player.show.gameRank0'])
                ->add('gameRank1', null, ['label' => 'player.show.gameRank1'])
                ->add('gameRank2', null, ['label' => 'player.show.gameRank2'])
                ->add('gameRank3', null, ['label' => 'player.show.gameRank3'])
            ->end();
    }
}
