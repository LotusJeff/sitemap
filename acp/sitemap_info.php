<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c) 2016 Jeff Cocking
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lotusjeff\sitemap\acp;

class sitemap_info
{
	public function module()
	{
		return array(
			'filename'	=> '\lotusjeff\sitemap\acp\sitemap_module',
			'title'		=> 'ACP_SITEMAP_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title' => 'ACP_SITEMAP_SETTINGS',
					'auth' => 'ext_lotusjeff/sitemap && acl_a_board',
					'cat' => array('ACP_SITEMAP_TITLE')
				),
			),
		);
	}
}
