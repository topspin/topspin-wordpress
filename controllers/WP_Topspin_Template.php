<?php
/**
 * Handles WordPress template modes
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Template {

	/**
	 * Retrieves the template mode
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function getTemplateMode() {
		return TOPSPIN_TEMPLATE_MODE;
	}

	/**
	 * Retrieves the plugin's template directory
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function getTemplateDirectory() {
		return sprintf('%s%s', TOPSPIN_PLUGIN_PATH, 'templates');
	}
	
	/**
	 * Retrieves the default path to the current template mode
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function getTemplatePath() {
		return sprintf('%s/%s', self::getTemplateDirectory(), self::getTemplateMode());
	}
	
	/**
	 * Retrieves the plugin's template URL location
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function getTemplateLocation() {
		return sprintf('%s/%s/%s', TOPSPIN_PLUGIN_URL, 'templates', self::getTemplateMode());
	}

	/**
	 * Retrieves the theme-side path to the current template mode
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function getThemePath() {
		return (get_stylesheet_directory() . '/topspin/' . self::getTemplateMode());
	}

	/**
	 * Retrieves the theme-side path to the current template mode
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function getThemeLocation() {
		return (get_stylesheet_directory_uri() . '/topspin/' . self::getTemplateMode());
	}
	
	/**
	 * Retrieves the file path
	 * 
	 * @access public
	 * @static
	 * @param string $path
	 * @param string $file
	 * @return string
	 */
	public static function getFilePath($path, $file) {
		return sprintf('%s/%s', $path, $file);
	}

	/**
	 * Checks to see if the specified file exists in the path
	 * 
	 * @access public
	 * @static
	 * @param string $path
	 * @param string $file
	 * @return bool True if the file exists
	 */
	public static function fileExists($path, $file) {
		return (file_exists(self::getFilePath($path, $file))) ? true : false;
	}

	/**
	 * Retrieves the template file (based on the template mode and availability of the theme-side template file
	 * 
	 * @access public
	 * @static
	 * @param mixed $file
	 * @return string The path to the template file
	 */
	public static function getFile($file) {
		$themePath = self::getThemePath();
		$path = $templatePath = self::getTemplatePath();
		// If the theme-side file exists, use it
		if(self::fileExists($themePath, $file)) { $path = $themePath; }
		// If the theme-side file doesn't exist, use the default
		return self::getFilePath($path, $file);
	}
	
	public static function getFileUrl($file) {
		$themePath = self::getThemePath();
		$templatePath = self::getTemplatePath();
		$path = self::getTemplateLocation();
		// If the theme-side file exists, use it
		if(self::fileExists($themePath, $file)) { $path = self::getThemeLocation(); }
		// If the theme-side file doesn't exist, use the default
		return self::getFilePath($path, $file);
	}
	
	/**
	 * Retrieve the output buffer of the specified template file
	 *
	 * @access public
	 * @static
	 * @param string $file				The template file to load
	 * @param array|bool $vars			An array of variables passed to the template
	 * @return string
	 */
	public static function getContents($file, $vars=false) {
		if($vars && is_array($vars)) { extract($vars); }
		ob_start();
		include(WP_Topspin_Template::getFile($file));
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

}

?>