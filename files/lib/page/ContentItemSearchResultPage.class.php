<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/util/SearchResultTextParser.class.php');
require_once(WCF_DIR.'lib/data/message/util/KeywordHighlighter.class.php');
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the content item search result page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ContentItemSearchResultPage extends MultipleLinkPage {
	// system
	public $templateName = 'contentItemSearchResult';
	
	/**
	 * highlight string
	 * 
	 * @var	string
	 */
	public $highlight = '';
	
	/**
	 * search id
	 * 
	 * @var	integer
	 */
	public $searchID = 0;
	
	/**
	 * search query
	 * 
	 * @var	string
	 */
	public $query = null;
	
	/**
	 * search results
	 * 
	 * @var	array
	 */
	public $result = null;
	
	/**
	 * list of content items
	 * 
	 * @var	array
	 */
	public $contentItems = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get search
		if (isset($_REQUEST['searchID'])) $this->searchID = intval($_REQUEST['searchID']);
		$sql = "SELECT 	*
			FROM	wcf".WCF_N."_search
			WHERE	searchID = ".$this->searchID."
				AND userID = ".WCF::getUser()->userID;
		$search = WCF::getDB()->getFirstRow($sql);
		if (empty($search['searchID']) || ($search['userID'] && $search['userID'] != WCF::getUser()->userID)) {
			throw new IllegalLinkException();
		}
		
		// get search data
		$search = unserialize($search['searchData']);
		$this->query = $search['query'];
		$this->result = $search['result'];
		
		// get highlight string
		if (isset($_REQUEST['highlight'])) $this->highlight = $_REQUEST['highlight'];
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return count($this->result);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->readItems();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'query' => $this->query,
			'contentItems' => $this->contentItems,
			'searchID' => $this->searchID,
			'highlight' => $this->highlight
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active page menu item
		PageMenu::setActiveMenuItem('wsip.header.menu.contentItem');
		
		parent::show();
	}
	
	/**
	 * Gets the items for the current page.
	 */
	protected function readItems() {
		for ($i = $this->startIndex - 1; $i < $this->endIndex; $i++) {
			// get content item id
			$contentItemID = $this->result[$i];
			
			// get content item
			$contentItem = new ContentItem($contentItemID);
			
			// add content item
			$this->contentItems[] = array(
				'contentItemID' => $contentItemID,
				'title' => KeywordHighlighter::doHighlight(StringUtil::encodeHTML(WCF::getLanguage()->get('wsip.contentItem.'.$contentItem->contentItem))),
				'text' => ($contentItem->isPage() ? SearchResultTextParser::parse(WCF::getLanguage()->get('wsip.contentItem.'.$contentItem->contentItem.'.text')) : '')
			);
		}
	}
}
?>