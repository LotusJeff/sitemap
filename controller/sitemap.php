<?php
/**
*
* @package phpBB Extension - Sitemap
* @copyright (c) 2016 Jeff Cocking
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lotusjeff\sitemap\controller;

use Symfony\Component\HttpFoundation\Response;

class sitemap
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver */
	protected $db;

	protected $phpEx;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\event\dispatcher_interface */
	protected $phpbb_dispatcher;

	/** @var string php_ext */
	protected $php_ext;

	/** @var string */
	protected $phpbb_extension_manager;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth						$auth					Auth object
	* @param \phpbb\config\config					$config					Config object
	* @param \phpbb\db\driver\driver_interface		$db						Database object
	* @param \phpbb\controller\helper				$helper					Helper object
	* @param string									$php_ext				phpEx
	* @param \phpbb_extension_manager				$phpbb_extension_manager    phpbb_extension_manager
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\event\dispatcher_interface $phpbb_dispatcher, $php_ext, $phpbb_extension_manager)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
		$this->php_ext = $php_ext;
		$this->phpbb_extension_manager = $phpbb_extension_manager;
		$this->board_url = generate_board_url();
	}

	/**
	 * Creates Sitemap Index of all allowed forums
	 *
	 * @return object
	 * @access public
	 */
	public function index()
	{
		$sql = 'SELECT forum_id, forum_name, forum_last_post_time, forum_topics_approved
			FROM ' . FORUMS_TABLE . '
			WHERE forum_type = ' . (int) FORUM_POST . '
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql);

		/**
		 * Obtain forum data
		 */
		while ($row = $this->db->sql_fetchrow($result))
		{
			if (($this->auth->acl_get('f_list', $row['forum_id'])) && (!in_array($row['forum_id'],unserialize($this->config['lotusjeff_sitemap_forum_exclude']))) && ($row['forum_topics_approved'] > $this->config['lotusjeff_sitemap_forum_threshold']))
			{
				$url_data[] = array(
					'url'		=> $this->helper->route('lotusjeff_sitemap_sitemap', array('id' => $row['forum_id']), true, '', \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
					'time'		=> $row['forum_last_post_time'],
				);
			}
		}

		/**
		 * If there are no available data, we need to send an error message of no data configured.
		 */
		if (empty($url_data))
		{
			trigger_error('LOTUSJEFF_SITEMAP_NODATA');
		}
		return $this->output_sitemap($url_data, $type = 'sitemapindex');
	}

	/**
	 * Creates Sitemap for individual allowed forums
	 *
	 * @param int		$id		The forum ID
	 * @return object
	 * @access public
	 */
	public function sitemap($id)
	{

		/**
		 * Check if the forum can be accessed via permissions.
		 */
		if (!$this->auth->acl_get('f_list', $id))
		{
			trigger_error('SORRY_AUTH_READ');
		}

		/**
		 * Check if the forum has been excluded
		 */
		if (in_array($id,unserialize($this->config['lotusjeff_sitemap_forum_exclude'])))
		{
			trigger_error('SORRY_AUTH_READ');
		}

		/**
		 * Get forum data
		 */
		$sql = 'SELECT forum_id, forum_last_post_time, forum_topics_approved
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		/**
		 * Check if the forum meets forum threshold
		 */
		if ($row['forum_topics_approved'] < $this->config['lotusjeff_sitemap_forum_threshold'])
		{
			trigger_error('LOTUSJEFF_SITEMAP_NODATA');
		}

		/**
		 * Create forum priority levels
		 */
		$pages = ceil($row['forum_topics_approved'] / $this->config['topics_per_page']);
		$forum_prio = $this->get_prio($row['forum_last_post_time'],$pages);
		/**
		 * Create forum url data
		 */
		$url_data[] = array(
			'url'	=> $this->board_url . '/viewforum.' . $this->php_ext . '?f=' . $id,
			'time'	=> $row['forum_last_post_time'],
			'prio'	=> number_format($forum_prio,1),
			'freq'	=> $this->get_freq($row['forum_last_post_time']),
		);
		/**
		 * Url data for multi-page forums
		 */
		if ($pages > 1)
		{
			$start = 0;
			for ($i = 1; $i < $pages; $i++)
			{
				$start = $start + $this->config['topics_per_page'];
				$url_data[] = array(
					'url'	=> $this->board_url . '/viewforum.' . $this->php_ext . '?f=' . $id . '&amp;start=' . $start,
					'time'	=> $row['forum_last_post_time'],
					'prio'	=> number_format(($forum_prio*0.95),1),
					'freq'	=> $this->get_freq($row['forum_last_post_time']),
				);
			}
		}

		/**
		 * Get all the forum topics.  topics must be:
		 *   - Topic has at least one approved post
		 */
		$sql = 'SELECT topic_id, topic_last_post_time, topic_status, topic_posts_approved, topic_type
			FROM ' . TOPICS_TABLE . '
			WHERE forum_id = ' . (int) $id . ' and topic_posts_approved > 0';
		$result = $this->db->sql_query($sql);

		while ($topic_row = $this->db->sql_fetchrow($result))
		{
			/**
			 * Set the priority of the topic
			 */
			$pages = ceil($topic_row['topic_posts_approved'] / $this->config['posts_per_page']);
			switch ($topic_row['topic_type'])
			{
				case POST_STICKY:
					$topic_priority = $this->config['lotusjeff_sitemap_sticky_priority'];
					break;
				case POST_GLOBAL:
					$topic_priority = $this->config['lotusjeff_sitemap_global_priority'];
					break;
				case POST_ANNOUNCE:
					$topic_priority = $this->config['lotusjeff_sitemap_announce_priority'];
					break;
				default:
					$topic_priority = $this->get_prio($topic_row['topic_last_post_time'], $pages);
			}

			/**
			 * Set topic data for first page of topic
			 */
			if ($topic_row['topic_status'] <> ITEM_MOVED)
			{
				$url_data[] = array(
					'url'	=> $this->board_url .  '/viewtopic.' . $this->php_ext . '?f=' . $id . '&amp;t=' . $topic_row['topic_id'],
					'time'	=> $topic_row['topic_last_post_time'],
					'prio'	=> number_format($topic_priority,1),
					'freq'	=> $this->get_freq($topic_row['topic_last_post_time']),
				);

				/**
				 * Set topic data for pages past 1
				 */
				if ( $pages > 1 )
				{
					$start = 0;
					for ($i = 1; $i < $pages; $i++)
					{
						$start = $start + $this->config['posts_per_page'];
						$url_data[] = array(
							'url'	=> $this->board_url . '/viewtopic.' . $this->php_ext . '?f=' . $id . '&amp;t=' . $topic_row['topic_id'] . '&amp;start=' . $start,
							'time'	=> $topic_row['topic_last_post_time'],
							'prio'	=> number_format(($topic_priority*0.95),1),
							'freq'	=> $this->get_freq($topic_row['topic_last_post_time']),
						);
					}
				}
			}
		}

		/**
		 * If there are no available data, we need to send an error message of no data configured.
		 */
		if (empty($url_data))
		{
			trigger_error('LOTUSJEFF_SITEMAP_NODATA');
		}

		return $this->output_sitemap($url_data, $type = 'urlset');
	}

	/**
	 * Generate the XML sitemap with data from index() and sitemap($id)
	 *
	 * @param array	$url_data
	 * @param string	$type
	 * @return Response
	 * @access private
	 */
	private function output_sitemap($url_data, $type = 'sitemapindex')
	{
		$style_xsl = $this->board_url . '/'. $this->phpbb_extension_manager->get_extension_path('lotusjeff/sitemap', false) . 'styles/all/template/style.xsl';

		/**
		 * Create xml file for sitemap and sitemap index
		 */
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<?xml-stylesheet type="text/xsl" href="' . $style_xsl . '" ?>' . "\n";
		if ($type == 'sitemapindex')
		{
			$xml .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			foreach ($url_data as $data)
			{
				$xml .= '	<sitemap>' . "\n";
				$xml .= '		<loc>' . $data['url'] . '</loc>'. "\n";
				$xml .= ($data['time'] <> 0) ? '		<lastmod>' . gmdate('Y-m-d\TH:i:s+00:00', (int) $data['time']) . '</lastmod>' .  "\n" : '';
				$xml .= '	</sitemap>' . "\n";
			}
		}
		else
		{
			$xml .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			foreach ($url_data as $data)
			{
				$xml .= '	<url>' . "\n";
				$xml .= '		<loc>' . $data['url'] . '</loc>'. "\n";
				$xml .= ($data['time'] <> 0) ? '		<lastmod>' . gmdate('Y-m-d\TH:i:s+00:00', (int) $data['time']) . '</lastmod>' .  "\n" : '';
				$xml .= '		<changefreq>' . $data['freq'] . '</changefreq>' .  "\n";
				$xml .= '		<priority>' . $data['prio'] . '</priority>' .  "\n";
				$xml .= '	</url>' . "\n";
			}
		}
		$xml .= '</' . $type . '>';

		/**
		 * Create headers and send the file
		 */
		$headers = array(
			'Content-Type'		=> 'application/xml; charset=UTF-8',
		);
		return new Response($xml, '200', $headers);
	}

	/**
	 * Generate the frequency value based on loastmodtime
	 *
	 * @param string	$lastmodtime
	 * @return frequency value
	 * @access private
	 */
	private function get_freq($lastmodtime)
	{
		$dt = time() - $lastmodtime;
		// 	42 weeks ~ 10 month		| 8 weeks 			| 15 days			| 2 days		| 12 hours
		return $dt > 25401600 ? 'yearly' : ( $dt > 4838400 ? 'monthly' : ( $dt > 1296000 ? 'weekly' : ( $dt > 172800 ? 'daily' : ( $dt > 43200 ? 'hourly' : 'always' ) ) ) );
	}

	/**
	* get_priority() computes the priority, bases on last mod time and page number
	* Freshest items with most pages gets the highest priority
	* 42 is the answer to the most important question in the universe ;-) From USU and phpBBSEO 3.0.x mod
	 * @param string	$lastmodtime
	 * @param string	number of pages within listing
	 * @return priority value
	 * @access private
	*/
	private function get_prio($lastmodtime, $pages = 1)
	{
		return time() / (time() + (((time() - $lastmodtime)* 42) / $pages));
	}
}
