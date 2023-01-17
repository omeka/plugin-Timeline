<?php
/**
 * The edit query view for a specific Timeline.
 */

$timelineTitle = metadata($timeline, 'title');
$title = __('Timeline | Edit "%s" Items Query', $timelineTitle);
$head = array('bodyclass' => 'timelines primary', 'title' => $title);
echo head($head);
?>
<script type="text/javascript" charset="utf-8">
    jQuery(window).load(function(){
       Omeka.Search.activateSearchButtons; 
    });
</script>
    <?php
$query = isset($timeline->query) ? unserialize($timeline->query): [];
if ($query && is_array($query)) {
?>
    <p><strong><?php echo __('The &#8220;%s&#8221; timeline displays items that match the following query:', $timelineTitle) ?></strong></p>
<?php
    echo item_search_filters($query);
}

$formArgs = array(
    'formAttributes' => array('id'=>'advanced-search-form'), 
    'formActionUri' => current_url(),
    'useSidebar' => true
);
$formArgs['csrf'] = isset($csrf) ? $csrf : '';
echo $this->partial('items/search-form.php', $formArgs);
echo foot();
?>
