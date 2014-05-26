<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 26.05.14
 */
trait Jarlssen_CmsFiles_Block_Adminhtml_Grid_Trait
{
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('merge', array(
            'header'    => Mage::helper('jarlssen_cmsfiles')->__('Merge'),
            'width'     => 10,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'jarlssen_cmsfiles/adminhtml_grid_renderer',
        ));
    }

    public function getMainButtonsHtml()
    {
        return "<span class=\"merge-file\">File is newer than DB</span>&nbsp;<span class=\"merge-db\">DB is newer than file</span>" . parent::getMainButtonsHtml();
    }

    public function getRowClass($row)
    {
        if($row->getMergeState() == Jarlssen_CmsFiles_Helper_Data::STATE_FILE_NEWER)
            return "merge-file";
        if($row->getMergeState() == Jarlssen_CmsFiles_Helper_Data::STATE_DB_NEWER)
            return "merge-db";
    }
}