<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CountryAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->remove('delete');
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
            ->add('codeIso2', TextType::class, [
                'label' => 'country.form.codeIso2',
                'required' => true,
            ])
            ->add('codeIso3', TextType::class, [
                'label' => 'country.form.codeIso3',
                'required' => true,
            ])
            ->add('codeIsoNumeric', TextType::class, [
                'label' => 'country.form.codeIsoNumeric',
                'required' => true,
            ])
            ->add('boolMaj', CheckboxType::class, [
                'label' => 'country.form.boolMaj',
                'required' => false,
            ])
            ->add('translations', TranslationsType::class, [
                'label' => 'country.form.translations',
                'required' => true,
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('codeIso3', null, ['label' => 'country.filter.codeIso3']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'country.list.id'])
            ->add('codeIso2', null, ['label' => 'country.list.codeIso2'])
            ->add('codeIso3', null, ['label' => 'country.list.codeIso3'])
            ->add('codeIsoNumeric', null, ['label' => 'country.list.codeIsoNumeric'])
            ->add('badge', null, ['label' => 'country.list.badge'])
            ->add('getDefaultName', null, ['label' => 'country.list.name'])
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
            ->add('getName', null, ['label' => 'country.show.name'])
            ->add('codeIso3', null, ['label' => 'country.show.codeIso3'])
            ->add('codeIso2', null, ['label' => 'country.show.codeIso2'])
            ->add('codeIsoNumeric', null, ['label' => 'country.show.codeIsoNumeric'])
            ->add('badge', null, ['label' => 'country.show.badge']);
    }
}
