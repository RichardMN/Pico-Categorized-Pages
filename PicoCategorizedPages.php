<?php

/**
 * Plugin for categorized pages
 *
 * @author David Boulard
 * @link https://github.com/arckauss/Pico-Categorized-Pages
 * @license http://opensource.org/licenses/MIT
 * @version 1.0.0
 */

class PicoCategorizedPages extends AbstractPicoPlugin
{
    protected $categories = array();
    protected $enabled = true;
    protected $base_url;
    protected $pages_order;
    protected $pages_order_by;
    protected $categories_order;

    /**
     * Triggered after Pico has read its configuration
     *
     * @see    Pico::getConfig()
     * @param  array &$config array of config variables
     * @return void
     */
    public function onConfigLoaded(array &$config)
    {
        $this->base_url = $this->getConfig('base_url');
        $this->pages_order = $this->getConfig('pages_order');
        $this->pages_order_by = $this->getConfig('pages_order_by');
        $this->categories_order = $this->getConfig('categories_order');
    }

    /**
     * Triggered when Pico reads its known meta header fields
     *
     * @see    Pico::getMetaHeaders()
     * @param  string[] &$headers list of known meta header
     *     fields; the array value specifies the YAML key to search for, the
     *     array key is later used to access the found value
     * @return void
     */
    public function onMetaHeaders(array &$headers)
    {
       $headers['position'] = 'Position';
       $headers['page_ignore'] = 'Page_Ignore';
       $headers['category_position'] = 'Category_Position';
       $headers['category_title'] = 'Category_Title';
       $headers['category_titles'] = 'Category_Titles';
       $headers['category_ignore'] = 'Category_Ignore';
	   /* Added for multilingual */
	   $headers['language'] = 'Language';
    }

    public function onPagesLoaded(
    array &$pages,
    array &$currentPage = null,
    array &$previousPage = null,
    array &$nextPage = null
    ) {
        if($this->pages_order_by == 'position') {
		  $current_language = $currentPage['meta']['language'] ? $currentPage['meta']['language'] : 'en';
            $temp_categories = array();
            $ignored_categories = array();

            foreach($pages as $page) {
                $current_category = $this->getCurrentCategoryFromURL($page['url']);

                if($page['meta']['category_ignore'] == true) {
                    array_push($ignored_categories, $current_category);
                }

                if($current_category != ''&& !in_array($current_category, $ignored_categories)
                    && !array_key_exists($current_category, $temp_categories)
                    && $page['meta']['category_position'] != '') {
                        $temp_categories[$current_category]['title'] = $page['meta']['category_title'];
                          if ( is_array( $page['meta']['category_titles'] ) ) {
                          $temp_categories[$current_category]['title'] = $page['meta']['category_titles'][$current_language];
                        }
                        $temp_categories[$current_category]['position'] = $page['meta']['category_position'];
                        if(!$page['meta']['page_ignore']) {
                            $temp_categories[$current_category]['pages'][1]['title'] = $page['title'];
                            $temp_categories[$current_category]['pages'][1]['url'] = $page['url'];
                        }
                }
            }

            foreach($pages as $page) {
                $current_category = $this->getCurrentCategoryFromURL($page['url']);

                if($current_category != ''
                    && !in_array($current_category, $ignored_categories)
                    && array_key_exists($current_category, $temp_categories)
                    && $page['meta']['category_position'] == ''
                    && !$page['meta']['page_ignore']
                    && ($page['meta']['language'] == $current_language )
                   ) {
                        $temp_categories[$current_category]['pages'][$page['meta']['position']]['title'] = $page['title'];
                        $temp_categories[$current_category]['pages'][$page['meta']['position']]['url'] = $page['url'];
                    }
            }

            foreach($temp_categories as $current_category) {
                if(isset($current_category['position'])) {
                    if($this->pages_order == 'desc')
                        krsort($current_category['pages']);
                    else
                        ksort($current_category['pages']);
                    $this->categories[$current_category['position']] = $current_category;
                }
            }

            if($this->categories_order == 'desc')
                krsort($this->categories);
            else
                ksort($this->categories);
        }
    }

    public function onPageRendering(Twig_Environment &$twig, array &$twigVariables, &$templateName)
    {
        if($this->categories)
            $twigVariables['categories'] = $this->categories;
    }

    private function getCurrentCategoryFromURL($url)
    {
        $current_category = '';
        $current_category = explode('/', trim(str_replace($this->base_url, '', urldecode($url)), '/'))[0];
        $current_category = explode('%2F', trim($current_category, '?'))[0];

        return $current_category;
    }
}
