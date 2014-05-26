<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 22.04.14
 */

class Jarlssen_CmsFiles_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * ACL validation before html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('system/index') and Mage::getStoreConfig('cms/dev/file_override')) {
            return <<<EOF
<div class="notification-global">
    <strong>CMS Developer Override is enabled</strong>
    This should only be active on a developer setup
</div>
EOF;
        }
        return '';
    }
}
