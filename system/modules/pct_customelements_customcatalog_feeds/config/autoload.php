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


// path relative from composer directory
$path = \Contao\System::getContainer()->getParameter('kernel.project_dir').'/vendor/composer/../../system/modules/pct_customelements_customcatalog_feeds';

/**
 * Register the classes
 */
$classMap = array
(
	'PCT\CustomCatalog\Feeds'							=> $path.'/PCT/CustomCatalog/Feeds.php',
	'PCT\CustomCatalog\Models\FeedModel'				=> $path.'/PCT/CustomCatalog/Models/FeedModel.php',
	'PCT\CustomCatalog\Feeds\TableCustomCatalog'		=> $path.'/PCT/CustomCatalog/Feeds/TableCustomCatalog.php',
	'PCT\CustomCatalog\Feeds\TableCustomCatalogFeed'	=> $path.'/PCT/CustomCatalog/Feeds/TableCustomCatalogFeed.php',
	'PCT\CustomCatalog\Feeds\TableLayout'				=> $path.'/PCT/CustomCatalog/Feeds/TableLayout.php',	
);

$loader = new \Composer\Autoload\ClassLoader();
$loader->addClassMap($classMap);
$loader->register();