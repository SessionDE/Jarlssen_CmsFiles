<?php

class Jarlssen_CmsFiles_Helper_Hierarchy extends Enterprise_Cms_Helper_Hierarchy
{

    const REGISTRY_DISABLE_HIERARCHY = 'disable_cms_hierarchy';

    /**
     * Check is Enabled Hierarchy Functionality
     *
     * Modifications: Made it possible to disable hierarchy when saving CMS Page model.
     * Prevents hierarchy nodes from begin removed when importing content from file to db.
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (Mage::registry(self::REGISTRY_DISABLE_HIERARCHY)) {
            return false;
        }

        return parent::isEnabled();
    }

}