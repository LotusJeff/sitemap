<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c) 2016 Jeff Cocking
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'LOTUSJEFF_SITEMAP_TITLE'				=> 'SEO Sitemaps',
	'LOTUSJEFF_SITEMAP_PRIORITY'			=> 'Priority Settings',
	'LOTUSJEFF_SITEMAP_STICKY_PRIORITY'				=> 'Sticky Topic Priority',
	'LOTUSJEFF_SITEMAP_STICKY_PRIORITY_EXPLAIN'		=> 'Sticky Topic priority for URLs listed in sitemaps. Must be a number between 0.0 and 1.0.',
	'LOTUSJEFF_SITEMAP_GLOBAL_PRIORITY'				=> 'Global Topic Priority',
	'LOTUSJEFF_SITEMAP_GLOBAL_PRIORITY_EXPLAIN'		=> 'Global Topic priority for URLs listed in sitemaps. Must be a number between 0.0 and 1.0.',
	'LOTUSJEFF_SITEMAP_ANNOUNCE_PRIORITY'			=> 'Announcement Topic Priority',
	'LOTUSJEFF_SITEMAP_ANNOUNCE_PRIORITY_EXPLAIN'	=> 'Announcement Topic priority for URLs listed in sitemaps. Must be a number between 0.0 and 1.0.',
	'LOTUSJEFF_SITEMAP_FORUM_EXCLUDE'		=> 'Forum Exclusions',
	'LOTUSJEFF_SITEMAP_FORUM_EXCLUDE_EXPLAIN'	=> 'You can exclude one or more forums from the sitemap listing. If this field is left empty all public forums will be included.',
	'LOTUSJEFF_SITEMAP_FORUM_THRESHOLD'		=> 'Sitemap Threshold',
	'LOTUSJEFF_SITEMAP_FORUM_THRESHOLD_EXPLAIN'	=> 'Minimum number of URLs to display a sitemap. Only forums with more than this threshold number of topics will have a sitemap.',
	'LOTUSJEFF_SITEMAP_LINKS'				=> 'Forum Linking',
	'LOTUSJEFF_SITEMAP_LINK'				=> 'Sitemap Link in Footer',
	'LOTUSJEFF_SITEMAP_LINK_EXPLAIN'		=> 'Display a sitemap link in the footer.',
	'LOTUSJEFF_SITEMAP_INVALID_PRIORITY_VALUE'	=> 'Priority Value must be between 0.0 and 1.0.',
	'LOTUSJEFF_SITEMAP_INVALID_THRESHOLD_VALUE'	=> 'Threshold Value must be a number.',
	'LOTUSJEFF_SITEMAP_SETTINGS_SAVED'		=> 'Settings Saved',
	'LOTUSJEFF_SITEMAP_EXPLAIN'		=> 'Settings Saved',
	'LOTUSJEFF_SITEMAP_LOCATION'		=> 'Sitemap Index',
));
