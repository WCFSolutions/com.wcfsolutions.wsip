<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the content item search form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class ContentItemSearchForm extends AbstractForm {
	// system
	public $templateName = 'contentItemSearch';
	
	/**
	 * given search query
	 * 
	 * @var	string
	 */
	public $query = '';
	
	/**
	 * list of results
	 * 
	 * @var	array
	 */
	public $result = array();
	
	/**
	 * existing search id
	 * 
	 * @var	integer
	 */
	public $searchID = 0;
	
	/**
	 * existing search data
	 * 
	 * @var	array
	 */
	public $searchData = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['q'])) $this->query = StringUtil::trim($_REQUEST['q']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->query)) {
			throw new UserInputException('query');
		}
		
		if (StringUtil::length($this->query) < 3 || strpos($this->query, '%') !== false || strpos($this->query, '_') !== false) {
			throw new UserInputException('query', 'invalid');
		}
		
		// search
		$this->result = ContentItem::search($this->query);
		if (!count($this->result)) {
			throw new NamedUserException(WCF::getLanguage()->get('wsip.contentItem.search.error.noMatches', array('$query' => StringUtil::encodeHTML($this->query))));
		}
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		try {
			parent::submit();
		}
		catch (NamedUserException $e) {
			WCF::getTPL()->assign('errorMessage', $e->getMessage());
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save result in database
		$this->searchData = array('query' => $this->query, 'result' => $this->result);
		$sql = "INSERT INTO	wcf".WCF_N."_search
					(userID, searchData, searchDate, searchType)
			VALUES		(".WCF::getUser()->userID.",
					'".escapeString(serialize($this->searchData))."',
					".TIME_NOW.",
					'contentItem')";
		WCF::getDB()->sendQuery($sql);
		$this->searchID = WCF::getDB()->getInsertID();
		$this->saved();
		
		// forward to result page
		HeaderUtil::redirect('index.php?page=ContentItemSearchResult&searchID='.$this->searchID.'&highlight='.urlencode($this->query).SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'query' => $this->query
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {		
		// set active page menu item
		PageMenu::setActiveMenuItem('wsip.header.menu.contentItem');
		
		if (!count($_POST) && !empty($this->query)) {
			$this->submit();
		}
		
		parent::show();
	}
}
?>