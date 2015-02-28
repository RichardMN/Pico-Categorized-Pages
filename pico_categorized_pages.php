<?php

/**
 * Pico for categorized pages plugin
 *
 * @author David Boulard
 * @link https://github.com/arckauss/Pico-Categorized-Pages
 * @license http://opensource.org/licenses/MIT
 * @version 0.9.0
 */
class Pico_Categorized_Pages {

	public $categories = array();
	
	public function before_read_file_meta(&$headers)
	{
		$headers['position'] = "Position";
		$headers['category_position'] = "Category_Position";
		$headers['category_title'] = "Category_Title";
	}
	
	public function get_page_data(&$data, $page_meta)
	{
		$data['position'] = isset($page_meta['position']) ? intval($page_meta['position']) : 0;
		$data['category_position'] = isset($page_meta['category_position']) ? intval($page_meta['category_position']) : 0;
		$data['category_title'] = isset($page_meta['category_title']) ? $page_meta['category_title'] : "";
	}
	
	public function get_pages(&$pages, &$current_page, &$prev_page, &$next_page)
	{
		global $config;
		if( $config['pages_order_by'] == 'position' )
		{
			$base_url = $config['base_url'];

			$temp_categories = array();

			foreach($pages as $page)
			{
				$current_category = explode("/", trim(str_replace($base_url, "", $page['url']), "/"))[0];
				if( $current_category != "" )
				{
					if( !array_key_exists($current_category, $temp_categories) )
					{
						$temp_categories[$current_category] = array();
						$temp_categories[$current_category]["pages"] = array();
					}

					if( $page['category_title'] != "" )
					{
						$temp_categories[$current_category]["title"] = $page['category_title'];
						$temp_categories[$current_category]["position"] = $page['category_position'];
						$temp_categories[$current_category]["pages"][1] = $page;
					}
					else
					{
						$temp_categories[$current_category]["pages"][$page['position']] = $page;
					}
				}
			}

			foreach( $temp_categories as $current_category )
			{
				if( isset($current_category['position']) )
				{
					ksort($current_category["pages"]);
					$this->categories[$current_category['position']] = $current_category;
				}
			}

			ksort($this->categories);
		}
	}
	
	public function before_render(&$twig_vars, &$twig, &$template)
	{
		if( $this->categories )
			$twig_vars['categories'] = $this->categories;
	}
}

?>
