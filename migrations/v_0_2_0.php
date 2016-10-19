<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c) 2016 Jeff Cocking
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lotusjeff\sitemap\migrations;

class v_0_2_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['lotusjeff_sitemap_versions']) && version_compare($this->config['lotusjeff_sitemap_versions'], '0.2.0', '>=');
	}
	static public function depends_on()
	{
		return array('\lotusjeff\sitemap\migrations\v_0_1_0');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('lotusjeff_sitemap_additional', 0)),
			array('config.update', array('lotusjeff_sitemap_versions', '0.2.0')),
		);
	}
}
