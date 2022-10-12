<?php
/**
 * Timelines Controller
 */
class TimelineJS_TimelinesController extends Omeka_Controller_AbstractActionController
{
    /**
     * Initialization
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('TimelineJS');
        $this->_browseRecordsPerPage = get_option('per_page_admin');
    }

    public function addAction()
    {
        require_once TIMELINE_JS_FORMS_DIR . '/timeline.php';
        $form = new TimelineJS_Form_Timeline;
        $form->setDefaults(array('item_date' => '40',
                                 'item_interval' => '38',
                                 'item_title' => '50',
                                 'item_description' => '41'));
        $this->view->form = $form;
        parent::addAction();
    }

    public function editAction()
    {
        $timeline = $this->_helper->db->findById();
    
        require_once TIMELINE_JS_FORMS_DIR . '/timeline.php';
        $form = new TimelineJS_Form_Timeline;
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
        $timeline = $this->_helper->db->findById();

        if(isset($_GET['search'])) {
            $timeline->query = $_GET;
            $timeline->save();
            $this->_helper->flashMessenger($this->_getEditSuccessMessage($timeline), 'success');
            $this->_helper->redirector->gotoRoute(array('action' => 'show'));
        }
        else {
            $queryArray = unserialize($timeline->query);
            // Some parts of the advanced search check $_GET, others check
            // $_REQUEST, so we set both to be able to edit a previous query.
            $_GET = $queryArray;
            $_REQUEST = $queryArray;
        }

        $this->view->timelinejs = $timeline;
    }

    // public function itemsAction()
    // {
    //     $timeline = $this->_helper->db->findById();
    // 
    //     $query = $timeline->query ? unserialize($timeline->query) : array();
    //     $items = get_db()->getTable('Item')->findBy($query, null);
    // 
    //     $this->view->timelinejs = $timeline;
    //     $this->view->items = $items;
    // }
    
    public function showAction()
    {
        $timeline = $this->_helper->db->findById();
        $items = get_db()->getTable('Item')->findBy(unserialize($timeline->query), null);

        $this->view->timelinejs = $timeline;
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
