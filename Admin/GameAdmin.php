<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_game';

    /**
     * @return string
     */
    private function getLibGame(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
    }

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->add('copy', $this->getRouterIdParameter().'/copy')
            ->add('maj', $this->getRouterIdParameter().'/maj');
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_page'] = 1;
        $sortValues['_sort_order'] = 'DESC';
        $sortValues['_sort_by'] = 'id';
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('serie', ModelAutocompleteType::class, [
                'property' => 'libSerie',
                'label' => 'label.serie',
            ])
            ->add('libGameEn', TextType::class, [
                'label' => 'label.name.en',
                'required' => true,
            ])
            ->add('libGameFr', TextType::class, [
                'label' => 'label.name.fr',
                'required' => false,
            ])
            ->add('rules', null, ['required' => false, 'expanded' => false, 'label' => 'label.rules'])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.badge',
            ])
            ->add('forum', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.forum',
            ])
            ->add('picture', TextType::class, [
                'label' => 'label.picture',
                'required' => false,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => Game::getStatusChoices(),
                ]
            )
            ->add('publishedAt', DateType::class, [
                'label' => 'label.publishedAt',
                'required' => false,
                'years' => range(2004, date('Y'))
            ])
            ->add(
                'etat',
                ChoiceType::class,
                [
                    'label' => 'label.state',
                    'choices' => Game::getEtatsChoices(),
                ]
            )
            ->add('boolRanking', CheckboxType::class, [
                'label' => 'label.boolRanking',
                'required' => false,
            ])
            ->add(
                'platforms',
                null,
                [
                    'label' => 'label.platforms',
                    'required' => false,
                    'expanded' => false,
                    'query_builder' =>
                        function($er) {
                            $qb = $er->createQueryBuilder('p');
                            $qb->orderBy('p.libPlatform', 'ASC');
                            return $qb;
                        }
                ]
            )
            ->end()
            ->with('label.groups')
            ->add(
                'groups',
                CollectionType::class,
                array(
                    'label' => 'label.groups',
                    'by_reference' => false,
                    'type_options' => array(
                        // Prevents the "Delete" option from being displayed
                        'delete' => true,
                        'delete_options' => array(
                            // You may otherwise choose to put the field but hide it
                            'type' => CheckboxType::class,
                            // In that case, you need to fill in the options as well
                            'type_options' => array(
                                'mapped' => false,
                                'required' => false,
                            )
                        )
                    ),
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->end();
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('serie', null, ['label' => 'label.serie'])
            ->add('libGameEn', null, ['label' => 'label.name.en'])
            ->add('libGameFr', null, ['label' => 'label.name.fr'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('etat', null, ['label' => 'label.state'])
            ->add('boolRanking', null, ['label' => 'label.boolRanking']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $btns = [
            'maj' => [
                'template' => 'VideoGamesRecordsCoreBundle:Admin:game_maj_link.html.twig'
            ],
        ];
        if ($this->hasAccess('create')) {
            $btns = array_merge($btns, [
                'copy' => [
                    'template' => 'VideoGamesRecordsCoreBundle:Admin:game_copy_link.html.twig'
                ],
                'add_group' => [
                    'template' => 'VideoGamesRecordsCoreBundle:Admin:game_add_group_link.html.twig'
                ]
            ]);
        }

        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libGameEn', null, ['label' => 'label.game.en','editable' => true])
            ->add('libGameFr', null, ['label' => 'label.game.fr','editable' => true])
            //->add('slug', null, ['label' => 'label.slug'])
            ->add(
                'picture',
                'text',
                [
                    'label' => 'label.picture',
                    'editable' => true
                ]
            )
            ->add(
                'badge.picture',
                null,
                [
                    'label' => 'label.badge',
                    'editable' => true
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'label.status',
                    'editable' => true,
                    'choices' => Game::getStatusChoices(),
                ]
            )
            ->add(
                'etat',
                'choice',
                [
                    'label' => 'label.state',
                    'editable' => true,
                    'choices' => Game::getEtatsChoices(),
                ]
            )
            ->add('_action', 'actions', [
                'actions' =>
                    array_merge(
                        [
                            'show' => [],
                            'edit' => [],
                            'groups' => [
                                'template' => 'VideoGamesRecordsCoreBundle:Admin:game_groups_link.html.twig'
                            ]
                        ],
                        $btns
                    )
             ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'label.id'])
            ->add('libGameEn', null, ['label' => 'label.name.en'])
            ->add('libGameFr', null, ['label' => 'label.name.fr'])
            ->add('picture', null, ['label' => 'label.picture'])
            ->add('badge', null, ['label' => 'label.badge'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('etat', null, ['label' => 'label.state'])
            ->add('forum', null, ['label' => 'label.forum'])
            ->add('groups', null, ['label' => 'label.groups']);
    }
}
