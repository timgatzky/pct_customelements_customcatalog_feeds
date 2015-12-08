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
class TableCustomCatalogFeed extends \Backend
{
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser','User');
	}
	
	/**
	 * Check feed alias
	 * @param mixed
	 * @param object
	 * @return mixed
	 */
	public function checkFeedAlias($varValue, $objDC)
	{
		// No change or empty value
		if ($varValue == $objDC->value || $varValue == '')
		{
			return $varValue;
		}

		$varValue = standardize($varValue); // see #5096

		$this->import('Automator');
		$arrFeeds = $this->Automator->purgeXmlFiles(true);

		// Alias exists
		if (array_search($varValue, $arrFeeds) !== false)
		{
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
		}

		return $varValue;
	}
	
	
	/**
	 * Check for modified feeds and update the XML files if necessary
	 */
	public function generateFeed()
	{
		$session = $this->Session->get('customcatalog_feed_updater');

		if (!is_array($session) || empty($session))
		{
			return;
		}

		$this->import('News');
		$objFeeds = new \PCT\CustomCatalog\Feeds;

		foreach ($session as $id)
		{
			$objFeeds->generateFeedsByArchive($id);
		}

		$this->import('Automator');
		$this->Automator->generateSitemap();

		$this->Session->set('customcatalog_feed_updater', null);
	}
	
	
	/**
	 * Schedule a feed update
	 *
	 * This method is triggered when a single news archive or multiple news
	 * archives are modified (edit/editAll).
	 *
	 * @param DataContainer $dc
	 */
	public function scheduleUpdate($objDC)
	{
		// Return if there is no ID
		if (!$objDC->id)
		{
			return;
		}

		// Store the ID in the session
		$session = $this->Session->get('customcatalog_feed_updater');
		$session[] = $objDC->id;
		$this->Session->set('customcatalog_feed_updater', array_unique($session));
	}
	
	
	/**
	 * Return the configurations as array
	 * @param object
	 * @return array
	 */
	public function getAllowedConfigurations($objDC)
	{
		$objResult =  \Database::getInstance()->prepare("SELECT * FROM tl_pct_customcatalog WHERE pid=?")->execute($objDC->activeRecord->pid);
		if($objResult->numRows < 1)
		{
			return array();
		}
		
		$arrReturn = array();
		while($objResult->next())
		{
			$arrReturn[ $objResult->id ] = $objResult->title . ' ['.$objResult->tableName.']';
		}
		
		return $arrReturn;
	}
	
	
	/**
	 * Return all attributes
	 * @param object
	 * @return array
	 */
	public function getTextAttributes($objDC)
	{
		$arrTypes = array('text','textarea');
		
		$objResult = \PCT\CustomElements\Core\AttributeFactory::fetchMultipleByCustomElement($objDC->activeRecord->pid);
		if($objResult->numRows < 1)
		{
			return array();
		}
		
		$arrReturn = array();
		while($objResult->next())
		{
			if(!in_array($objResult->type, $arrTypes))
			{
				continue;
			}
			
			$arrReturn[ $objResult->id ] = $objResult->title . ' ['.$objResult->type.']';
		}
		
		return $arrReturn;
	}
}