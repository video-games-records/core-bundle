<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;

class ChartAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_chart';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
        ;

    }
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $groupOptions = array();
        if ( ($this->hasRequest()) && ($this->isCurrentRoute('create')) ) {
            $idGroup = $this->getRequest()->get('idGroup', null);
            if ($idGroup !== null) {
                $_SESSION['vgrcorebundle_admin_chart']['idGroup'] = $idGroup;
            }

            if (isset($_SESSION['vgrcorebundle_admin_chart']['idGroup'])) {
                $idGroup = $_SESSION['vgrcorebundle_admin_chart']['idGroup'];
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Group');
                $group = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Group', $idGroup);
                $groupOptions = array('data' => $group);
            }
        }

        $formMapper
            ->add('idChart', 'text', array(
                'label' => 'idChart',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('group', 'sonata_type_model_list', array_merge(
                $groupOptions,
                array(
                    'data_class'    => null,
                    'btn_add'       => false,
                    'btn_list'      => true,
                    'btn_delete'    => false,
                    'btn_catalogue' => true,
                    'label'         => 'Group',
                    'required'      => true,
                )
            ),array(
                'placeholder' => 'No group selected'
            ))
            ->add('libChartEn', 'text', array(
                'label' => 'Name (EN)',
                'required' => true,
            ))
            ->add('libChartFr', 'text', array(
                'label' => 'Name (FR)',
                'required' => false,
            ))
        ;

        if ( ($this->hasRequest()) && ($this->isCurrentRoute('edit')) ) {
            $formMapper
                ->add(
                    'statusUser',
                    'choice',
                    array(
                        'label' => 'Status User',
                         'choices' => Chart::getStatusChoices()
                    )
                )
            ;
            $formMapper
                ->add(
                    'statusTeam',
                    'choice',
                    array(
                        'label' => 'Status Team',
                        'choices' => Chart::getStatusChoices()
                    )
                )
            ;
        }

        $formMapper
            ->add('libs', 'sonata_type_collection', array(
                'by_reference' => false,
                'help' => (($this->isCurrentRoute('create')) ? 'If you dont add libs, the libs will be automatically added to the chart by cloning the first chart of the group' : ''),
                'type_options' => array(
                    // Prevents the "Delete" option from being displayed
                    'delete' => true,
                    'delete_options' => array(
                        // You may otherwise choose to put the field but hide it
                        'type'         => 'checkbox',
                        // In that case, you need to fill in the options as well
                        'type_options' => array(
                            'mapped'   => false,
                            'required' => false,
                        )
                    )
                )
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ))
        ;

    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libChartEn')
            ->add('libChartFr')
            ->add('group', 'doctrine_orm_model_autocomplete', array(), null, array(
                'property' => 'libGroupEn',
            ))
            ->add('statusUser', 'doctrine_orm_choice', array(), 'choice', array('choices' => Chart::getStatusChoices()))
            ->add('statusTeam', 'doctrine_orm_choice', array(), 'choice', array('choices' => Chart::getStatusChoices()))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idChart')
            ->add('libChartEn', null, array('editable' => false))
            ->add('libChartFr')
            ->add('group', null, array(
                'associated_property'       => 'libGroupEn',
                'label'         => 'Group',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('idChart')
            ->add('libChartFr')
            ->add('libChartEn')
            ->add('group', null, array(
                'associated_property'       => 'libGroupEn',
                'label'         => 'Group',
            ))
        ;
    }


    /**
     * @param mixed $object
     */
    public function prePersist($object)
    {
        $libs = $object->getLibs();
        if (count($libs) == 0) {
            $group = $object->getGroup();
            if ($group != null) {
                $charts = $group->getCharts();
                if (count($charts) > 0) {
                    $chart = $charts[0];
                    foreach ($chart->getLibs() as $oldLib) {
                        $newLib = new ChartLib();
                        $newLib->setName($oldLib->getName());
                        $newLib->setType($oldLib->getType());
                        $object->addLib($newLib);
                    }
                }
            }
        }
    }

}