<?php
/**
 * TimelineJS record.
 */
class Timeline extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{

    public $title;
    public $description;
    public $font;
    public $item_date;
    public $item_interval;
    public $item_title;
    public $item_description;
    public $query;
    public $creator_id = 0;
    public $public = 0;
    public $featured = 0;
    public $added;
    public $modified;

    /**
     * Mixin initializer.
     */
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this, 'creator_id');
        $this->_mixins[] = new Mixin_PublicFeatured($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
    }

    /**
     * Required by Zend_Acl_Resource_Interface.
     *
     * Identifies Timeline records as relating to the Timeline ACL
     * resource.
     *
     * @since 1.0
     * @return string
     */
    public function getResourceId()
    {
        return 'Timeline_Timelines';
    }

    /**
     * Validate the form data.
     */
    protected function _validate()
    {
        if (empty($this->title)) {
            $this->addError('title', __('Title is required.'));
        }
    }

    protected function beforeSave($args)
    {
        $query = $this->query;
        if (is_array($query)) {
          $this->query = serialize($query);
        }
    }

    /**
     * Get the routing parameters or the URL string to this record.
     */
    public function getRecordUrl($action = 'show')
    {
        $urlHelper = new Omeka_View_Helper_Url;
        $params = array('action' => $action, 'id' => $this->id);
        return $urlHelper->url($params, 'timelinesAction');
    }
}
