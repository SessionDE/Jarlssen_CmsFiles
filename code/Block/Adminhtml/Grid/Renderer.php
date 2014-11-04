<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 23.05.14
 */

class Jarlssen_CmsFiles_Block_Adminhtml_Grid_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $model = $row instanceof Mage_Cms_Model_Block ? "block" : "page";

        $html = '';
        // foreach ($row->getStoreId() as $storeId) {
            $fileUrl = Mage::helper('adminhtml')->getUrl("*/cmsfiles_merge_$model/file", array(
                'id' => $row->getId(),
                // 'store_id' => $storeId,
            ));
            $html .= "<a href=\"$fileUrl\">&gt;&nbsp;Files</a>";

            if($row->getMergeState() != Jarlssen_CmsFiles_Helper_Data::STATE_NO_FILE) {
                $dbUrl = Mage::helper('adminhtml')->getUrl("*/cmsfiles_merge_$model/db", array(
                    'id' => $row->getId(),
                    // 'store_id' => $storeId,
                ));
                $html .= "<br><a href=\"$dbUrl\">&gt;&nbsp;DB</a>";
            }

            $html .= '<br>';
        // }

        return $html;
    }
}