<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c) 2016 Jeff Cocking
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lotusjeff\sitemap\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config        $config             Config object
	 * @param \phpbb\template\template    $template           Template object
	 * @param \phpbb\user                 $user               User object
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header'		=> 'lotusjeff_sitemap_set_tpl_data',
		);
	}

	/**
	 * Set Sitemap template data
	 *
	 * @return null
	 * @access public
	 */
	public function lotusjeff_sitemap_set_tpl_data()
	{

		if ($this->config['lotusjeff_sitemap_link'])
		{
			$this->user->add_lang_ext('lotusjeff/sitemap', 'common');
			$sitemap_url = generate_board_url()."/app.php/lotusjeff-sitemap/sitemap.xml";
			$this->template->assign_var('S_LOTUSJEFF_SITEMAP_LINK',$this->config['lotusjeff_sitemap_link']);
			$this->template->assign_var('LOTUSJEFF_SITEMAP_URL',$sitemap_url);
		}
	}
}
