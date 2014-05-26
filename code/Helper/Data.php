<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 23.05.14
 */

class Jarlssen_CmsFiles_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONF_CMS_PAGE_PATH = "cms/path/pages";

    const CONF_CMS_BLOCK_PATH = "cms/path/blocks";

    const CONF_CMS_DEV_OVERRIDE = "cms/dev/file_override";

    const STATE_NO_FILE = 'nofile';
    const STATE_FILE_NEWER = 'file';
    const STATE_DB_NEWER = 'db';

    public function getPath($cfg, $id)
    {
        $path = Mage::getStoreConfig($cfg) . DS . $id;
        if($path[0] != '/')
            $path = Mage::getBaseDir() . DS . $path;
        return $path;
    }

}