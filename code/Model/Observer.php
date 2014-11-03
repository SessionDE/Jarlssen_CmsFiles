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
            // Mage::log(strtotime($obj->getUpdateTime()));
            // Mage::log(filemtime($path));

            if((int)filemtime($path) > (int)strtotime($obj->getUpdateTime()))
                $obj->setMergeState(Jarlssen_CmsFiles_Helper_Data::STATE_FILE_NEWER);
            else
                $obj->setMergeState(Jarlssen_CmsFiles_Helper_Data::STATE_DB_NEWER);

            if((bool)Mage::getStoreConfig(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_DEV_OVERRIDE)) {
                $obj->setContent(file_get_contents($path));
            }
        } else {
            $obj->setMergeState(Jarlssen_CmsFiles_Helper_Data::STATE_NO_FILE);
        }
    }


    public function loadCmsPage(Varien_Event_Observer $observer)
    {
        /** @var Mage_Cms_Model_Page $page */
        $page = $observer->getEvent()->getDataObject();
        if ($page->getIdentifier()) {
            $this->annotate($page, $this->_getPath(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_PAGE_PATH, $page));
        }
    }

    public function loadCmsBlock(Varien_Event_Observer $observer)
    {
        /** @var Mage_Cms_Model_Block $page */
        $block = $observer->getEvent()->getDataObject();
        if ($block->getIdentifier()) {
            $this->annotate($block, $this->_getPath(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_BLOCK_PATH, $block));
        }
    }

    /*
     * Check the file for current store id and default store id
     *
     * @param string $configPath
     * @param Mage_Core_Model_Abstract $obj
     * @return string
     */
    protected function _getPath($configPath, $obj)
    {
        $helper = Mage::helper('jarlssen_cmsfiles');

        // if admin panel, get the latest updated file
        if (Mage::app()->getStore()->getId() == 0) {
            $filename = null;
            foreach (array_merge(array(0), $obj->getStoreId()) as $storeId) {
                $path = $helper->getPath($configPath, $obj, $storeId, true);
                if (is_null($filename) OR filemtime($path) > filemtime($filename)) {
                    $filename = $path;
                }
            }
        } else {
            $filename = $helper->getPath($configPath, $obj, Mage::app()->getStore()->getId(), true);
            if (!$filename) {
                $filename = $helper->getPath($configPath, $obj, 0);
            }
        }

        return $filename;
    }
}
