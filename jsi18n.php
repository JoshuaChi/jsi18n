<?php
/**
 * Javascript i18n Helper 
 * 
 * @author Joshua Chi
 **/
class Jsi18nHelper extends AppHelper
{
	var $helpers = array('Javascript');
	
	/**
	 * html tags used by this helper.
	 *
	 * @var array
	 */
		var $tags = array(
			'javascriptblock' => '<script type="text/javascript">%s</script>',
			'javascriptstart' => '<script type="text/javascript">',
			'javascriptlink' => '<script type="text/javascript" src="%s"></script>',
			'javascriptend' => '</script>',
		);
	
	/**
	 * The Filename to store Cached JS i18n files to.
	 *
	 * @access private
	 * @var string
	 **/
	var $__inJSCachePath = null;
	
	/**
	 * strtotime Compatible Time Expression for lifetime of cached JS files
	 *
	 * @var string
	 **/
	var $__cacheTime = '+99 days';

	/**
	 * Initialize i18n languages
	 *
	 * @var array
	 **/
	var $langs = array('en_us','zh_cn');
	
	/**
	 * Name the cache file name you like
	 *
	 * @var string
	 **/
	var $jsi18nFolder = 'jsi18n';
	
	/**
	 * construct the helper and include JSMin.
	 *
	 * @return void
	 **/
	function __construct(){
		parent::__construct();
		$this->__inJSCachePath = $this->jsi18nFolder.DS;
	}
	
	function __writeJSi18nCache($path, $content) {
		if (!is_dir(dirname(CACHE . $path))) {
			mkdir(dirname(CACHE . $path));
		}
		$r = cache($path, $content, $this->__cacheTime);
	}
	
	function __makeCleanJSi18n($data) {
		$output = $this->Javascript->object($data);
		return $output;
	}
	
	function link($jsfile){
		if(!$jsfile){
			return;
		}
		$cachepath = $this->__inJSCachePath.$jsfile;
		if (!($output = cache($cachepath, null, $this->__cacheTime))) {
			$output = '';
			foreach($this->langs as $lang){
				$output .= "var {$lang}_lang = ";
				include_once(APP . 'locale' . DS . $lang . DS . 'lang.php');
				$output .= $this->__makeCleanJSi18n($lang);
				$output .= "; ";
			}
			$this->__writeJSi18nCache($cachepath, $output);
		}
		$out = sprintf($this->tags['javascriptblock'], $output);
		return $out;
	}
}
?>