<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a content item.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ContentItemPage extends AbstractPage {
	// system
	public $templateName = 'contentItem';

	/**
	 * content item id
	 *
	 * @var	integer
	 */
	public $contentItemID = 0;

	/**
	 * content item object
	 *
	 * @var	ContentItem
	 */
	public $contentItem = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get item
		if (isset($_REQUEST['contentItemID'])) $this->contentItemID = intval($_REQUEST['contentItemID']);
		$this->contentItem = new ContentItem($this->contentItemID);
		$this->contentItem->enter();

		// redirect to external url
		if ($this->contentItem->isExternalLink()) {
			// forward
			HeaderUtil::redirect($this->contentItem->externalURL, false);
			exit;
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'metaDescription' => $this->contentItem->getMetaDescription(),
			'metaKeywords' => $this->contentItem->getMetaKeywords(),
			'contentItem' => $this->contentItem,
			'contentItemID' => $this->contentItemID
		));

		if ($this->contentItem->allowSpidersToIndexThisPage) {
			WCF::getTPL()->assign('allowSpidersToIndexThisPage', true);
		}
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// change box layout
		BoxLayoutManager::changeBoxLayout($this->contentItem->boxLayoutID);

		parent::show();
	}
}
?>