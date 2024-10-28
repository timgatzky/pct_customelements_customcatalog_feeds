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
 * Load language file
 */
\Contao\System::loadLanguageFile('tl_news_feed');

/**
 * Table tl_pct_customcatalog_feed
 */
$GLOBALS['TL_DCA']['tl_pct_customcatalog_feed'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'ptable'					  => 'tl_pct_customelement',
		'onload_callback' => array
		(
			array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'generateFeed')
		),
		'onsubmit_callback' => array
		(
			array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'scheduleUpdate')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'alias' => 'index'
			)
		),
		#'backlink' => \Controller::getReferer(),
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('title'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	)
);


/**
 * Palettes
 */
$arrPalettes = array
(
	'title_legend'				=> array('title','alias','language'),
	'configs_legend'			=> array('configs','jumpTo'),
	'export_legend'				=> array('titleField','publishedField','descriptionField','authorField','imageField'),
	'config_legend'				=> array('format','maxItems','feedBase','description'),
);
$GLOBALS['TL_DCA']['tl_pct_customcatalog_feed']['palettes']['default'] = $objDcaHelper->generatePalettes($arrPalettes);


/**
 * Fields
 */
$objDcaHelper->addFields(array
(
	'id' => array
	(
		'sql'                     => "int(10) unsigned NOT NULL auto_increment"
	),
	'pid' => array
	(
		'sql'                     => "int(10) unsigned NOT NULL default '0'"
	),
	'tstamp' => array
	(
		'sql'                     => "int(10) unsigned NOT NULL default '0'"
	),
	'title' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['title'],
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'text',
		'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'alias' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['alias'],
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'text',
		'eval'                    => array('mandatory'=>true, 'rgxp'=>'alias', 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
		'save_callback' => array
		(
			array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'checkFeedAlias')
		),
		'sql'                     => "varchar(255) BINARY NOT NULL default ''"
	),
	'language' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['language'],
		'exclude'                 => true,
		'search'                  => true,
		'filter'                  => true,
		'inputType'               => 'text',
		'eval'                    => array('mandatory'=>true, 'maxlength'=>32, 'tl_class'=>'w50'),
		'sql'                     => "varchar(32) NOT NULL default ''"
	),
	'configs' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['configs'],
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'checkbox',
		'options_callback'        => array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'getAllowedConfigurations'),
		'eval'                    => array('multiple'=>true, 'mandatory'=>true),
		'sql'                     => "blob NULL"
	),
	'jumpTo' => array
	(
		'label'           		=> &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['jumpTo'],
		'exclude'         		=> true,
		'inputType'       		=> 'pageTree',
		'eval'            		=> array('tl_class'=>'','mandatory'=>true),
		'sql'			  		=> "int(10) NOT NULL default '0'",
	),
	'format' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['format'],
		'default'                 => 'rss',
		'exclude'                 => true,
		'filter'                  => true,
		'inputType'               => 'select',
		'options'                 => array('rss'=>'RSS 2.0', 'atom'=>'Atom'),
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "varchar(32) NOT NULL default ''"
	),
	'maxItems' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['maxItems'],
		'default'                 => 25,
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('mandatory'=>true, 'rgxp'=>'natural', 'tl_class'=>'w50'),
		'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
	),
	'feedBase' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['feedBase'],
		'default'                 => \Contao\Environment::get('base'),
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'text',
		'eval'                    => array('trailingSlash'=>true, 'rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'description' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['description'],
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'textarea',
		'eval'                    => array('style'=>'height:60px', 'tl_class'=>'clr'),
		'sql'                     => "text NULL"
	),
	// export settings
	'titleField' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['titleField'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'getTextAttributes'),
		'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true,'chosen'=>true),
		'sql'                     => "int(10) NOT NULL default '0'"
	),
	'descriptionField' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['descriptionField'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'getTextAttributes'),
		'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true,'chosen'=>true),
		'sql'                     => "int(10) NOT NULL default '0'"
	),
	'publishedField' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['publishedField'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'getTimestampAttributes'),
		'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true,'chosen'=>true),
		'sql'                     => "int(10) NOT NULL default '0'"
	),
	'authorField' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['authorField'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'getTextAttributes'),
		'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true,'chosen'=>true),
		'sql'                     => "int(10) NOT NULL default '0'"
	),
	'imageField' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customcatalog_feed']['imageField'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('PCT\CustomCatalog\Feeds\TableCustomCatalogFeed', 'getImageAttributes'),
		'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true,'chosen'=>true),
		'sql'                     => "int(10) NOT NULL default '0'"
	),
));
