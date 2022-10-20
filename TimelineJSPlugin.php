<?php

if (!defined('TIMELINE_JS_PLUGIN_DIR')) {
    define('TIMELINE_JS_PLUGIN_DIR', dirname(__FILE__));
}

if (!defined('TIMELINE_JS_HELPERS_DIR')) {
    define('TIMELINE_JS_HELPERS_DIR', TIMELINE_JS_PLUGIN_DIR . '/helpers');
}

if (!defined('TIMELINE_JS_FORMS_DIR')) {
    define('TIMELINE_JS_FORMS_DIR', TIMELINE_JS_PLUGIN_DIR . '/forms');
}

require_once TIMELINE_JS_PLUGIN_DIR . '/TimelineJSPlugin.php';
require_once TIMELINE_JS_HELPERS_DIR . '/TimelineJSFunctions.php';

/**
 * TimelineJS plugin.
 * 
 * @package Omeka\Plugins\TimelineJS
 */
class TimelineJSPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'admin_head',
        'public_head',
        'install',
        'uninstall',
        'initialize',
        'define_acl',
        'define_routes',
        'admin_append_to_plugin_uninstall_message',
        'item_browse_sql',
    );

    protected $_filters = array(
        'admin_navigation_main',
        'public_navigation_main',
        'response_contexts',
        'action_contexts',
        'exhibit_layouts'
    );

    public function hookAdminHead()
    {
        queue_css_url('https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css');
        queue_js_url('https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js');
    }

    public function hookPublicHead()
    {
        queue_css_url('https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css');
        queue_js_url('https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js');
    }

    public function hookInstall()
    {
        $db = $this->_db;
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->TimelineJS` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` TINYTEXT COLLATE utf8_unicode_ci DEFAULT NULL,
                `description` TEXT COLLATE utf8_unicode_ci DEFAULT NULL,
                `item_date` INT UNSIGNED NOT NULL,
                `item_interval` INT UNSIGNED NOT NULL,
                `item_title` INT UNSIGNED NOT NULL,
                `item_description` INT UNSIGNED NOT NULL,
                `query` TEXT COLLATE utf8_unicode_ci DEFAULT NULL,
                `creator_id` INT UNSIGNED NOT NULL,
                `public` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
                `featured` TINYINT(1) NOT NULL DEFAULT '0',
                `added` timestamp NOT NULL default '2000-01-01 00:00:00',
                `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ";

        $db->query($sql);

    }

    public function hookUninstall()
    {

        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->TimelineJS`";
        $db->query($sql);

        delete_option('timelinejs');

    }

    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
        add_shortcode('timeline', 'timelinejs_shortcode');
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];

        $acl->addResource('TimelineJS_Timelines');

        // Allow everyone access to browse, show, and items.
        $acl->allow(null, 'TimelineJS_Timelines', array('show', 'browse', 'items'));

        $acl->allow('researcher', 'TimelineJS_Timelines', 'showNotPublic');
        $acl->allow('contributor', 'TimelineJS_Timelines', array('add', 'editSelf', 'querySelf', 'itemsSelf', 'deleteSelf', 'showNotPublic'));
        $acl->allow(array('super', 'admin', 'contributor', 'researcher'), 'TimelineJS_Timelines', array('edit', 'query', 'items', 'delete'), new Omeka_Acl_Assert_Ownership);

    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $actionRoute = new Zend_Controller_Router_Route('timeline-js/timelines/:action/:id',
                        array(
                            'module'        => 'timeline-js',
                            'controller'    => 'timelines'
                            ),
                        array('id'          => '\d+'));
        $router->addRoute('timelinesAction', $actionRoute);

        $defaultRoute = new Zend_Controller_Router_Route('timeline-js/timelines/:action',
                        array(
                            'module'        => 'timeline-js',
                            'controller'    => 'timelines'
                            ),
                        );
        $router->addRoute('timelinesDefault', $defaultRoute);

        $redirectRoute = new Zend_Controller_Router_Route('timeline-js',
                        array(
                            'module'        => 'timeline-js',
                            'controller'    => 'timelines',
                            'action'        => 'browse'
                            ),
                        );
        $router->addRoute('timelinesRedirect', $redirectRoute);

        $pageRoute = new Zend_Controller_Router_Route('timeline-js/timelines/:page',
                        array(
                            'module'        => 'timeline-js',
                            'controller'    => 'timelines',
                            'action'        => 'browse',
                            'page'          => '1'
                            ),
                        array('page'          => '\d+'));
        $router->addRoute('timelinesPagination', $pageRoute);
    }

    public function hookAdminAppendToPluginUninstallMessage()
    {
        $string = __('<strong>Warning</strong>: Uninstalling the TimelineJS plugin
          will remove all Timeline records.');

        echo '<p>'.$string.'</p>';

    }

    /**
     * Filter the items_browse_sql to return only items that have a non-empty
     * value for the chosen date field, when using the timelinejs-json context.
     * Uses the ItemSearch model (models/ItemSearch.php) to add the check for
     * a non-empty DC:Date.
     *
     * @param Omeka_Db_Select $select
     */
    public function hookItemBrowseSql()
    {

        $context = Zend_Controller_Action_HelperBroker::getStaticHelper('ContextSwitch')->getCurrentContext();
        if ($context == 'timelinejs-json') {
            $search = new ItemSearch($select);
            $newParams[0]['element_id'] = timelinejs_get_option('item_date');
            $newParams[0]['type'] = 'is not empty';
            $search->advanced($newParams);
        }

    }

    /**
     * Timeline admin_navigation_main filter.
     *
     * Adds a button to the admin's main navigation.
     *
     * @param array $nav
     * @return array
     */
    public function filterAdminNavigationMain($nav)
    {

        $nav[] = array(
            'label' => __('TimelineJS'),
            'uri' => url('timeline-js'),
            'resource' => 'TimelineJS_Timelines',
            'privilege' => 'browse'
        );
        return $nav;

    }

    /**
     * Timeline public_navigation_main filter.
     *
     * Adds a button to the public theme's main navigation.
     *
     * @param array $nav
     * @return array
     */
    public function filterPublicNavigationMain($nav)
    {

        $nav[] = array(
            'label' => __('TimelineJS'),
            'uri' => url('timeline-js')
        );
        return $nav;

    }

    /**
     * Adds the timelinejs-json context to response contexts.
     */
    public function filterResponseContexts($contexts)
    {

        $contexts['timelinejs-json'] = array(
            'suffix'  => 'timelinejs-json',
            'headers' => array('Content-Type' => 'text/javascript')
        );

        return $contexts;

    }

    /**
     * Adds timelinejs-json context to the 'items' actions for the
     * TimelineJS_TimelinesController.
     */
    public function filterActionContexts($contexts, $args)
    {

        if ($args['controller'] instanceof TimelineJS_TimelinesController) {
            $contexts['items'][''] = 'timelinejs-json';
        }

        return $contexts;

    }

    /**
     * Register an exhibit layout for displaying a timeline.
     *
     * @param array $layouts Exhibit layout specs.
     * @return array
     */
    public function filterExhibitLayouts($layouts)
    {
        $layouts['timelinejs'] = array(
            'name' => __('TimelineJS'),
            'description' => __('Embed a TimelineJS timeline.')
        );
        return $layouts;
    }
}
