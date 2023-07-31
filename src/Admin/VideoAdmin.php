<?php

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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\ValueObject\VideoType;

class VideoAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_video';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->add('maj', $this->getRouterIdParameter() . '/maj');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'label.id',
                'required' => false,
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('player', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.player',
            ])
            ->add('game', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.game',
            ])
            ->add('libVideo', TextType::class, [
                'label' => 'label.name',
                'required' => true,
            ])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'label.type',
                    'choices' => VideoType::getTypeChoices(),
                    'choice_translation_domain' => false,
                ]
            )
            ->add('videoId', TextType::class, [
                'label' => 'label.video.id',
                'required' => true,
            ])
            ->add('url', TextType::class, [
                'label' => 'label.url',
                'required' => true,
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'label.isActive',
                'required' => false,
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('isActive', null, ['label' => 'label.isActive'])
            ->add('type', null, ['label' => 'label.type'])
            ->add('player', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'label.player',
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'label.player',
            ])
            ->add('game', null, [
                'associated_property' => 'libGameEn',
                'label' => 'label.game',
            ])
            ->add('libVideo', null, ['label' => 'label.name'])
            ->add('title', null, ['label' => 'label.title'])
            ->add('type', null, ['label' => 'label.type'])
            ->add('videoId', null, ['label' => 'label.video.id'])
            ->add(
                'isActive',
                'boolean',
                [
                    'label' => 'label.isActive',
                    'editable' => true,
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'maj' => [
                        'template' => '@VideoGamesRecordsCore/Admin/object_maj_link.html.twig'
                    ],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id', null, ['label' => 'label.id'])
            ->add('isActive', null, ['label' => 'label.isActive'])
            ->add('libVideo', null, ['label' => 'label.name'])
            ->add('player', null, ['label' => 'label.player'])
            ->add('game', null, ['label' => 'label.game'])
            ->add('url', null, ['label' => 'label.url'])
            ->add('nbComment', null, ['label' => 'label.nbComment'])

            ->add('title', null, ['label' => 'label.title'])
            ->add('viewCount', null, ['label' => 'label.viewCount'])
            ->add('likeCount', null, ['label' => 'label.likeCount'])
            ->add('thumbnail', null, ['label' => 'label.thumbnail']);
    }
}
