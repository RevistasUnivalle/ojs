<?php

/**
 * AuthorSubmitStep5Form.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package author.form.submit
 *
 * Form for Step 5 of author article submission.
 *
 * $Id$
 */

import("author.form.submit.AuthorSubmitForm");

class AuthorSubmitStep5Form extends AuthorSubmitForm {
	
	/**
	 * Constructor.
	 */
	function AuthorSubmitStep5Form($articleId) {
		parent::AuthorSubmitForm($articleId, 5);
	}
	
	/**
	 * Display the form.
	 */
	function display() {
		$templateMgr = &TemplateManager::getManager();
		
		// Get article file for this article
		$articleFileDao = &DAORegistry::getDAO('ArticleFileDAO');
		$articleFiles = $articleFileDao->getArticleFilesByArticle($this->articleId);

		// Remove supp files
		foreach ($articleFiles as $articleFile) {
			if ($articleFile->getType() != "supp") {
				$filteredArticleFiles[] = $articleFile;
			}
		}	
				
		// Get supplementary files for this article
		$suppFileDao = &DAORegistry::getDAO('SuppFileDAO');
		$suppFiles = $suppFileDao->getSuppFilesByArticle($this->articleId);
		
		$templateMgr->assign('files', array_merge($filteredArticleFiles, $suppFiles));
		parent::display();
	}
	
	/**
	 * Save changes to article.
	 */
	function execute() {
		$articleDao = &DAORegistry::getDAO('ArticleDAO');
		
		// Update article
		$article = &$this->article;
		$article->setDateSubmitted(Core::getCurrentDate());
		$article->setSubmissionProgress(0);
		$articleDao->updateArticle($article);
		
		// Create additional submission mangement records
		$layoutDao = &DAORegistry::getDAO('LayoutAssignmentDAO');
		$layoutAssignment = &new LayoutAssignment();
		$layoutAssignment->setArticleId($article->getArticleId());
		$layoutAssignment->setEditorId(0);
		$layoutDao->insertLayoutAssignment($layoutAssignment);
		
		ArticleLog::logEvent($this->articleId, ARTICLE_LOG_ARTICLE_SUBMIT, ARTICLE_LOG_TYPE_AUTHOR);
		
		return $this->articleId;
	}
	
}

?>
