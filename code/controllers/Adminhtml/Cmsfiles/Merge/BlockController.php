<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 26.05.14
 */

class Jarlssen_CmsFiles_Adminhtml_Cmsfiles_Merge_BlockController extends Jarlssen_CmsFiles_Controller_Abstract
{
    protected function path($identifier)
    {
        return Mage::helper('jarlssen_cmsfiles')->getPath(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_BLOCK_PATH, $identifier);
    }

    protected function model()
    {
        return Mage::getModel('cms/block');
    }
}