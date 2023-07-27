<?php

if (!defined('TIMELINE_PLUGIN_DIR')) {
    define('TIMELINE_PLUGIN_DIR', dirname(__FILE__));
}

if (!defined('TIMELINE_HELPERS_DIR')) {
    define('TIMELINE_HELPERS_DIR', TIMELINE_PLUGIN_DIR . '/helpers');
}

if (!defined('TIMELINE_FORMS_DIR')) {
    define('TIMELINE_FORMS_DIR', TIMELINE_PLUGIN_DIR . '/forms');
}

require_once TIMELINE_PLUGIN_DIR . '/TimelinePlugin.php';
require_once TIMELINE_HELPERS_DIR . '/TimelineFunctions.php';

/**
 * Timeline plugin.
 * 
 * @package Omeka\Plugins\Timeline
 */
class TimelinePlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'admin_head',
        'public_head',
        'install',
        'upgrade',
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
        queue_css_file('timeline');
        queue_js_url('https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js');
    }

    public function hookInstall()
    {
        $db = $this->_db;
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->Timeline` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` TINYTEXT COLLATE utf8_unicode_ci DEFAULT NULL,
                `description` TEXT COLLATE utf8_unicode_ci DEFAULT NULL,
                `font` TINYTEXT NOT NULL,
                `item_date` INT DEFAULT NULL,
                `item_interval` INT DEFAULT NULL,
                `item_title` INT DEFAULT NULL,
                `item_description` INT DEFAULT NULL,
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

    public function hookUpgrade($args)
    {
        $db = $this->_db;
        $old = $args['old_version'];

        if (version_compare($old, '1.1', '<')) {
            $sql = "
                ALTER TABLE `$db->Timeline`
                CHANGE `item_date` `item_date` INT DEFAULT NULL,
                CHANGE `item_interval` `item_interval` INT DEFAULT NULL,
                CHANGE `item_title` `item_title` INT DEFAULT NULL,
                CHANGE `item_description` `item_description` INT DEFAULT NULL
                ";
            $db->query($sql);
        }
    }

    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->Timeline`";
        $db->query($sql);

        delete_option('timeline');
    }

    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
        add_shortcode('timeline', 'timeline_shortcode');
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Timeline_Timelines');

        // Allow everyone access to browse and show.
        $acl->allow(null, 'Timeline_Timelines', array('show', 'browse'));
        $acl->allow('researcher', 'Timeline_Timelines', 'showNotPublic');
        $acl->allow('contributor', 'Timeline_Timelines', array('add', 'editSelf', 'querySelf', 'deleteSelf', 'showNotPublic'));
        $acl->allow(array('super', 'admin', 'contributor', 'researcher'), 'Timeline_Timelines', array('edit', 'query', 'delete'), new Omeka_Acl_Assert_Ownership);

    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $actionRoute = new Zend_Controller_Router_Route('timeline/:action/:id',
            array(
                'module'        => 'timeline',
                'controller'    => 'timelines'
                ),
            array('id'          => '\d+'));
        $router->addRoute('timelinesAction', $actionRoute);

        $defaultRoute = new Zend_Controller_Router_Route('timeline/:action',
            array(
                'module'        => 'timeline',
                'controller'    => 'timelines'
                ),
            );
        $router->addRoute('timelinesDefault', $defaultRoute);

        $redirectRoute = new Zend_Controller_Router_Route('timeline',
            array(
                'module'        => 'timeline',
                'controller'    => 'timelines',
                'action'        => 'browse'
                ),
            );
        $router->addRoute('timelinesRedirect', $redirectRoute);

        $pageRoute = new Zend_Controller_Router_Route('timeline/:page',
            array(
                'module'        => 'timeline',
                'controller'    => 'timelines',
                'action'        => 'browse',
                'page'          => '1'
                ),
            array('page'        => '\d+'));
        $router->addRoute('timelinesPagination', $pageRoute);
    }

    public function hookAdminAppendToPluginUninstallMessage()
    {
        $string = __('<strong>Warning</strong>: Uninstalling the Timeline plugin
          will remove all Timeline records.');

        echo '<p>'.$string.'</p>';
    }

    /**
     * Filter the items_browse_sql to return only items that have a non-empty
     * value for the chosen date field, when using the timeline-json context.
     * Uses the ItemSearch model (models/ItemSearch.php) to add the check for
     * a non-empty DC:Date.
     *
     * @param Omeka_Db_Select $select
     */
    public function hookItemBrowseSql()
    {
        $context = Zend_Controller_Action_HelperBroker::getStaticHelper('ContextSwitch')->getCurrentContext();
        if ($context == 'timeline-json') {
            $search = new ItemSearch($select);
            $newParams[0]['element_id'] = timeline_get_option('item_date');
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
            'label' => __('Timeline'),
            'uri' => url('timeline'),
            'resource' => 'Timeline_Timelines',
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
            'label' => __('Timeline'),
            'uri' => url('timeline')
        );
        return $nav;
    }

    /**
     * Register an exhibit layout for displaying a timeline.
     *
     * @param array $layouts Exhibit layout specs.
     * @return array
     */
    public function filterExhibitLayouts($layouts)
    {
        $layouts['timeline'] = array(
            'name' => __('Timeline'),
            'description' => __('Embed a timeline.')
        );
        return $layouts;
    }
}
