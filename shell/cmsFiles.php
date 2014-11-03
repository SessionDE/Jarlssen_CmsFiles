<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 02.05.14
 */

require_once 'abstract.php';

class Jarlssen_CmsFiles_Script extends Mage_Shell_Abstract
{
    protected $_action = null;

    protected $_type = null;

    protected $_identifier = null;

    protected function _parseArgs()
    {
        $this->_action = $_SERVER['argv'][1];
        $this->_type = $_SERVER['argv'][2];
        $this->_identifier = $_SERVER['argv'][3];
    }

    protected function _validate()
    {
        parent::_validate();
        if(!in_array($this->_action, array('pull','push','diff')))
            print("Invalid action\n") and die($this->usageHelp());
        if(!is_null($this->_type) and !in_array($this->_type, array('page','block')))
            print("Invalid type\n") and die($this->usageHelp());
    }

    protected function getObjects()
    {
        $connection = Mage::getModel('core/resource')->getConnection('read');
        $helper     = Mage::helper('jarlssen_cmsfiles');
        $results    = array();

        if(is_null($this->_type) or $this->_type == 'page') {
            $select = $connection->select()
                ->from('cms_page', array(
                    '*',
                    "trim('cms/page') as model",
                    'page_id as id'
                ));

            $select->joinInner(
                array('ps' => Mage::getModel('core/resource')->getTableName('cms/page_store')),
                'ps.page_id = cms_page.page_id',
                array('store_id')
            );

            if(!is_null($this->_identifier))
                $select->where('identifier = ?', $this->_identifier);

            $results = $connection->fetchAll($select);

            foreach ($results as &$result) {
                $result['file_path'] = $helper->getPath(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_PAGE_PATH, $result['identifier'], $result['store_id']);
            }
        }

        if(is_null($this->_type) or $this->_type == 'block') {
            $select = $connection->select()
                ->from('cms_block', array(
                    '*',
                    "trim('cms/block') as model",
                    'block_id as id'
                ));

            $select->joinInner(
                array('bs' => Mage::getModel('core/resource')->getTableName('cms/block_store')),
                'bs.block_id = cms_block.block_id',
                array('store_id')
            );

            if(!is_null($this->_identifier))
                $select->where('identifier = ?', $this->_identifier);

            $results = $connection->fetchAll($select);

            foreach ($results as &$result) {
                $result['file_path'] = $helper->getPath(Jarlssen_CmsFiles_Helper_Data::CONF_CMS_BLOCK_PATH, $result['identifier'], $result['store_id']);
            }
        }

        return $results;
    }

    public function run()
    {
        switch($this->_action) {
            case 'pull':
                foreach($this->getObjects() as $cms) {
                    echo "Writing {$cms['file_path']}\n";
                    file_put_contents($cms['file_path'], $cms['content']);
                }
            break;
            case 'push':
                foreach($this->getObjects() as $cms) {
                    if(file_exists($cms['file_path'])) {
                        $fileContent = file_get_contents($cms['file_path']);
                        Mage::getModel($cms['model'])
                            //->load($cms['identifier'], 'identifier')
                            ->load($cms['id'])
                            ->setContent($fileContent)
                            ->save();
                    } else
                        echo "Warning: {$cms['file_path']} does not exist, cannot push\n";
                }
            break;
            case 'diff':
                $cols = `tput cols`;
                foreach($this->getObjects() as $cms) {
                    if(file_exists($cms['file_path'])) {
                        echo "------ comparing {$cms['model']} identifier={$cms['identifier']}\n";
                        $sdiff = proc_open("sdiff -dWs '{$cms['file_path']}' - -w$cols", array(
                            0 => array('pipe','r'), //stdin
                            1 => array('pipe', 'w'), //stdout
                            2 => array('pipe', 'a'), //stdout
                        ), $pipes);
                        fwrite($pipes[0], $cms['content']);
                        fclose($pipes[0]);
                        echo stream_get_contents($pipes[1]);
                        fclose($pipes[1]);
                        proc_close($sdiff);
                    }
                }
            break;
        }
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f cmsFiles.php -- [action] [type] [identifier]

  [action] is one of
    pull        Write CMS content pages with content from database entries
    push        Update database content field with page content
    diff        Compare differences between database and file content
    help        This help

    This argument is mandatory.

  [type] is one of
    page        CMS Page
    block       CMS Static Block

  [identifier]  The CMS page or block identifier to operate on.

    This argument may be omitted, in which case the action will be performed
    on all pages or blocks as specified by the [type] argument. If [type] is
    also omitted, both pages and blocks will be operated on.\n
USAGE;
    }
}

$shell = new Jarlssen_CmsFiles_Script();
$shell->run();
