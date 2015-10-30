<?php
/**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 26.05.14
 */

abstract class Jarlssen_CmsFiles_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    abstract protected function path($identifier, $storeId = null);

    abstract protected function model();

    public function fileAction()
    {
        $id = $this->getRequest()->getParam('id');
        if($id and $obj = $this->model()->load($id)) {

            if ($this->getRequest()->getParam('store_id')) {
                $store = Mage::app()->getStore($this->getRequest()->getParam('store_id'));
                $storeIds = array($store->getId());
            } else {
                $storeIds = $obj->getStoreId();
            }

            foreach ($storeIds as $storeId) {
                try {
                    $path = $this->path($obj, $storeId);
                    if(!is_writable(dirname($path)))
                        throw new Exception("$path directory is not writable");
                    if(file_put_contents($path, $obj->getContent()))
                        Mage::getModel('adminhtml/session')->addSuccess("$path successfully written");
                    touch($path, strtotime($obj->getUpdateTime()));
                } catch(Exception $e) {
                    Mage::getModel('adminhtml/session')->addError($e->getMessage());
                }
            }
        } else {
            throw new Exception("Could not load CMS model");
        }
        $this->_redirectReferer();
    }

    public function dbAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Mage_Cms_Model_Page $obj */
        if($id and $obj = $this->model()->load($id)) {

            // get the latest updated file
            $storeIds = array_merge(array(0), $obj->getStoreId());
            $path = null;
            foreach ($storeIds as $storeId) {
                $tempPath = $this->path($obj, $storeId);
                if (is_null($path) OR filemtime($tempPath) > filemtime($path)) {
                    $path = $tempPath;
                }
            }

            if ($path) {
                try {
                    if(!is_readable($path))
                        throw new Exception("$path is not readable");
                    $content = file_get_contents($path);
                    if(!$content)
                        throw new Exception("$path contains no content");

                    $obj->setContent($content);

                    Mage::register(Jarlssen_CmsFiles_Helper_Hierarchy::REGISTRY_DISABLE_HIERARCHY, true);
                    $obj->save();
                    Mage::unregister(Jarlssen_CmsFiles_Helper_Hierarchy::REGISTRY_DISABLE_HIERARCHY);

                    Mage::getModel('adminhtml/session')->addSuccess("{$obj->getIdentifier()} successfully updated from file");

                } catch(Exception $e) {
                    Mage::getModel('adminhtml/session')->addError($e->getMessage());
                }
            }

        } else {
            throw new Exception("Could not load CMS model");
        }
        $this->_redirectReferer();
    }
}