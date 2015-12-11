<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2015
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customelements
 * @subpackage	pct_customelements_plugin_customcatalog
 * @subpackage	pct_customelements_customcatalog_feeds
 * @link		http://contao.org
 */

$this->loadLanguageFile('tl_news_archive');

array_insert($GLOBALS['TL_DCA']['tl_pct_customcatalog']['list']['global_operations'],0,array
(
	'feeds' => array
	(
		'label'               => &$GLOBALS['TL_LANG']['tl_news_archive']['feeds'],
		'href'                => 'table=tl_pct_customcatalog_feed',
		'class'               => 'header_rss',
		'attributes'          => 'onclick="Backend.getScrollOffset()"',
		'button_callback'     => array('PCT\CustomCatalog\Feeds\TableCustomCatalog', 'manageFeedsButton')
	),
));