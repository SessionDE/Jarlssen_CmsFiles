<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 26.05.14
 */

abstract class Jarlssen_CmsFiles_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    abstract protected function path($identifier);

    abstract protected function model();

    public function fileAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            if($id and $obj = $this->model()->load($id)) {
                $path = $this->path($obj->getIdentifier());
                if(!is_writable(dirname($path)))
                    throw new Exception("$path directory is not writable");
                if(file_put_contents($path, $obj->getContent()))
                    Mage::getModel('adminhtml/session')->addSuccess("$path successfully written");
                touch($path, strtotime($obj->getUpdateTime()));
            } else
                throw new Exception("Could not load CMS model");
        } catch(Exception $e) {
            Mage::getModel('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirectReferer();
    }

    public function dbAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            if($id and $obj = $this->model()->load($id)) {
                $path = $this->path($obj->getIdentifier());
                if(!is_readable($path))
                    throw new Exception("$path is not readable");
                $content = file_get_contents($path);
                if(!$content)
                    throw new Exception("$path contains no content");
                $obj->setContent($content)->save();
                Mage::getModel('adminhtml/session')->addSuccess("{$obj->getIdentifier()} successfully updated from file");
            } else
                throw new Exception("Could not load CMS model");
        } catch(Exception $e) {
            Mage::getModel('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirectReferer();
    }
}