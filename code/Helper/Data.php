<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 23.05.14
 */

class Jarlssen_CmsFiles_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONF_CMS_PAGE_PATH           = 'cms/path/pages';
    const CONF_CMS_BLOCK_PATH          = 'cms/path/blocks';
    const CONF_CMS_DEV_OVERRIDE        = 'cms/dev/file_override';
    const CONF_SAVE_IN_TEMPLATE_FOLDER = 'cms/path/save_in_template_folder';

    const STATE_NO_FILE    = 'nofile';
    const STATE_FILE_NEWER = 'file';
    const STATE_DB_NEWER   = 'db';

    /**
     * Paths cache
     *
     * @var array
     */
    protected $_paths = array();

    /**
     * Template dir paths cache
     *
     * @var string
     */
    protected $_templateDir = array();

    /**
     * Default store id in frontend area
     *
     * @var int
     */
    protected $_defaultStoreId;

    /*
     * Retrieve the file path
     *
     * @param  Mage_Core_Model_Abstract|string $identifier
     * @return string
     */
    public function getPath($cfg, $identifier, $storeId = 0, $checkFileExists = false)
    {
        if (!isset($this->_paths[$cfg][$storeId])) {
            $this->_paths[$cfg][$storeId] = $this->_getPath($cfg, $storeId);
            Mage::getModel('core/config_options')->createDirIfNotExists($this->_paths[$cfg][$storeId]);
        }

        if ($identifier instanceof Varien_Object) {
            $identifier = $identifier->getIdentifier();
        }

        $filename = $identifier . '_' . $storeId;

        $path = $this->_paths[$cfg][$storeId] . DS . $filename . '.phtml';

        if ($checkFileExists AND !file_exists($path)) {
            $path = '';
        }

        return $path;
    }

    /**
     * Retrieve the path where to save content
     *
     * @param  string $cfg Config node path
     * @param  int $storeId
     * @return string
     */
    protected function _getPath($cfg, $storeId)
    {
        $path = Mage::getStoreConfig($cfg);

        // absolute path
        if($path[0] == '/') {
            return $path;
        }

        if (Mage::getStoreConfigFlag(self::CONF_SAVE_IN_TEMPLATE_FOLDER)) {
            $basePath = $this->_getTemplateDir($storeId);
        } else {
            $basePath = Mage::getBaseDir();
        }

        $path = $basePath . DS . $path;

        return $path;
    }

    /**
     * Retrieve the path to the design template directory
     *
     * @param int|null $storeId
     * @return string
     */
    protected function _getTemplateDir($storeId)
    {
        if ($storeId == 0) {
            $storeId = $this->_getDefaultFronendStoreId();
        }

        if (!isset($this->_templateDir[$storeId])) {
            $package = Mage::getStoreConfig('design/package/name', $storeId);
            $theme   = Mage::getStoreConfig('design/theme/template', $storeId);

            if (empty($theme)) {
                $theme = Mage::getStoreConfig('design/theme/default', $storeId);
            }

            if (empty($theme)) {
                $theme = Mage_Core_Model_Design_Package::DEFAULT_THEME;
            }

            $this->_templateDir[$storeId] = Mage::getDesign()->getBaseDir(array(
                '_area'    => Mage_Core_Model_App_Area::AREA_FRONTEND,
                '_package' => $package,
                '_theme'   => $theme,
                '_type'    => null,
            )) . DS . 'template';
        }
        return $this->_templateDir[$storeId];
    }

    /**
     * Retrieve default store id in frontend area
     *
     * @return string
     */
    protected function _getDefaultFronendStoreId()
    {
        if (is_null($this->_defaultStoreId)) {
            $defaultWebsite = null;
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getIsDefault()) {
                    $this->_defaultStoreId = $website->getDefaultStore()->getId();
                    break;
                }
            }
        }
        return $this->_defaultStoreId;
    }
}