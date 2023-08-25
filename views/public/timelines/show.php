<?php
/**
 * The public show view for Timelines.
 */

$head = array('bodyclass' => 'timelines primary',
              'title' => metadata($timeline, 'title')
              );
echo head($head);
?>
<h1><?php echo metadata($timeline, 'title'); ?></h1>

    <!-- Construct the timeline. -->
    <?php echo $this->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timeline)); ?>

    <script>
      jQuery(document).ready(function($) {
            // Use webkit-line-clamp to truncate if selected
            <?php if (metadata($timeline, 'truncate')): ?>
                $('.tl-text-headline-container, .tl-text-content').css({
                    'display': '-webkit-box',
                    '-webkit-box-orient': 'vertical',
                    'overflow': 'hidden',
                    'font-size': '1rem',
                    'line-height': '1.5rem',
                });
                $('.tl-text-headline-container').css({
                    '-webkit-line-clamp': '2',
                    'height': '6rem',
                    'max-height': '105px',
                });
                $('.tl-text-content').css({
                    '-webkit-line-clamp': '4',
                    'height': '5.5rem',
                    'max-height': '200px',
                });
            <?php endif; ?>
        });
    </script>

<?php echo foot(); ?>
