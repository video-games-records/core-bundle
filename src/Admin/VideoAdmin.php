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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'label' => 'video.form.id',
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
                'label' => 'video.form.player',
            ])
            ->add('game', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'video.form.game',
            ])
            ->add('title', TextType::class, [
                'label' => 'video.form.name',
                'required' => true,
            ])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'video.form.type',
                    'choices' => VideoType::getTypeChoices(),
                    'choice_translation_domain' => false,
                ]
            )
            ->add('url', TextType::class, [
                'label' => 'video.form.url',
                'required' => true,
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'video.form.isActive',
                'required' => false,
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'video.filter.id'])
            ->add('isActive', null, ['label' => 'video.filter.isActive'])
            ->add('type', null, ['label' => 'video.filter.type'])
            ->add('player', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'video.filter.player',
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'video.list.id'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'video.list.player',
            ])
            ->add('game', null, [
                'associated_property' => 'libGameEn',
                'label' => 'video.list.game',
            ])
            ->add('title', null, ['label' => 'video.list.title'])
            ->add('type', null, ['label' => 'video.list.type'])
            ->add('externalId', null, ['label' => 'video.list.videoId'])
            ->add(
                'isActive',
                'boolean',
                [
                    'label' => 'video.list.isActive',
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
        $show->add('id', null, ['label' => 'video.show.id'])
            ->add('isActive', null, ['label' => 'video.show.isActive'])
            ->add('player', null, ['label' => 'video.show.player'])
            ->add('game', null, ['label' => 'video.show.game'])
            ->add('url', null, ['label' => 'video.show.url'])
            ->add('nbComment', null, ['label' => 'video.show.nbComment'])

            ->add('title', null, ['label' => 'video.show.title'])
            ->add('viewCount', null, ['label' => 'video.show.viewCount'])
            ->add('likeCount', null, ['label' => 'video.show.likeCount'])
            ->add('thumbnail', null, ['label' => 'video.show.thumbnail']);
    }
}
