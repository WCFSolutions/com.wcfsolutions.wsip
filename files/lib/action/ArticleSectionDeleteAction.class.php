<?php
// wsip imports
require_once(WSIP_DIR.'lib/action/AbstractArticleSectionAction.class.php');

/**
 * Deletes an article section.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class ArticleSectionDeleteAction extends AbstractArticleSectionAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		if (!$this->article->isDeletable($this->category)) {
			throw new PermissionDeniedException();
		}

		// delete article
		if ($this->sectionID == $this->article->firstSectionID) {
			$this->article->delete();
			$this->category->updateArticles(-1); // maybe use $category->refresh() here..

			// reset box tab cache
			require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
			BoxTab::resetBoxTabCacheByBoxTabType('articles');
		}
		// delete section
		else {
			$this->section->delete();
		}

		// refresh category entries
		$this->category->refresh();

		// reset cache
		WCF::getCache()->clearResource('categoryData', true);
		$this->executed();

		// forward to page
		if ($this->sectionID == $this->article->firstSectionID) {
			HeaderUtil::redirect('index.php?page=ArticleOverview&categoryID='.$this->article->categoryID.SID_ARG_2ND_NOT_ENCODED);
		}
		else {
			HeaderUtil::redirect('index.php?page=Article&sectionID='.$this->article->firstSectionID.SID_ARG_2ND_NOT_ENCODED);
		}
		exit;
	}
}
?>