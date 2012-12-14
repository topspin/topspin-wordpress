<?php
if(!class_exists('WP_MediaHandler')) {
	/**
	 * WP_MediaHandler
	 *
	 * @package WordPress
	 * @subpackage MediaHandler
	 */
	class WP_MediaHandler {
	
		/**
		 * Caches a remote image URL to into the WordPress Media Library
		 *
		 * Copies the image specified by a URL into the WordPress media library
		 * and attaches the new media item to a specified post
		 * 
		 * @access public
		 * @static
		 * @param mixed $src
		 * @param mixed $post_ID (default: null)
		 * @return int|bool The new attachment post ID or false on failure
		 */
		public static function saveURL($src, $post_ID) {
			$uploadedFile = self::cacheURL($src);
			// If the file was cached successfully
			if($uploadedFile) {
				$wp_filetype = wp_check_filetype($uploadedFile['filename'], null);
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => $uploadedFile['title'],
					'post_content' => '',
					'post_status' => 'inherit'
				);
				// Create and generate the attachment metadata
				if($post_ID) {
					$attach_id = wp_insert_attachment($attachment, $uploadedFile['path'], $post_ID);
					if(!function_exists('wp_generate_attachment_metadata')) { require_once(sprintf('%swp-admin/includes/image.php', ABSPATH)); }
					$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedFile['path']);
					wp_update_attachment_metadata($attach_id, $attach_data);
					return $attach_id;
				}
			}
			return false;
		}
		
		/**
		 * Saves the image into the WordPress uploads directory
		 * 
		 * @access public
		 * @static
		 * @param string $src
		 * @return array|bool An array containing the path, filename, and title of the new image
		 */
		public static function cacheURL($src) {
			if(strlen($src)) {
				$valid = self::checkRemoteUrl($src);
				if($valid) {
					$wpUploadDir = wp_upload_dir();
					$cachePath = $wpUploadDir['path'];
					$srcLocation = $src;
					//If stripped extension is the same as src basename, than there is no extension, so add one!
					$urlParts = parse_url($srcLocation);
					$pathInfo = pathinfo($urlParts['path']);
					$hasExtension = 0;
					if(isset($pathInfo['extension']) && strlen($pathInfo['extension'])) { $hasExtension = 1; }
					//Set the src basename
					$srcBasename = array_shift(explode('?', basename($srcLocation)));
					//If doens't have an extension, add it!
					if(!$hasExtension) { $srcBasename .= '.jpeg'; }
					//Set the full title name
					$fullFilename = sprintf('%s-%s',time(), $srcBasename);
					$postTitle = preg_replace('/\.[^.]+$/','', $srcBasename);
					if(wp_mkdir_p($cachePath)) {
						$destPath = sprintf('%s/%s', $cachePath, $fullFilename);
						self::copy($srcLocation, $destPath); // copies the remote file
						$file = array(
							'path' => $destPath,
							'filename' => $fullFilename,
							'title' => $postTitle
						);
						return $file;
					}
				}
			}
			return false;
		}
		
		/**
		 * Checks the remote source URL if it returns a valid resource/response
		 *
		 * @access public
		 * @static
		 * @param string $src
		 * @return bool
		 */
		public static function checkRemoteUrl($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec($ch);
			if($res !== false) { return true; }
			else { return false; }
		}
		
		/**
		 * Copes a source location to the destination path
		 *
		 * @param string $srcLocation
		 * @param string $destPath
		 */
		public static function copy($srcLocation, $destPath) {
			// copy($srcLocation, $destPath); // copies source location (deprecated)
			// cUrl method
			$fp = fopen($destPath, 'w');
			$ch = curl_init($srcLocation);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			$data = curl_exec($ch);
			curl_close($ch);
			fclose($fp);
		}

	}
}

?>