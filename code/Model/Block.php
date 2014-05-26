<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 23.04.14
 */

class Jarlssen_CmsFiles_Model_Block extends Mage_Cms_Model_Block
{
    // correct an annoying upstream omission
    protected $_eventPrefix = 'cms_block';
}