<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class DiscordAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_discord';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'discord.form.id',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'discord.form.name',
                'required' => true,
            ])
            ->add('url', UrlType::class, [
                'label' => 'discord.form.url',
                'required' => true,
            ])
            ->add('games', ModelType::class, [
                'class' => 'VideoGamesRecords\CoreBundle\Entity\Game',
                'property' => 'libGameEn',
                'multiple' => true,
                'required' => false,
                'label' => 'discord.form.games',
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'discord.filter.id'])
            ->add('name', null, ['label' => 'discord.filter.name'])
            ->add('url', null, ['label' => 'discord.filter.url'])
            ->add('games', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'libGameEn'],
                'label' => 'discord.filter.games',
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'discord.list.id'])
            ->add('name', null, ['label' => 'discord.list.name'])
            ->add('url', 'url', ['label' => 'discord.list.url'])
            ->add('games', null, [
                'associated_property' => 'libGameEn',
                'label' => 'discord.list.games',
            ])
            ->add('createdAt', null, ['label' => 'discord.list.createdAt'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'discord.show.id'])
            ->add('name', null, ['label' => 'discord.show.name'])
            ->add('url', 'url', ['label' => 'discord.show.url'])
            ->add('games', null, [
                'associated_property' => 'libGameEn',
                'label' => 'discord.show.games',
            ])
            ->add('createdAt', null, ['label' => 'discord.show.createdAt'])
            ->add('updatedAt', null, ['label' => 'discord.show.updatedAt']);
    }
}
