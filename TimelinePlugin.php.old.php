<?php

class TimelinePlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'initialize',
        'uninstall',
        'public_head',
        'admin_head',
    );

    protected $_filters = array(
        'exhibit_layouts',
    );

    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    public function hookUninstall()
    {
        $db = $this->_db;

        $timelineBlocks = $db->getTable('ExhibitPageBlock')->findBy(array('layout' => 'timeline-block'));
        foreach ($timelineBlocks as $block) {
            $block->delete();
        }
    }

    public function hookPublicHead()
    {
        queue_css_url('https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css');
        queue_js_url('https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js');
    }
    
    public function hookAdminHead()
    {
        // queue_css_file('exhibits');
    }

    public function filterExhibitLayouts($layouts)
    {
        $layouts['timeline-block'] = array(
            'name' => __('Timeline Block'),
            'description' => __('Adds a Timeline.js block'),
        );

        return $layouts;
    }
}
