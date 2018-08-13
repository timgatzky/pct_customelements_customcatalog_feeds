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
namespace PCT\CustomCatalog;

/**
 * Imports
 */
use PCT\CustomCatalog\Models\FeedModel as FeedModel;
use PCT\CustomElements\Plugins\CustomCatalog\Core\CustomCatalogFactory as CustomCatalogFactory;

/**
 * Class file
 */
class Feeds extends \Frontend
{
	/**
	 * Update a particular RSS feed
	 * @param integer
	 */
	public function generateFeed($intId)
	{
		$objFeed = FeedModel::findByPk($intId);
		if ($objFeed === null)
		{
			return;
		}

		$objFeed->feedName = $objFeed->alias ?: 'customcatalog' . $objFeed->id;

		// Delete XML file
		if (\Input::get('act') == 'delete')
		{
			$this->import('Files');
			$this->Files->delete($objFeed->feedName . '.xml');
		}

		// Update XML file
		else
		{
			$this->generateFiles($objFeed->row());
			$this->log('Generated news feed "' . $objFeed->feedName . '.xml"', __METHOD__, TL_CRON);
		}
	}


	/**
	 * Delete old files and generate all feeds
	 */
	public function generateFeeds()
	{
		$this->import('Automator');
		$this->Automator->purgeXmlFiles();

		$objFeed = FeedModel::findAll();

		if ($objFeed !== null)
		{
			while ($objFeed->next())
			{
				$objFeed->feedName = $objFeed->alias ?: 'customcatalog' . $objFeed->id;
				$this->generateFiles($objFeed->row());
				$this->log('Generated news feed "' . $objFeed->feedName . '.xml"', __METHOD__, TL_CRON);
			}
		}
	}


	/**
	 * Generate all feeds including a certain config
	 * @param integer $intId
	 */
	public function generateFeedsByConfig($intId)
	{
		$objFeed = FeedModel::findByConfig($intId);

		if ($objFeed !== null)
		{
			while ($objFeed->next())
			{
				$objFeed->feedName = $objFeed->alias ?: 'customcatalog' . $objFeed->id;

				// Update the XML file
				$this->generateFiles($objFeed->row());
				$this->log('Generated CustomCatalog feed "' . $objFeed->feedName . '.xml"', __METHOD__, TL_CRON);
			}
		}
	}


	/**
	 * Generate the XML files and save them to the root directory
	 * @param array
	 */
	protected function generateFiles($arrFeed)
	{
		$arrConfigs = deserialize($arrFeed['configs']);

		if(!is_array($arrConfigs) || empty($arrConfigs))
		{
			return;
		}
		
		
		$strType = ($arrFeed['format'] == 'atom') ? 'generateAtom' : 'generateRss';
		$strLink = $arrFeed['feedBase'] ?: \Environment::get('base');
		$strFile = $arrFeed['feedName'];

		$objFeed = new \Feed($strFile);
		$objFeed->link = $strLink;
		$objFeed->title = $arrFeed['title'];
		$objFeed->description = $arrFeed['description'];
		$objFeed->language = $arrFeed['language'];
		$objFeed->published = $arrFeed['tstamp'];
		
		$objJumpTo = \PageModel::findByPk($arrFeed['jumpTo']);

		// find the source attributes
		$objDescriptionAttribute 	= \PCT\CustomElements\Core\AttributeFactory::fetchById($arrFeed['descriptionField']);
		$objPublishedAttribute 		= \PCT\CustomElements\Core\AttributeFactory::fetchById($arrFeed['publishedField']);
		$objTitleAttribute 			= \PCT\CustomElements\Core\AttributeFactory::fetchById($arrFeed['titleField']);
		$objAuthorAttribute 		= \PCT\CustomElements\Core\AttributeFactory::fetchById($arrFeed['authorField']);
		$objImageAttribute 			= \PCT\CustomElements\Core\AttributeFactory::fetchById($arrFeed['imageField']);
		
		$arrFields = array
		(
			'id','pid','tstamp',
			'description' 	=> $objDescriptionAttribute->alias,
			'title'			=> $objTitleAttribute->alias,
			'published'		=> $objPublishedAttribute->alias,
			'author'		=> $objAuthorAttribute->alias,
			'singleSRC'		=> $objImageAttribute->alias,
		);
		
		foreach($arrConfigs as $config_id)
		{
			$objCC = CustomCatalogFactory::findById($config_id);
			if($objCC === null)
			{
				continue;
			}
			
			// simulate a list module
			$objModule = new \StdClass;
			$objModule->customcatalog_filter_showAll = true;
			$objCC->setOrigin($objModule);
			
			// set visibles to source attribute only
			$objCC->setVisibles(array_filter(array_values($arrFields)));
			
			if($arrFeed['maxItems'] > 0)
			{
				$objCC->setLimit($arrFeed['maxItems']);
			}
			
			// fetch the entries
			$objEntries = $objCC->prepare();
			if($objEntries->numRows < 1)
			{
				continue;
			}
			
			while($objEntries->next())
			{
				$objItem = new \FeedItem();
				$objItem->title = $objEntries->{$arrFields['title']};
				if(version_compare(VERSION, '4.4','>='))
				{
					$strLink = '';
				}
				$objItem->link = $strLink.$objCC->generateDetailsUrl($objEntries,$objJumpTo);
				$objItem->published = $objEntries->{$arrFields['published']} ?: $objEntries->tstamp;
				$objItem->author = $objEntries->{$arrFields['author']} ?: '';
				
				$strDescription = $this->replaceInsertTags($objEntries->{$arrFields['description']}, false);
				$objItem->description = $this->convertRelativeUrls($strDescription, $strLink);
				
				if($objEntries->{$arrFields['singleSRC']} && $objImageAttribute->published)
				{
					$objFile = \FilesModel::findByUuid($objEntries->{$arrFields['singleSRC']});
					if ($objFile !== null)
					{
						$objItem->addEnclosure($objFile->path);
					}
				}
				
				// add feed item
				$objFeed->addItem($objItem);
			}
		}
		
		$path = 'share';
		if(version_compare(VERSION, '4.4','>='))
		{
			$path = 'web/share';
		}
		
		// create file
		\File::putContent($path.'/'.$strFile . '.xml', $this->replaceInsertTags($objFeed->$strType(), false));
	}
	
	
	/**
	 * Return the names of the existing feeds so they are not removed
	 * @return array
	 */
	public function purgeOldFeeds()
	{
		$arrFeeds = array();
		$objFeeds = FeedModel::findAll();

		if($objFeeds !== null)
		{
			while($objFeeds->next())
			{
				$arrFeeds[] = $objFeeds->alias ?: 'customcatalog' . $objFeeds->id;
			}
		}

		return $arrFeeds;
	}
}