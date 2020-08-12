<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\AdminBundle\Form\Type\ModelListType;
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
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $query->leftJoin($query->getRootAliases()[0]  . '.translations', 't')
            ->addSelect('t');
        return $query;
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('serie', ModelAutocompleteType::class, [
                'property' => 'translations.name'
            ])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Badge',
            ])
            ->add('picture', TextType::class, [
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
            ->add('publishedAt', DateType::class, [
                'label' => 'Published At',
                'required' => false,
                'years' => range(2004, date('Y'))
            ])
            ->add(
                'etat',
                ChoiceType::class,
                [
                    'label' => 'Etat',
                    'choices' => Game::getEtatsChoices(),
                ]
            )
            ->add('boolRanking', CheckboxType::class, [
                'label' => 'Ranking ?',
                'required' => false,
            ])
            ->add('boolMaj', CheckboxType::class, [
                'label' => 'Maj ?',
                'required' => false,
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'name' => [
                        'field_type' => TextType::class,
                        'label' => ' Name',
                        'required' => true,
                    ],
                    'rules' => [
                        'field_type' => CKEditorType::class,
                        'label' => ' Rules',
                        'required' => false,
                    ]
                ]
            ])
            ->add('platforms', null, ['required' => false, 'expanded' => false]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('serie')
            ->add('translations.name', null, ['label' => 'Name'])
            ->add('status')
            ->add('etat')
            ->add('boolRanking');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('getDefaultName', null, ['label' => 'Name'])
            ->add('slug', null, ['label' => 'Slug'])
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
            ->add('getDefaultName', null, ['label' => 'Name'])
            ->add('status')
            ->add('etat')
            ->add('groups');
    }

    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Game $object
     * @throws \Exception
     */
    public function preUpdate($object)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);

        // PUBLISHED
        if ($originalObject['status'] === Game::STATUS_INACTIVE && $object->getStatus() === Game::STATUS_ACTIVE) {
            if ($object->getPublishedAt() == null) {
                $object->setPublishedAt(new \DateTime());
            }
        }
    }
}
