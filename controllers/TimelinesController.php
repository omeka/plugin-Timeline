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

    public function browseAction()
    {
        $db = $this->_helper->db;

        // Set the order of timelines.
        $timelinesTable = $db->getTable('Timeline');
        $timelines = $timelinesTable->fetchOrderedTimelines();

        $this->view->assign('timelines', $timelines);
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

    protected function _redirectAfterAdd($timeline)
    {
        $this->_redirect("timeline/query/{$timeline->id}");
    }

    protected function _redirectAfterDelete($record)
    {
        $this->_redirect("timeline");
    }

    public function editAction()
    {
        $timeline = $this->_helper->db->findById();
    
        require_once TIMELINE_FORMS_DIR . '/timeline.php';
        $form = new Timeline_Form_Timeline;
        $form->setDefaults(array('title' => $timeline->title, 
                                 'description' => $timeline->description,
                                 'font' => $timeline->font,
                                 'public' => $timeline->public,
                                 'item_date' => $timeline->item_date,
                                 'item_interval' => $timeline->item_interval,
                                 'item_title' => $timeline->item_title,
                                 'item_description' => $timeline->item_description,
                                 'featured' => $timeline->featured,
                                 'truncate' => $timeline->truncate,
                                 'order' => $timeline->order));

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

    /**
     * Order the timelines.
     */
    public function updateOrderAction()
    {
        // Allow only AJAX requests.
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->redirector->gotoUrl('/');
        }

        // Update the timeline orders.
        $this->_helper->db->getTable('Timeline')->updateOrder($this->_getParam('timelines'));
    }

    /**
     * Reset the timeline order to default (order added).
     */
    public function resetOrderAction()
    {
        // Allow only AJAX requests.
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->redirector->gotoUrl('/');
        }

        // Reorder Timeline param array by ID.
        $this->_helper->db->getTable('Timeline')->resetOrder();
    }

    protected function _getAddSuccessMessage($timeline)
    {
        return __('The timeline "%s" was successfully added! Please search for items to populate timeline.', $timeline->title);
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
