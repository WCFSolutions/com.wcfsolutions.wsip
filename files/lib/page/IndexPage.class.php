<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the index page of the portal.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class IndexPage extends AbstractPage {
	// system
	public $templateName = 'index';
	
	/**
	 * @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// register required positions
		BoxLayout::registerPositions(array('index'));
		
		// change box layout
		BoxLayoutManager::changeBoxLayout(INDEX_BOX_LAYOUT);
		
		// assign variables
		WCF::getTPL()->assign(array(
			'allowSpidersToIndexThisPage' => true
		));
	}
}
?>