<?php

/**
 * Table for Timeline objects.
 *
 * @package Timeline
 * @subpackage Models
 */
class TimelineTable extends Omeka_Db_Table {

    /**
     * Filter public/not public timelines.
     *
     * @param Zend_Db_Select
     * @param boolean Whether to retrieve only public timelines.
     * @return void
     */
    public function filterByPublic($select, $isPublic)
    {
        $isPublic = (bool) $isPublic;

        if ($isPublic) {
            $select->where('timelines.public = 1');
        } else {
            $select->where('timelines.public = 0');
        }
    }

    /**
     * Filter featured/not featured timelines.
     *
     * @param Zend_Db_Select
     * @param boolean Whether to retrieve only featured timelines.
     */
    public function filterByFeatured($select, $isFeatured)
    {
        $isFeatured = (bool) $isFeatured;

        if ($isFeatured) {
            $select->where('timelines.featured = 1');
        } else {
            $select->where('timelines.featured = 0');
        }
    }

    /**
     * Filter for timelines created by a specific user.
     *
     * @param Zend_Db_Select
     * @param boolean Whether to retrieve only featured timelines.
     */
    public function filterByUser($select, $userId, $userField)
    {
        $userId = (int) $userId;

        if ($userId) {
            $select->where('timelines.creator_id = ?', $userId);
        }
    }

    /**
     * Order SELECT results randomly.
     *
     * @param Zend_Db_Select
     * @return void
     */
    public function orderSelectByRandom($select)
    {
        $select->order('RAND()');
    }

    /**
     * Possible options: 'public','user', and 'featured'.
     *
     * @param Omeka_Db_Select
     * @param array
     * @return void
     */
    public function applySearchFilters($select, $params)
    {
        parent::applySearchFilters($select, $params);

        if (isset($params['user'])) {
            $userId = $params['user'];
            $this->filterByUser($select, $userId);
        }

        if(isset($params['public'])) {
            $this->filterByPublic($select, $params['public']);
        }

        if(isset($params['featured'])) {
            $this->filterByFeatured($select, $params['featured']);
        }

        if(isset($params['random'])) {
            $this->orderSelectByRandom($select);
        }

        $select->group("timelines.id");
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $permissions = new Omeka_Db_Select_PublicPermissions('Timeline_Timelines');
        $permissions->apply($select, 'timelines', null);
        return $select;
    }

    /**
     * Return the columns to be used for creating an HTML select of timelines.
     *
     * @return array
     */
    public function _getColumnPairs()
    {
        return array(
            'timelines.id',
            'timelines.title'
        );
    }
}
