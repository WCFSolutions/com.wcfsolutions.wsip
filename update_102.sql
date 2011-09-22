-- articles
ALTER TABLE wsip1_1_article ADD ratings INT(10) NOT NULL DEFAULT 0 AFTER views;
ALTER TABLE wsip1_1_article ADD rating INT(7) NOT NULL DEFAULT 0 AFTER ratings;
ALTER TABLE wsip1_1_article ADD enableComments TINYINT(1) NOT NULL DEFAULT 1 AFTER comments;
ALTER TABLE wsip1_1_article ADD ipAddress VARCHAR(15) NOT NULL DEFAULT '' AFTER enableComments;
ALTER TABLE wsip1_1_article ADD KEY (categoryID);
ALTER TABLE wsip1_1_article ADD KEY (languageID);

-- articles (sections)
ALTER TABLE wsip1_1_article_section ADD KEY (articleID);
ALTER TABLE wsip1_1_article_section DROP userID;
ALTER TABLE wsip1_1_article_section DROP username;
ALTER TABLE wsip1_1_article_section DROP time;
ALTER TABLE wsip1_1_article_section DROP ipAddress;

-- categories
ALTER TABLE wsip1_1_category DROP links;
ALTER TABLE wsip1_1_category_moderator DROP canEditLink;
ALTER TABLE wsip1_1_category_moderator DROP canDeleteLink;
ALTER TABLE wsip1_1_category_to_group ADD canStartNewsPoll TINYINT(1) NOT NULL DEFAULT -1 AFTER canUploadNewsAttachment;
ALTER TABLE wsip1_1_category_to_group ADD canVoteNewsPoll TINYINT(1) NOT NULL DEFAULT -1 AFTER canStartNewsPoll;
ALTER TABLE wsip1_1_category_to_group DROP canViewLink;
ALTER TABLE wsip1_1_category_to_group DROP canViewOwnLink;
ALTER TABLE wsip1_1_category_to_group DROP canVisitLink;
ALTER TABLE wsip1_1_category_to_group DROP canAddLink;
ALTER TABLE wsip1_1_category_to_group DROP canEditOwnLink;
ALTER TABLE wsip1_1_category_to_group DROP canDeleteOwnLink;
ALTER TABLE wsip1_1_category_to_group DROP canSetLinkTags;
ALTER TABLE wsip1_1_category_to_group DROP canDownloadLinkAttachment;
ALTER TABLE wsip1_1_category_to_group DROP canViewLinkAttachmentPreview;
ALTER TABLE wsip1_1_category_to_group DROP canUploadLinkAttachment;
ALTER TABLE wsip1_1_category_to_user ADD canStartNewsPoll TINYINT(1) NOT NULL DEFAULT -1 AFTER canUploadNewsAttachment;
ALTER TABLE wsip1_1_category_to_user ADD canVoteNewsPoll TINYINT(1) NOT NULL DEFAULT -1 AFTER canStartNewsPoll;
ALTER TABLE wsip1_1_category_to_user DROP canViewLink;
ALTER TABLE wsip1_1_category_to_user DROP canViewOwnLink;
ALTER TABLE wsip1_1_category_to_user DROP canVisitLink;
ALTER TABLE wsip1_1_category_to_user DROP canAddLink;
ALTER TABLE wsip1_1_category_to_user DROP canEditOwnLink;
ALTER TABLE wsip1_1_category_to_user DROP canDeleteOwnLink;
ALTER TABLE wsip1_1_category_to_user DROP canSetLinkTags;
ALTER TABLE wsip1_1_category_to_user DROP canDownloadLinkAttachment;
ALTER TABLE wsip1_1_category_to_user DROP canViewLinkAttachmentPreview;
ALTER TABLE wsip1_1_category_to_user DROP canUploadLinkAttachment;

-- categories (structure)
ALTER TABLE wsip1_1_category ADD showOrder INT(10) NOT NULL DEFAULT 0;

UPDATE	wsip1_1_category category
SET	showOrder = (
		SELECT	position
		FROM	wsip1_1_category_structure
		WHERE	categoryID = category.categoryID
		LIMIT	1
	);

DROP TABLE IF EXISTS wsip1_1_category_structure;

