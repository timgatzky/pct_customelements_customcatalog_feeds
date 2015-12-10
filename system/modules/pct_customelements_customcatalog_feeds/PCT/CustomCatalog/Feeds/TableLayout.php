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
 * Namespace
 */
namespace PCT\CustomCatalog\Feeds;

/**
 * Class file
 */
class TableLayout extends \Backend
{
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser','User');
	}
	
	/**
	 * Return all feeds as array
	 * @param object
	 */
	public function getAllFeeds($objDC)
	{
		$objFeeds = \PCT\CustomCatalog\Models\FeedModel::findAll();
		if($objFeeds === null)
		{
			return array();
		}
		
		$arrReturn = array();
		while($objFeeds->next())
		{
			$arrReturn[ $objFeeds->id ] = $objFeeds->title ?: $objFeeds->alias;
		}
	
		return $arrReturn;
	}
}