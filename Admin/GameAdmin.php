<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_game';

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
            ->add('serie', 'sonata_type_model_list', [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => true,
                'btn_catalogue' => true,
                'label' => 'Serie',
            ])
            ->add('badge', 'sonata_type_model_list', [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Badge',
            ])
            ->add('picture', 'text', [
                'label' => 'Picture',
                'required' => false,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => Game::getStatusChoices(),
                ]
            )
            ->add(
                'etat',
                ChoiceType::class,
                [
                    'label' => 'Etat',
                    'choices' => Game::getEtatsChoices(),
                ]
            )
            ->add('translations', TranslationsType::class, [
                'required' => true,
            ])
            ->add('platforms', null, ['required' => false, 'expanded' => false]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('translations.name')
            ->add('status')
            ->add('etat');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('getName', null, ['label' => 'Name'])
            ->add(
                'picture',
                'text',
                [
                    'label' => 'Picture',
                    'editable' => true
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'editable' => true,
                    'choices' => Game::getStatusChoices(),
                ]
            )
            ->add(
                'etat',
                'choice',
                [
                    'label' => 'Etat',
                    'editable' => false,
                    'choices' => Game::getEtatsChoices(),
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'groups' => [
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:game_groups_link.html.twig'
                    ],
                    'add_group' => [
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:game_add_group_link.html.twig'
                    ],
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('getName', null, ['label' => 'Name'])
            ->add('status')
            ->add('etat')
            ->add('groups');
    }
}
