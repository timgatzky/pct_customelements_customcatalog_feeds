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

use Contao\Backend;
use Contao\BackendUser;
use Contao\StringUtil;

/**
 * Class file
 */
class TableCustomCatalog extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import(BackendUser::class, 'User');	
	}
	
	/**
	 * Return the manage feeds button
	 *
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $class
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function manageFeedsButton($href, $label, $title, $class, $attributes)
	{
		return ($this->User->isAdmin || !empty($this->User->newsfeeds) || $this->User->hasAccess('create', 'customcatalogfeedp')) ? '<a href="'.$this->addToUrl($href).'" class="'.$class.'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.$label.'</a> ' : '';
	}
}