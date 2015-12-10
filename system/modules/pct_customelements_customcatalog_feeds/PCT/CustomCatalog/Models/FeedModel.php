<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2015 Leo Feyer
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
namespace PCT\CustomCatalog\Models;

/**
 * Class file
 */
class FeedModel extends \Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_pct_customcatalog_feed';


	/**
	 * Find all feeds by a customcatalog id
	 * @param integer
	 * @param array   
	 * @return object
	 */
	public static function findByConfig($intId, $arrOptions=array())
	{
		$t = static::$strTable;
		return static::findBy(array("$t.configs LIKE '%\"" . intval($intId) . "\"%'"), null, $arrOptions);
	}


	/**
	 * Find multiple feeds by their IDs
	 * @param array
	 * @param array
	 * @return object
	 */
	public static function findByIds($arrIds, array $arrOptions=array())
	{
		if(!is_array($arrIds) || count($arrIds) < 1)
		{
			return null;
		}
		$t = static::$strTable;
		return static::findBy(array("$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")"), null, $arrOptions);
	}
	
	
	/**
	 * Find all feeds
	 * @param array
	 * @param array
	 * @return object
	 */
	public static function findAll(array $arrOptions=array())
	{
		$t = static::$strTable;
		return static::findBy(array("$t.tstamp > 0"), null, $arrOptions);
	}
}
