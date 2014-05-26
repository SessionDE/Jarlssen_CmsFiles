<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 24.04.14
 */

class Jarlssen_CmsFiles_Model_Observer
{
    protected function annotate(Varien_Object $obj, $path)
    {
        if(file_exists($path)) {
            Mage::log(strtotime($obj->getUpdateTime()));
            Mage::log(filemtime($path));

            if((int)filemtime($path) > (int)strtotime($obj->getUpdateTime()))
                $obj->setMergeState(Jarlssen_CmsFiles_Helper_Data::STATE_FILE_NEWER);
            else
                $obj->setMergeState(Jarlssen_CmsFiles_Helper_Data::STATE_DB_NEWER);

            if((bool)Mage::getStoreConfig(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_DEV_OVERRIDE)) {
                $obj->setContent(file_get_contents($path));
            }
        } else
            $obj->setMergeState(Jarlssen_CmsFiles_Helper_Data::STATE_NO_FILE);
    }


    public function loadCmsPage(Varien_Event_Observer $observer)
    {
        /** @var Mage_Cms_Model_Page $page */
        $page = $observer->getEvent()->getDataObject();
        $this->annotate($page, Mage::helper('jarlssen_cmsfiles')->getPath(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_PAGE_PATH, $page->getIdentifier()));
    }

    public function loadCmsBlock(Varien_Event_Observer $observer)
    {
        /** @var Mage_Cms_Model_Block $page */
        $block = $observer->getEvent()->getDataObject();
        $this->annotate($block, Mage::helper('jarlssen_cmsfiles')->getPath(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_BLOCK_PATH, $block->getIdentifier()));
    }
}