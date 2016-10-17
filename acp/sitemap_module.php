<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c) 2016 Jeff Cocking
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lotusjeff\sitemap\acp;

/**
* @ignore
*/
//use lotusjeff\socialshare\prefixes;

class sitemap_module
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\config\db_text */
	protected $config_text;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\log\log */
	protected $log;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var string */
	protected $phpbb_root_path;
	/** @var string */
	protected $phpbb_extension_manager;	
	/** @var string */
	public $u_action;

	/**
	 * Delegates to proper functions that handle the specific case
	 *
	 * @param string $id the id of the acp-module (the url-param "i")
	 * @param string $mode the phpbb acp-mode
	 */
	public function main($id, $mode)
	{
		global $user, $phpbb_container, $phpbb_admin_path, $board_url;

		$user->add_lang_ext('lotusjeff/sitemap', 'sitemap_acp');
		
		switch ($mode)
		{
			case 'settings':
			// no break
			default:
				$this->tpl_name = 'sitemap';
				$this->page_title = 'ACP_SITEMAP_SETTINGS';
				$this->handle_settings();
		}
	}

	/**
	 * Default settings page
	 */
	private function handle_settings()
	{
		global $config, $request, $template, $user;
		// Define the name of the form for use as a form key
		$form_name = 'sitemap';
		add_form_key($form_name);

		$errors = array();

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key($form_name))
			{
				trigger_error('FORM_INVALID');
			}

			$commit_to_db = true;
			$msg = array();

			/**
			 * Perform validation checks
			 */
			$lotusjeff_sitemap_announce_priority = number_format($request->variable('lotusjeff_sitemap_announce_priority', 0.8),1);
			if (!preg_match('/^((\.[0-9]{1})|(0\.[0-9]{1})|(1\.0)|(1))$/i',$lotusjeff_sitemap_announce_priority) && !empty($lotusjeff_sitemap_announce_priority))
			{
				$commit_to_db = false;
				$errors[] = $user->lang('LOTUSJEFF_SITEMAP_INVALID_PRIORITY_VALUE');
			}
			$lotusjeff_sitemap_global_priority = number_format($request->variable('lotusjeff_sitemap_global_priority', 0.8),1);
			if (!preg_match('/^((\.[0-9]{1})|(0\.[0-9]{1})|(1\.0)|(1))$/i',$lotusjeff_sitemap_global_priority) && !empty($lotusjeff_sitemap_global_priority))
			{
				$commit_to_db = false;
				$errors[] = $user->lang('LOTUSJEFF_SITEMAP_INVALID_PRIORITY_VALUE');
			}
			$lotusjeff_sitemap_sticky_priority = number_format($request->variable('lotusjeff_sitemap_sticky_priority', 0.8),1);
			if (!preg_match('/^((\.[0-9]{1})|(0\.[0-9]{1})|(1\.0)|(1))$/i',$lotusjeff_sitemap_sticky_priority) && !empty($lotusjeff_sitemap_sticky_priority))
			{
				$commit_to_db = false;
				$errors[] = $user->lang('LOTUSJEFF_SITEMAP_INVALID_PRIORITY_VALUE');
			}
			$lotusjeff_sitemap_forum_threshold = $request->variable('lotusjeff_sitemap_forum_threshold', 0);
			if (!preg_match('/^[0-9]{1,16}$/i',$lotusjeff_sitemap_forum_threshold) && !empty($lotusjeff_sitemap_forum_threshold))
			{
				$commit_to_db = false;
				$errors[] = $user->lang('LOTUSJEFF_SITEMAP_INVALID_THRESHOLD_VALUE');
			}

			/**
			 * Validation passed, commit to config and return saved message
			 */
			if ($commit_to_db)
			{
				$config->set('lotusjeff_sitemap_announce_priority', $lotusjeff_sitemap_announce_priority);
				$config->set('lotusjeff_sitemap_sticky_priority', $lotusjeff_sitemap_sticky_priority);
				$config->set('lotusjeff_sitemap_global_priority', $lotusjeff_sitemap_global_priority);
				$config->set('lotusjeff_sitemap_forum_threshold', $lotusjeff_sitemap_forum_threshold);
				$config->set('lotusjeff_sitemap_link', $request->variable('lotusjeff_sitemap_link', 1));
				$config->set('lotusjeff_sitemap_forum_exclude', serialize($request->variable('lotusjeff_sitemap_forum_exclude', array(0))));

				if (empty($msg))
				{
					$msg[] = $user->lang('LOTUSJEFF_SITEMAP_SETTINGS_SAVED');
				}
				trigger_error(join('<br/>', $msg) . adm_back_link($this->u_action));
			}

		}

		/**
		 * Build forum exclude selection box
		 */
		$forum_link = make_forum_select(false, false, true, true, true, false, true);
		foreach ($forum_link as $link)
		{
			$template->assign_block_vars('lotusjeff_sitemap_forum_exclude_options', array(
				'VALUE'			=> $link['forum_id'],
				'LABEL'			=> $link['padding'] .$link['forum_name'],
				'S_SELECTED'	=> in_array($link['forum_id'], unserialize($config['lotusjeff_sitemap_forum_exclude'])),
				'S_DISABLED' 	=> $link['disabled'],
			));
		}

		$template->assign_vars(array(
			'LOTUSJEFF_SITEMAP_ANNOUNCE_PRIORITY'	=> $config['lotusjeff_sitemap_announce_priority'],
			'LOTUSJEFF_SITEMAP_STICKY_PRIORITY'		=> $config['lotusjeff_sitemap_sticky_priority'],
			'LOTUSJEFF_SITEMAP_GLOBAL_PRIORITY'		=> $config['lotusjeff_sitemap_global_priority'],
			'LOTUSJEFF_SITEMAP_FORUM_THRESHOLD'		=> $config['lotusjeff_sitemap_forum_threshold'],
			'LOTUSJEFF_SITEMAP_LINK'				=> $config['lotusjeff_sitemap_link'],
			'LOTUSJEFF_SITEMAP_LOCATION'			=> generate_board_url() . '/app.php/sitemap/sitemap.xml',
			'S_ERROR'                               => (sizeof($errors)) ? true : false,
			'ERROR_MSG'                             => implode('<br />', $errors),
			'U_ACTION'                     			=> $this->u_action,
		));
	}

}
