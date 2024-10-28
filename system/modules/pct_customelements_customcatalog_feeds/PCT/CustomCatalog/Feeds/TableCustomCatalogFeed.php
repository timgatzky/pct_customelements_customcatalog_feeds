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

use Contao\Automator;
use Contao\Backend;
use Contao\BackendUser;
use Contao\Database;
use Contao\StringUtil;
use Contao\System;

/**
 * Class file
 */
class TableCustomCatalogFeed extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import(BackendUser::class, 'User');
		$this->import(Database::class, 'Database');	
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

		$varValue = StringUtil::standardize($varValue); // see #5096

		$this->import(Automator::class, 'Automator');
		$arrFeeds = $this->Automator->purgeXmlFiles(true);

		// Alias exists
		if (array_search($varValue, $arrFeeds) !== false)
		{
			throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
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

		$objFeeds = new \PCT\CustomCatalog\Feeds;

		foreach ($session as $id)
		{
			$objFeeds->generateFeedsByConfig($id);
		}

		$this->import(Automator::class, 'Automator');
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
		$objResult = $this->Database->prepare("SELECT * FROM tl_pct_customcatalog WHERE pid=?")->execute($objDC->activeRecord->pid);
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
	 * Return all text attributes
	 * @param object
	 * @return array
	 */
	public function getTextAttributes($objDC)
	{
		$arrTypes = array('text','textarea');
		$objResult = $this->Database->prepare("SELECT * FROM tl_pct_customelement_attribute WHERE pid IN(SELECT id FROM tl_pct_customelement_group WHERE pid=?) AND ".$this->Database->findInSet('type',$arrTypes))->execute($objDC->activeRecord->pid);
		$arrReturn = array();
		while($objResult->next())
		{
			$arrReturn[ $objResult->id ] = $objResult->title . ' ['.$objResult->type.' id:'.$objResult->id.']';
		}
		return $arrReturn;
	}
	
	
	/**
	 * Return all timestamp attributes
	 * @param object
	 * @return array
	 */
	public function getTimestampAttributes($objDC)
	{
		$arrTypes = array('text','timestamp');
		$objResult = $this->Database->prepare("SELECT * FROM tl_pct_customelement_attribute WHERE pid IN(SELECT id FROM tl_pct_customelement_group WHERE pid=?) AND ".$this->Database->findInSet('type',$arrTypes))->execute($objDC->activeRecord->pid);
		$arrReturn = array();
		while($objResult->next())
		{
			$arrReturn[ $objResult->id ] = $objResult->title . ' ['.$objResult->type.' id:'.$objResult->id.']';
		}
		return $arrReturn;
	}


	/**
	 * Return all image attributes
	 * @param object
	 * @return array
	 */
	public function getImageAttributes($objDC)
	{
		$arrTypes = array('image');
		$objResult = $this->Database->prepare("SELECT * FROM tl_pct_customelement_attribute WHERE pid IN(SELECT id FROM tl_pct_customelement_group WHERE pid=?) AND ".$this->Database->findInSet('type',$arrTypes))->execute($objDC->activeRecord->pid);
		$arrReturn = array();
		while($objResult->next())
		{
			$arrReturn[ $objResult->id ] = $objResult->title . ' ['.$objResult->type.' id:'.$objResult->id.']';
		}
		return $arrReturn;
	}

}