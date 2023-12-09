<?php
/**
 * The edit query view for a specific Timeline.
 */

$timelineTitle = metadata($timeline, 'title');
$title = __('Timeline | Edit "%s" Items Query', $timelineTitle);
$head = array('bodyclass' => 'timelines primary', 'title' => $title);
echo head($head);
echo flash();
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
    // Some parts of the advanced search check $_GET, others check
    // $_REQUEST, so we set both to be able to edit a previous query.
    $_GET = $query;
    $_REQUEST = $query;
}

$formArgs = array(
    'formAttributes' => array('id'=>'advanced-search-form'), 
    'formActionUri' => current_url(),
    'useSidebar' => true
);

// Insert CSRF token
echo str_replace('</form>', $csrf . '</form>', $this->partial('items/search-form.php', $formArgs));
echo foot();
?>
