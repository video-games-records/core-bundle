<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use VideoGamesRecords\CoreBundle\Form\Type\RichTextEditorType;

class RuleAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_rule';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $query->leftJoin($query->getRootAliases()[0] . '.translations', 't')
            ->addSelect('t');
        return $query;
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class, [
                'label' => 'rule.form.name',
                'required' => true,
            ])
            ->add(
                'player',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => true,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => true,
                    'label' => 'rule.form.player',
                ]
            )
            ->add('translations', TranslationsType::class, [
                'label' => 'rule.form.translations',
                'fields' => [
                    'content' => [
                        'field_type' => RichTextEditorType::class,
                        'label' => 'rule.form.rules',
                        'required' => false,
                     ]
                ]
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'rule.filter.id'])
            ->add('name', null, ['label' => 'rule.filter.name']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'rule.list.id'])
            ->add('name', null, ['label' => 'rule.list.name'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'rule.list.player',
            ])
            ->add('created_at', 'datetime', ['label' => 'rule.list.createdAt'])
            ->add('updated_at', 'datetime', ['label' => 'rule.list.updatedAt'])
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
            ->add('id', null, ['label' => 'rule.show.id'])
            ->add('name', null, ['label' => 'rule.show.name'])
            ->add('player', null, ['label' => 'rule.show.player'])
            ->add('created_at', 'datetime', ['label' => 'rule.show.createdAt'])
            ->add('updated_at', 'datetime', ['label' => 'rule.show.updatedAt'])
            ->add('games', null, ['label' => 'rule.show.games'])
            ->add('getContent', null, ['label' => 'rule.show.text', 'safe' => true]);
    }
}
