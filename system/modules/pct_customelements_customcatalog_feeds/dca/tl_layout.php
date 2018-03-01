<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2015 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013, Premium Contao Webworks, Premium Contao Themes
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customelements
 * @subpackage	pct_customelements_plugin_customcatalog
 * @subpackage	pct_customelements_customcatalog_feeds
 * @link		http://contao.org
 */

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('newsfeeds', 'newsfeeds,customcatalogfeeds', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_layout']['fields']['customcatalogfeeds'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['customcatalogfeeds'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('PCT\CustomCatalog\Feeds\TableLayout', 'getAllFeeds'),
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"	
);

