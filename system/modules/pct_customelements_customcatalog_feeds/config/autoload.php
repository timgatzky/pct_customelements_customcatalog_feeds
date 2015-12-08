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

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'PCT\CustomCatalog',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'PCT\CustomCatalog\Feeds'							=> 'system/modules/pct_customelements_customcatalog_feeds/PCT/CustomCatalog/Feeds.php',
	'PCT\CustomCatalog\Feeds\TableCustomCatalog'		=> 'system/modules/pct_customelements_customcatalog_feeds/PCT/CustomCatalog/Feeds/TableCustomCatalog.php',
	'PCT\CustomCatalog\Feeds\TableCustomCatalogFeed'	=> 'system/modules/pct_customelements_customcatalog_feeds/PCT/CustomCatalog/Feeds/TableCustomCatalogFeed.php',

));