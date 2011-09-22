<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents an article section.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article.section
 * @category	Infinite Portal
 */
class ArticleSection extends DatabaseObject {
	/**
	 * list of all article sections
	 * 
	 * @var	array<ArticleSection>
	 */
	protected static $sections = array();
	
	/**
	 * article section options
	 * 
	 * @var	array
	 */
	protected static $sectionSelect = null;

	/**
	 * Creates a new ArticleSection object.
	 * 
	 * @param	integer		$sectionID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($sectionID, $row = null) {
		if ($sectionID !== null) {
			$sql = "SELECT	*
				FROM 	wsip".WSIP_N."_article_section
				WHERE 	sectionID = ".$sectionID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}
	
	/**
	 * Creates the section select list.
	 * 
	 * @param	array		$ignoredCategories
	 * @return 	array
	 */
	public static function getSectionSelect($articleID, $ignoredSections = array()) {
		self::$sectionSelect = array();
		
		if (!isset(self::$sections[$articleID])) {
			self::$sections[$articleID] = array();
			
			$sql = "SELECT		sectionID, parentSectionID, subject
				FROM		wsip".WSIP_N."_article_section
				WHERE		articleID = ".$articleID."
				ORDER BY	parentSectionID, showOrder";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				self::$sections[$articleID][$row['parentSectionID']][] = new ArticleSection(null, $row);
			}
		}
		
		self::makeSectionSelect($articleID, 0, 0, $ignoredSections);
		
		return self::$sectionSelect;
	}
	
	/**
	 * Generates the section select list.
	 * 
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 * @param	array		$ignoredCategories
	 */
	protected static function makeSectionSelect($articleID, $parentID = 0, $depth = 0, $ignoredSections = array()) {
		if (!isset(self::$sections[$articleID][$parentID])) return;
		
		foreach (self::$sections[$articleID][$parentID] as $section) {
			if (in_array($section->sectionID, $ignoredSections)) continue;
			
			$subject = StringUtil::encodeHTML($section->subject);
			if ($depth > 0) $subject = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth). ' '.$subject;
			
			self::$sectionSelect[$section->sectionID] = $subject;
			self::makeSectionSelect($articleID, $section->sectionID, $depth + 1, $ignoredSections);
		}
	}
}
?>