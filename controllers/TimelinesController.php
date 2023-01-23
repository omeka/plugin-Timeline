<?php
/**
 * Timelines Controller
 */
class Timeline_TimelinesController extends Omeka_Controller_AbstractActionController
{
    protected $_autoCsrfProtection = true;
    
    /**
     * Initialization
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Timeline');
    }

    public function addAction()
    {
        require_once TIMELINE_FORMS_DIR . '/timeline.php';
        $form = new Timeline_Form_Timeline;
        $form->setDefaults(array('item_date' => '40',
                                 'item_interval' => '38',
                                 'item_title' => '50',
                                 'item_description' => '41'));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if (!$this->view->form->isValid($_POST)) {
                $this->_helper->_flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        parent::addAction();
    }

    public function editAction()
    {
        $timeline = $this->_helper->db->findById();
    
        require_once TIMELINE_FORMS_DIR . '/timeline.php';
        $form = new Timeline_Form_Timeline;
        $form->setDefaults(array('title' => $timeline->title, 
                                 'description' => $timeline->description,
                                 'public' => $timeline->public,
                                 'item_date' => $timeline->item_date,
                                 'item_interval' => $timeline->item_interval,
                                 'item_title' => $timeline->item_title,
                                 'item_description' => $timeline->item_description,
                                 'featured' => $timeline->featured));

        $this->view->form = $form;
        parent::editAction();
    
    }

    public function queryAction()
    {
        $csrf = new Omeka_Form_SessionCsrf;
        $this->view->csrf = $csrf;

        $timeline = $this->_helper->db->findById();

        if(!empty($_GET)) {
            if (!$csrf->isValid($_GET)) {
                $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
            } else if (isset($_GET['search'])) {
                $timeline->query = $_GET;
                unset($timeline->query['csrf_token']);
                $timeline->save();
                $this->_helper->flashMessenger($this->_getEditSuccessMessage($timeline), 'success');
                $this->_helper->redirector->gotoRoute(array('action' => 'show'));
            } else {
                $queryArray = unserialize((string)$timeline->query);
                // Some parts of the advanced search check $_GET, others check
                // $_REQUEST, so we set both to be able to edit a previous query.
                $_GET = $queryArray;
                $_REQUEST = $queryArray;
            }
        }

        $this->view->timeline = $timeline;
    }
    
    public function showAction()
    {
        $timeline = $this->_helper->db->findById();

        if (isset($timeline->query)) {
            $items = get_db()->getTable('Item')->findBy(unserialize($timeline->query), null);
        } else {
            $items = [];
        }

        $this->view->timeline = $timeline;
        $this->view->items = $items;
    }

    protected function _getAddSuccessMessage($timeline)
    {
        return __('The timeline "%s" was successfully added!', $timeline->title);
    }

    protected function _getEditSuccessMessage($timeline)
    {
        return __('The timeline "%s" was successfully changed!', $timeline->title);
    }

    protected function _getDeleteSuccessMessage($timeline)
    {
        return __('The timeline "%s" was successfully deleted!', $timeline->title);
    }

    protected function _getDeleteConfirmMessage($timeline)
    {
        return __('This will delete the timeline "%s" and its associated metadata. This will not delete any items associated with this timeline.', $timeline->title);
    }

}
