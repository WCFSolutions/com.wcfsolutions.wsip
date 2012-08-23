<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/type/AbstractBoxTabType.class.php');

/**
 * Represents the content items box tab type.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.box.tab.type
 * @category	Infinite Portal
 */
class ContentItemsBoxTabType extends AbstractBoxTabType {
	/**
	 * list of content items
	 *
	 * @var	array<ContentItem>
	 */
	public $contentItems = null;

	/**
	 * @see	BoxTabType::cache()
	 */
	public function cache(BoxTab $boxTab) {
		if ($this->contentItems === null) {
			$this->contentItems = array(0 => new ContentItem(null, array('contentItemID' => 0))) + ContentItem::getContentItems();
		}
	}

	/**
	 * @see	BoxTabType::getData()
	 */
	public function getData(BoxTab $boxTab) {
		return $this->contentItems[$boxTab->contentItemID];
	}

	/**
	 * @see	BoxTabType::isAccessible()
	 */
	public function isAccessible(BoxTab $boxTab) {
		return (isset($this->contentItems[$boxTab->contentItemID]));
	}

	/**
	 * @see	BoxTabType::getTemplateName()
	 */
	public function getTemplateName() {
		return 'contentItemsBoxTabType';
	}
}
?>