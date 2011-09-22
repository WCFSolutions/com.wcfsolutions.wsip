DROP TABLE IF EXISTS wsip1_1_article;
CREATE TABLE wsip1_1_article (
	articleID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	categoryID INT(10) NOT NULL DEFAULT 0,
	languageID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL,
	username VARCHAR(255) NOT NULL DEFAULT '',
	subject VARCHAR(255) NOT NULL DEFAULT '',
	teaser TINYTEXT,
	firstSectionID INT(10) NOT NULL DEFAULT 0,
	time INT(10) NOT NULL DEFAULT 0,
	views MEDIUMINT(7) NOT NULL DEFAULT 0,
	ratings INT(10) NOT NULL DEFAULT 0,
	rating INT(7) NOT NULL DEFAULT 0,
	comments SMALLINT(5) NOT NULL DEFAULT 0,
	enableComments TINYINT(1) NOT NULL DEFAULT 1,
	ipAddress VARCHAR(15) NOT NULL DEFAULT '',
	KEY (categoryID),
	KEY (languageID),
	KEY (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_article_section;
CREATE TABLE wsip1_1_article_section (
	sectionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	parentSectionID INT(10) NOT NULL DEFAULT 0,
	articleID INT(10) NOT NULL DEFAULT 0,
	subject VARCHAR(255) NOT NULL DEFAULT '',
	message TEXT NULL,
	showOrder INT(10) NOT NULL DEFAULT 0,
	attachments SMALLINT(5) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	FULLTEXT KEY (subject, message),
	KEY (articleID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_category;
CREATE TABLE wsip1_1_category (
	categoryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	parentID INT(10) NOT NULL DEFAULT 0,
	category VARCHAR(255) NOT NULL DEFAULT '',
	allowDescriptionHtml TINYINT(1) NOT NULL DEFAULT 0,
	time INT(10) NOT NULL DEFAULT 0,
	newsEntries INT(10) NOT NULL DEFAULT 0,
	articles INT(10) NOT NULL DEFAULT 0,
	showOrder INT(10) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_category_moderator;
CREATE TABLE wsip1_1_category_moderator (
	categoryID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL DEFAULT 0,
	groupID INT(10) NOT NULL DEFAULT 0,
	canEditNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canReadDeletedNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteNewsEntryCompletely TINYINT(1) NOT NULL DEFAULT -1,
	canEnableNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canMoveNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canEditArticle TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteArticle TINYINT(1) NOT NULL DEFAULT -1,
	KEY (userID),
	KEY (groupID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_category_to_group;
CREATE TABLE wsip1_1_category_to_group (
	categoryID INT(10) NOT NULL DEFAULT 0,
	groupID INT(10) NOT NULL DEFAULT 0,
	canViewCategory TINYINT(1) NOT NULL DEFAULT -1,
	canEnterCategory TINYINT(1) NOT NULL DEFAULT -1,
	canReadNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canReadOwnNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canAddNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canEditOwnNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteOwnNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canAddNewsEntryWithoutModeration TINYINT(1) NOT NULL DEFAULT -1,
	canSetNewsTags TINYINT(1) NOT NULL DEFAULT -1,
	canDownloadNewsAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canViewNewsAttachmentPreview TINYINT(1) NOT NULL DEFAULT -1,
	canUploadNewsAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canStartNewsPoll TINYINT(1) NOT NULL DEFAULT -1,
	canVoteNewsPoll TINYINT(1) NOT NULL DEFAULT -1,
	canReadArticle TINYINT(1) NOT NULL DEFAULT -1,
	canReadOwnArticle TINYINT(1) NOT NULL DEFAULT -1,
	canAddArticle TINYINT(1) NOT NULL DEFAULT -1,
	canEditOwnArticle TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteOwnArticle TINYINT(1) NOT NULL DEFAULT -1,
	canSetArticleTags TINYINT(1) NOT NULL DEFAULT -1,
	canDownloadArticleSectionAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canViewArticleSectionAttachmentPreview TINYINT(1) NOT NULL DEFAULT -1,
	canUploadArticleSectionAttachment TINYINT(1) NOT NULL DEFAULT -1,
	PRIMARY KEY (groupID, categoryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_category_to_publication_type;
CREATE TABLE wsip1_1_category_to_publication_type (
	categoryID INT(10) NOT NULL DEFAULT 0,
	publicationType VARCHAR(255) NOT NULL DEFAULT '',
	UNIQUE KEY (categoryID, publicationType)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_category_to_user;
CREATE TABLE wsip1_1_category_to_user (
	categoryID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL DEFAULT 0,
	canViewCategory TINYINT(1) NOT NULL DEFAULT -1,
	canEnterCategory TINYINT(1) NOT NULL DEFAULT -1,
	canReadNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canReadOwnNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canAddNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canEditOwnNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteOwnNewsEntry TINYINT(1) NOT NULL DEFAULT -1,
	canAddNewsEntryWithoutModeration TINYINT(1) NOT NULL DEFAULT -1,
	canSetNewsTags TINYINT(1) NOT NULL DEFAULT -1,
	canDownloadNewsAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canViewNewsAttachmentPreview TINYINT(1) NOT NULL DEFAULT -1,
	canUploadNewsAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canStartNewsPoll TINYINT(1) NOT NULL DEFAULT -1,
	canVoteNewsPoll TINYINT(1) NOT NULL DEFAULT -1,
	canReadArticle TINYINT(1) NOT NULL DEFAULT -1,
	canReadOwnArticle TINYINT(1) NOT NULL DEFAULT -1,
	canAddArticle TINYINT(1) NOT NULL DEFAULT -1,
	canEditOwnArticle TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteOwnArticle TINYINT(1) NOT NULL DEFAULT -1,
	canSetArticleTags TINYINT(1) NOT NULL DEFAULT -1,
	canDownloadArticleSectionAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canViewArticleSectionAttachmentPreview TINYINT(1) NOT NULL DEFAULT -1,
	canUploadArticleSectionAttachment TINYINT(1) NOT NULL DEFAULT -1,
	PRIMARY KEY (userID, categoryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_content_item;
CREATE TABLE wsip1_1_content_item (
	contentItemID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	parentID INT(10) NOT NULL DEFAULT 0,
	contentItem VARCHAR(255) NOT NULL DEFAULT '',
	contentItemType TINYINT(1) NOT NULL DEFAULT 0,
	externalURL VARCHAR(255) NOT NULL DEFAULT '',
	icon VARCHAR(255) NOT NULL DEFAULT '',
	publishingStartTime INT(10) NOT NULL DEFAULT 0,
	publishingEndTime INT(10) NOT NULL DEFAULT 0,
	styleID INT(10) NOT NULL DEFAULT 0,
	enforceStyle TINYINT(1) NOT NULL DEFAULT 0,
	boxLayoutID INT(10) NOT NULL DEFAULT 0,
	allowSpidersToIndexThisPage TINYINT(1) NOT NULL DEFAULT 1,
	showOrder INT(10) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_content_item_box;
CREATE TABLE wsip1_1_content_item_box (
	boxID INT(10) NOT NULL DEFAULT 0,
	contentItemID INT(10) NOT NULL DEFAULT 0,
	showOrder INT(10) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_content_item_to_group;
CREATE TABLE wsip1_1_content_item_to_group (
	contentItemID INT(10) NOT NULL DEFAULT 0,
	groupID INT(10) NOT NULL DEFAULT 0,
	canViewContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canEnterContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canViewHiddenContentItem TINYINT(1) NOT NULL DEFAULT -1,
	PRIMARY KEY (groupID, contentItemID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_content_item_to_user;
CREATE TABLE wsip1_1_content_item_to_user (
	contentItemID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL DEFAULT 0,
	canViewContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canEnterContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canViewHiddenContentItem TINYINT(1) NOT NULL DEFAULT -1,
	PRIMARY KEY (userID, contentItemID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_news_entry;
CREATE TABLE wsip1_1_news_entry (
	entryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	categoryID INT(10) NOT NULL DEFAULT 0,
	languageID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL,
	username VARCHAR(255) NOT NULL DEFAULT '',
	subject VARCHAR(255) NOT NULL DEFAULT '',
	message TEXT NULL,
	teaser TINYTEXT,
	time INT(10) NOT NULL DEFAULT 0,
	publishingTime INT(10) NOT NULL DEFAULT 0,
	everEnabled TINYINT(1) NOT NULL DEFAULT 1,
	isDisabled TINYINT(1) NOT NULL DEFAULT 0,
	isDeleted TINYINT(1) NOT NULL DEFAULT 0,
	deleteTime INT(10) NOT NULL DEFAULT 0,
	deletedBy VARCHAR(255) NOT NULL DEFAULT '',
	deletedByID INT(10) NOT NULL DEFAULT 0,
	deleteReason TEXT,
	views MEDIUMINT(7) NOT NULL DEFAULT 0,
	ratings INT(10) NOT NULL DEFAULT 0,
	rating INT(7) NOT NULL DEFAULT 0,
	comments SMALLINT(5) NOT NULL DEFAULT 0,
	attachments SMALLINT(5) NOT NULL DEFAULT 0,
	pollID INT(10) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	enableComments TINYINT(1) NOT NULL DEFAULT 1,
	ipAddress VARCHAR(15) NOT NULL DEFAULT '',
	FULLTEXT KEY (subject, message),
	KEY (categoryID),
	KEY (languageID),
	KEY (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_publication_object_comment;
CREATE TABLE wsip1_1_publication_object_comment (
	commentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	publicationObjectID INT(10) NOT NULL DEFAULT 0,
	publicationType VARCHAR(255) NOT NULL DEFAULT '',
	userID INT(10) NOT NULL DEFAULT 0,
	username VARCHAR(255) NOT NULL DEFAULT '',
	comment TEXT NULL,
	time INT(10) NOT NULL DEFAULT 0,
	ipAddress VARCHAR(15) NOT NULL DEFAULT '',
	KEY (publicationObjectID, publicationType)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_publication_object_subscription;
CREATE TABLE wsip1_1_publication_object_subscription (
	userID INT(10) NOT NULL DEFAULT 0,
	publicationObjectID INT(10) NOT NULL DEFAULT 0,
	publicationType VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (userID, publicationObjectID, publicationType)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_publication_type;
CREATE TABLE wsip1_1_publication_type (
	publicationTypeID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL,
	publicationType VARCHAR(255) NOT NULL DEFAULT '' UNIQUE KEY,
	classFile VARCHAR(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wsip1_1_user;
CREATE TABLE wsip1_1_user (
	userID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	newsEntries SMALLINT(5) NOT NULL DEFAULT 0,
	KEY (newsEntries)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;