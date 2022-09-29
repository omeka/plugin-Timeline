<?php
$formStem = $block->getFormStem();
$options = $block->getOptions();
?>
<div class="layout-options">
    <div class="block-header">
        <h4><?php echo __('Timeline Items'); ?></h4>
        <div class="drawer-toggle"></div>
    </div>
    
    <form method="POST" action="#" class="import">
        <div id="search-by-range" class="field">
            <?php echo $this->formLabel('range', __('Search by a range of ID#s (example: 1-4, 156, 79)')); ?>
            <div class="inputs">
            <?php
                echo $this->formText('range', @$_GET['range'],
                    array('size' => '40')
                );
            ?>
            </div>
        </div>

        <div class="field">
            <?php echo $this->formLabel('collection-search', __('Search By Collection')); ?>
            <div class="inputs">
            <?php
                echo $this->formSelect(
                    'collection',
                    @$_REQUEST['collection'],
                    array('id' => 'collection-search'),
                    get_table_options('Collection', null, array('include_no_collection' => true))
                );
            ?>
            </div>
        </div>

        <div class="field">
            <?php echo $this->formLabel('item-type-search', __('Search By Type')); ?>
            <div class="inputs">
            <?php
                echo $this->formSelect(
                    'type',
                    @$_REQUEST['type'],
                    array('id' => 'item-type-search'),
                    get_table_options('ItemType')
                );
            ?>
            </div>
        </div>
        
        <div class="field">
            <?php echo $this->formLabel('tag-search', __('Search By Tags')); ?>
            <div class="inputs">
            <?php
                echo $this->formText('tags', @$_REQUEST['tags'],
                    array('size' => '40', 'id' => 'tag-search')
                );
            ?>
            </div>
        </div>
        
        <div>
        <input
          type="submit"
          id="submit_search_advanced"
          class="submit big green button"
          name="submit_search"
          value="<?php echo __('Find Items'); ?>" />
        </div>
    
    </form>
</div>

<div class="layout-options">
    <div class="block-header">
        <h4><?php echo __('Timeline Options'); ?></h4>
        <div class="drawer-toggle"></div>
    </div>

    <div class="timeline-title">
        <?php echo $this->formLabel($formStem . '[options][timeline-title]', __('Timeline title')); ?>
        <?php
        echo $this->formText($formStem . '[options][timeline-title]',
            @$options['timeline-title'],
        );
        ?>
    </div>
    
    <div class="timeline-text">
        <?php echo $this->formLabel($formStem . '[options][timeline-text]', __('Timeline text')); ?>
        <?php
        echo $this->formTextarea($formStem . '[options][timeline-text]',
            @$options['timeline-text'],
        );
        ?>
    </div>
    
    <div class="timeline-timestamp-field">
        <?php echo $this->formLabel($formStem . '[options][timestamp-field]', __('Timestamp field')); ?>
        <?php
        echo $this->formSelect($formStem . '[options][timestamp-field]',
            @$options['timestamp-field'], array(),
            get_table_options('Element', null, array(
                'record_types' => array('Item', 'All'),
                'sort' => 'orderBySet')
            )
        )
        ?>
    </div>
    
    <div class="timeline-interval-field">
        <?php echo $this->formLabel($formStem . '[options][interval-field]', __('Interval field')); ?>
        <?php
        echo $this->formSelect($formStem . '[options][interval-field]',
            @$options['interval-field'], array(),
            get_table_options('Element', null, array(
                'record_types' => array('Item', 'All'),
                'sort' => 'orderBySet')
            )
        )
        ?>
    </div>
</div>
