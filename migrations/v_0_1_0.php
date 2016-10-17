<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c) 2016 Jeff Cocking
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lotusjeff\sitemap\migrations;

class v_0_1_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return;
	}
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('lotusjeff_sitemap_images', 1)),
			array('config.add', array('lotusjeff_sitemap_sticky_priority', 0.8)),
			array('config.add', array('lotusjeff_sitemap_announce_priority', 0.8)),
			array('config.add', array('lotusjeff_sitemap_global_priority', 0.8)),
			array('config.add', array('lotusjeff_sitemap_forum_exclude', 'a:0:{}')),
			array('config.add', array('lotusjeff_sitemap_forum_threshold', 0)),
			array('config.add', array('lotusjeff_sitemap_link', 1)),
			array('config.add', array('lotusjeff_sitemap_versions', '0.1.0')),
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_SITEMAP_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_SITEMAP_TITLE',
				array(
					'module_basename'	=> '\lotusjeff\sitemap\acp\sitemap_module',
					'auth'				=> 'ext_lotusjeff\sitemap && acl_a_board',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
