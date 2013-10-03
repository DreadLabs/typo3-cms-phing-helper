<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

return array(
	'BE' => array(
		'accessListRenderMode' => 'checkbox',
		'compressionLevel' => '3',
		'disableClassic' => '1',
		'disable_exec_function' => '0',
		'elementVersioningOnly' => '1',
		'flexformForceCDATA' => '1',
		'forceCharset' => '',
		// "DreadLabs"
		'installToolPassword' => '2ccbdfbea4716a57da75f4c8e8d651ac',
		'loginSecurityLevel' => 'rsa',
		'maxFileSize' => '50000',
		'versionNumberInFilename' => '0',
	),
	'DB' => array(
		'database' => 'db123456_999',
		'extTablesDefinitionScript' => 'extTables.php',
		'host' => '127.0.0.3',
		'password' => 'XJq951Z38K',
		'username' => 'db123456_999',
	),
	'EXT' => array(
		'allowGlobalInstall' => '0',
		'extConf' => array(
			'beautyofcode' => 'a:0:{}',
			'disqusapi' => 'a:0:{}',
			'em' => 'a:4:{s:14:"showOldModules";s:1:"0";s:14:"inlineToWindow";s:1:"1";s:19:"displayMyExtensions";s:1:"1";s:17:"selectedLanguages";s:2:"de";}',
			'extdeveval' => 'a:0:{}',
			'extension_builder' => 'a:3:{s:15:"enableRoundtrip";s:0:"";s:15:"backupExtension";s:1:"1";s:9:"backupDir";s:35:"uploads/tx_extensionbuilder/backups";}',
			'fed' => 'a:9:{s:29:"enableBackendRecordController";s:1:"0";s:24:"enableFluidPageTemplates";s:1:"0";s:42:"enableFluidPageTemplateVariableInheritance";s:1:"0";s:31:"enableFallbackFluidPageTemplate";s:1:"0";s:18:"enableSolrFeatures";s:1:"0";s:21:"enableFrontendPlugins";s:1:"0";s:30:"enableIntegratedBackendLayouts";s:1:"0";s:28:"increaseExtbaseCacheLifetime";s:1:"0";s:35:"disableAutomaticTypoScriptInclusion";s:1:"1";}',
			'fluidpages' => 'a:1:{s:8:"doktypes";s:0:"";}',
			'flux' => 'a:3:{s:9:"debugMode";s:1:"1";s:7:"compact";s:1:"0";s:20:"rewriteLanguageFiles";s:1:"0";}',
			'gridelements' => 'a:1:{s:20:"additionalStylesheet";s:0:"";}',
			'indexed_search' => 'a:0:{}',
			'opendocs' => 'a:1:{s:12:"enableModule";s:1:"0";}',
			'phpunit' => 'a:2:{s:17:"excludeextensions";s:8:"lib, div";s:10:"phpunitlib";s:0:"";}',
			'realurl' => 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"0";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}',
			'rsaauth' => 'a:0:{}',
			'saltedpasswords' => 'a:2:{s:3:"FE.";a:2:{s:7:"enabled";s:1:"1";s:21:"saltedPWHashingMethod";s:28:"tx_saltedpasswords_salts_md5";}s:3:"BE.";a:2:{s:7:"enabled";s:1:"1";s:21:"saltedPWHashingMethod";s:28:"tx_saltedpasswords_salts_md5";}}',
			'static_info_tables' => 'a:4:{s:13:"enableManager";s:1:"0";s:5:"dummy";s:1:"0";s:7:"charset";s:5:"utf-8";s:12:"usePatch1822";s:1:"0";}',
			'vhs' => 'a:0:{}',
		),
		'extListArray' => array(
			'extbase',
			'css_styled_content',
			'version',
			'tsconfig_help',
			'context_help',
			'extra_page_cm_options',
			'impexp',
			'sys_note',
			'tstemplate',
			'tstemplate_ceditor',
			'tstemplate_info',
			'tstemplate_objbrowser',
			'tstemplate_analyzer',
			'func_wizards',
			'wizard_crpages',
			'wizard_sortpages',
			'lowlevel',
			'install',
			'belog',
			'beuser',
			'aboutmodules',
			'setup',
			'taskcenter',
			'info_pagetsconfig',
			'viewpage',
			'rtehtmlarea',
			't3skin',
			't3editor',
			'reports',
			'about',
			'cshmanual',
			'recycler',
			'indexed_search',
			'static_info_tables_de',
			'fluid',
			'beautyofcode',
			'info',
			'perm',
			'func',
			'filelist',
			'scheduler',
			'workspaces',
			'form',
			'felogin',
			'rsaauth',
			'saltedpasswords',
			'kickstarter',
			'phpunit',
			'sys_action',
			'opendocs',
			'extdeveval',
			'disqusapi',
			'realurl',
			'flux',
			'fluidpages',
			'fluidcontent',
			'gridelements',
			'static_info_tables',
			'vhs',
			'adodb',
			'dbal',
		),
		'extList_FE' => 'extbase,css_styled_content,version,install,rtehtmlarea,t3skin,indexed_search,static_info_tables,static_info_tables_de,realurl,fluid,beautyofcode,workspaces,form,felogin,rsaauth,saltedpasswords,kickstarter,flux,fluidpages,vhs,phpunit,fed,gridelements',
		'noEdit' => '1',
	),
	'EXTCONF' => array(
		'lang' => array(
			'availableLanguages' => array(),
		),
	),
	'FE' => array(
		'compressionLevel' => '5',
		'disableNoCacheParameter' => '0',
		'lockIP' => '4',
		'logfile_dir' => 'fileadmin/',
		'noPHPscriptInclude' => '1',
		'pageNotFound_handling' => 'http://www.example.org/page-not-found/index.html',
		'permalogin' => '0',
		'versionNumberInFilename' => '',
	),
	'GFX' => array(
		'TTFdpi' => '96',
		'enable_typo3temp_db_tracking' => '1',
		'gdlib_2' => '1',
		'gdlib_png' => '1',
		'gif_compress' => '0',
		'im_combine_filename' => 'combine',
		'im_imvMaskState' => '1',
		'im_mask_temp_ext_gif' => '0',
		'im_noScaleUp' => '1',
		'im_no_effects' => '0',
		'im_path' => '/usr/bin/',
		'im_version_5' => 'gm',
		'imagefile_ext' => 'gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai',
		'jpg_quality' => '85',
		'noIconProc' => '0',
		'png_truecolor' => '1',
		'thumbnails_png' => '1',
	),
	'INSTALL' => array(
		'wizardDone' => array(
			'TYPO3\CMS\Install\CoreUpdates\CompressionLevelUpdate' => 1,
			'TYPO3\CMS\Install\CoreUpdates\InstallSysExtsUpdate' => '["info","perm","func","filelist","about","cshmanual","feedit","opendocs","recycler","t3editor","reports","scheduler","simulatestatic"]',
			'TYPO3\CMS\Install\Updates\FilemountUpdateWizard' => 1,
			'TYPO3\CMS\Rtehtmlarea\Hook\Install\DeprecatedRteProperties' => 1,
			'tx_coreupdates_compressionlevel' => '1',
			'tx_coreupdates_installnewsysexts' => '1',
			'tx_coreupdates_installsysexts' => '1',
			'tx_rtehtmlarea_deprecatedRteProperties' => '1',
		),
	),
	'SYS' => array(
		'UTF8filesystem' => '0',
		'binPath' => '/www/123456_12345/tools/exiftags,/www/123456_12345/tools/catdoc/bin,/www/123456_12345/tools/xpdf/xpdf',
		'compat_version' => '6.0',
		'debugExceptionHandler' => 't3lib_error_DebugExceptionHandler',
		'devIPmask' => '192.168.1.254',
		'displayErrors' => '1',
		'enableDeprecationLog' => 'file',
		'enable_DLOG' => '1',
		'enable_errorDLOG' => '0',
		// sha384sum of "DreadLabs"
		'encryptionKey' => '3d49295648b225db9b2f1b6dda89ca350bb5e517a915246cd49966b98d6553c306070d1e20f104941414d47e864c8b45',
		'errorHandler' => '',
		'exceptionalErrors' => '28917',
		'forceReturnPath' => '0',
		'multiplyDBfieldSize' => '1',
		'productionExceptionHandler' => '',
		'setDBinit' => 'SET NAMES utf8;',
		'sitename' => 'example.org [Testing]',
		'sqlDebug' => '0',
		'systemLog' => '',
		'systemLogLevel' => '2',
	),
);
?>