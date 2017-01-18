<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class GroupAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_group';


    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ( ($this->hasRequest()) && ($this->isCurrentRoute('create')) ) {
            $idGame = $this->getRequest()->get('idGame', null);
            if ($idGame !== null) {
                $_SESSION['vgrcorebundle_admin_group']['idGame'] = $idGame;
            }

            if (isset($_SESSION['vgrcorebundle_admin_group']['idGame'])) {
                $idGame = $_SESSION['vgrcorebundle_admin_group']['idGame'];
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Game');
                $game = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);
            }

        }

        $formMapper
            ->add('idGroup', 'text', array(
                'label' => 'idGroup',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('game', 'sonata_type_model_list', array(
                'data'          => isset($game) ? $game : null,
                'data_class'    => null,
                'btn_add'       => false,
                'btn_list'      => true,
                'btn_delete'    => false,
                'btn_catalogue' => true,
                'label'         => 'Game',
            ))
            ->add('libGroupEn', 'text', array(
                'label' => 'Name (EN)',
                'required' => true,
            ))
            ->add('libGroupFr', 'text', array(
                'label' => 'Name (FR)',
                'required' => false,
            ))
            ->add('boolDLC', 'checkbox', array(
                'label' => 'DLC ?',
                'required' => false,
            ))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libGroupFr')
            ->add('libGroupEn')
            ->add('game')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idGroup')
            ->add('libGroupEn', null, array('editable' => false))
            ->add('libGroupFr')
            ->add('game', null, array(
                'associated_property' => 'libGameEn',
                'label' => 'Game',
            ))
            ->add('boolDLC', 'boolean')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'groups' => array(
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:group_charts_link.html.twig'
                    ),
                )
            ))
        ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('idGroup')
            ->add('libGroupFr')
            ->add('libGroupEn')
            ->add('game', null, array(
                'associated_property' => 'libGameEn',
                'label' => 'Game',
            ))
            ->add('charts')
        ;
    }

}