-- content items (general)
ALTER TABLE wsip1_1_content_item ADD parentID INT(10) NOT NULL DEFAULT 0 AFTER contentItemID;
ALTER TABLE wsip1_1_content_item ADD contentItemType TINYINT(1) NOT NULL DEFAULT 0 AFTER contentItem;
ALTER TABLE wsip1_1_content_item ADD externalURL VARCHAR(255) NOT NULL DEFAULT '' AFTER contentItemType;
ALTER TABLE wsip1_1_content_item ADD icon VARCHAR(255) NOT NULL DEFAULT '' AFTER externalURL;
ALTER TABLE wsip1_1_content_item ADD publishingStartTime INT(10) NOT NULL DEFAULT 0 AFTER icon;
ALTER TABLE wsip1_1_content_item ADD publishingEndTime INT(10) NOT NULL DEFAULT 0 AFTER publishingStartTime;
ALTER TABLE wsip1_1_content_item ADD styleID INT(10) NOT NULL DEFAULT 0 AFTER publishingEndTime;
ALTER TABLE wsip1_1_content_item ADD enforceStyle TINYINT(1) NOT NULL DEFAULT 0 AFTER styleID;
ALTER TABLE wsip1_1_content_item ADD boxLayoutID INT(10) NOT NULL DEFAULT 0 AFTER enforceStyle;
ALTER TABLE wsip1_1_content_item ADD showOrder INT(10) NOT NULL DEFAULT 0 AFTER allowSpidersToIndexThisPage;

-- content items (boxes)
DROP TABLE IF EXISTS wsip1_1_content_item_box;
CREATE TABLE wsip1_1_content_item_box (
	boxID INT(10) NOT NULL DEFAULT 0,
	contentItemID INT(10) NOT NULL DEFAULT 0,
	showOrder INT(10) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- content items (group/user permissions)
DROP TABLE IF EXISTS wsip1_1_content_item_to_group;
CREATE TABLE wsip1_1_content_item_to_group (
	contentItemID INT(10) NOT NULL DEFAULT 0,
	groupID INT(10) NOT NULL DEFAULT 0,
	canViewContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canEnterContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canViewHiddenContentItem TINYINT(1) NOT NULL DEFAULT -1,
	PRIMARY KEY (groupID, contentItemID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- content items (moderator permissions)
DROP TABLE IF EXISTS wsip1_1_content_item_to_user;
CREATE TABLE wsip1_1_content_item_to_user (
	contentItemID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL DEFAULT 0,
	canViewContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canEnterContentItem TINYINT(1) NOT NULL DEFAULT -1,
	canViewHiddenContentItem TINYINT(1) NOT NULL DEFAULT -1,
	PRIMARY KEY (userID, contentItemID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- obsolete tables
DROP TABLE IF EXISTS wsip1_1_guestbook_entry;
DROP TABLE IF EXISTS wsip1_1_link;

-- news entries
ALTER TABLE wsip1_1_news_entry ADD publishingTime INT(10) NOT NULL DEFAULT 0 AFTER time;
ALTER TABLE wsip1_1_news_entry ADD ratings INT(10) NOT NULL DEFAULT 0 AFTER views;
ALTER TABLE wsip1_1_news_entry ADD rating INT(7) NOT NULL DEFAULT 0 AFTER ratings;
ALTER TABLE wsip1_1_news_entry ADD pollID INT(10) NOT NULL DEFAULT 0 AFTER attachments;
ALTER TABLE wsip1_1_news_entry ADD enableComments TINYINT(1) NOT NULL DEFAULT 1 AFTER enableBBCodes;
ALTER TABLE wsip1_1_news_entry ADD KEY (categoryID);
ALTER TABLE wsip1_1_news_entry ADD KEY (languageID);

-- publication object comments
ALTER TABLE wsip1_1_publication_object_comment ADD ipAddress VARCHAR(15) NOT NULL DEFAULT '' AFTER time;
ALTER TABLE wsip1_1_publication_object_comment CHANGE publicationID publicationObjectID INT(10) NOT NULL DEFAULT 0;

-- publication object subscriptions
DROP TABLE IF EXISTS wsip1_1_publication_object_subscription;
CREATE TABLE wsip1_1_publication_object_subscription (
	userID INT(10) NOT NULL DEFAULT 0,
	publicationObjectID INT(10) NOT NULL DEFAULT 0,
	publicationType VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (userID, publicationObjectID, publicationType)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;