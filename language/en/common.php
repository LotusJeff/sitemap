<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c)  2016 Jeff Cocking
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
    'LOTUSJEFF_DISPLAY_TITLE'   => 'Sitemap',
    'LOTUSJEFF_SITEMAP_NODATA'  => 'Forum has no data.',
));
