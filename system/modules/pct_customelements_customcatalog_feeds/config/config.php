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

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\System;

/**
 * Constants
*/
define('PCT_CUSTOMCATALOG_FEEDS_VERSION', '2.0.0');

if( version_compare(ContaoCoreBundle::getVersion(),'5.0','>=') )
{
	$rootDir = System::getContainer()->getParameter('kernel.project_dir');
	include( $rootDir.'/system/modules/pct_customelements_plugin_customcatalog_feeds/config/autoload.php' );
}

// Register more tables to the pct_customelement module
$GLOBALS['BE_MOD']['content']['pct_customelements']['tables'][] = 'tl_pct_customcatalog_feed';

/**
 * Register the model classes
 */
$GLOBALS['TL_MODELS']['tl_pct_customcatalog_feed'] = 'PCT\CustomCatalog\Models\FeedModel';

/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['daily'][] = array('PCT\CustomCatalog\Feeds', 'generateFeeds');

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] 	= array('PCT\CustomCatalog\Feeds', 'purgeOldFeeds');
$GLOBALS['TL_HOOKS']['generateXmlFiles'][] 	= array('PCT\CustomCatalog\Feeds', 'generateFeeds');
$GLOBALS['TL_HOOKS']['generatePage'][] 	= array('PCT\CustomCatalog\Feeds', 'addFeedLinkToPage');
