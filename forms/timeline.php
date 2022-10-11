<?php
/**
 * Form for creating TimelineJS timelines.
 */
class TimelineJS_Form_Timeline extends Omeka_Form
{
    public function init()
    {
        parent::init();

        $this->setMethod('post');
        $this->setAttrib('id', 'timeline-form');

        // Timeline Title
        $this->addElement('text', 'title', array(
            'label'       => __('Title'),
            'description' => __('A title for your timeline.')
        ));

        // Timeline Description
        $this->addElement('textarea', 'description', array(
            'label'       => __('Description'),
            'description' => __('A description for your timeline.'),
            'attribs'     => array('class' => 'html-editor', 'rows' => '15')
        ));
        
        // Item date field
        $this->addElement('select', 'item_date', array(
            'label'       => __('Item date field'),
            'description' => __('Metadata field to use for item date containing discrete time/date values (must be in YYYY-MM-DDThh:mm:ss format).'),
            'multiOptions' => get_table_options('Element', null, array(
                              'record_types' => array('Item', 'All'),
                              'sort' => 'orderBySet')
            )
        ));
        
        // Item interval field
        $this->addElement('select', 'item_interval', array(
            'label'       => __('Item interval field'),
            'description' => __('Metadata field to use for item date containing time/date span values (must be in YYYY-MM-DDThh:mm:ss/YYYY-MM-DDThh:mm:ss format).'),
            'multiOptions' => get_table_options('Element', null, array(
                              'record_types' => array('Item', 'All'),
                              'sort' => 'orderBySet')
            )
        ));
        
        // Item title field
        $this->addElement('select', 'item_title', array(
            'label'       => __('Item title field'),
            'description' => __('Metadata field to use for item title in timeline.'),
            'multiOptions' => get_table_options('Element', null, array(
                              'record_types' => array('Item', 'All'),
                              'sort' => 'orderBySet')
            )
        ));
        
        // Item description field
        $this->addElement('select', 'item_description', array(
            'label'       => __('Item description field'),
            'description' => __('Metadata field to use for item description in timeline.'),
            'multiOptions' => get_table_options('Element', null, array(
                              'record_types' => array('Item', 'All'),
                              'sort' => 'orderBySet')
            )
        ));

        // Public/Not Public
        $this->addElement('select', 'public', array(
            'label'        => __('Status'),
            'description'  => __('Whether the timeline is public or not.'),
            'multiOptions' => array('0' => 'Not Public', '1' => 'Public')
        ));

        // Featured/Not Featured
        $this->addElement('select', 'featured', array(
            'label'        => __('Featured'),
            'description'  => __('Whether the timeline is featured or not.'),
            'multiOptions' => array('0' => 'Not Featured', '1' => 'Featured')
        ));

        // Submit
        $this->addElement('submit', 'submit', array(
            'label' => __('Save Timeline')
        ));

        // Group the title, description, and public fields
        $this->addDisplayGroup(
            array('title',
                  'description',
                  'item_date',
                  'item_interval',
                  'item_title',
                  'item_description',
                  'public',
                  'featured',
                 ),
            'timeline_info'
        );

        // Add the submit to a separate display group.
        $this->addDisplayGroup(array('submit'), 'timeline_submit');
    }

}
