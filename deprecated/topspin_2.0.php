<?php

class WP_Topspin {
	function getLandingPage() { return $this->getHeader(); }
	function getHeader() {

			// Set requisite global Wordpress SQL variable.
			global $wpdb;
			// This query pulls the header image.
			$query = "
				SELECT ID
				FROM 
					$wpdb->posts 
				WHERE 
					post_title = 'topspin-header'
					AND post_type = 'attachment' 
				LIMIT 1";
			// Ask Wordpress to find the header.
			$header = $wpdb->get_results($query);
			// If the header is found...
			if(!empty($header))
			{
				// Get the post and populate a post array containing the post variables.
				$imagePost = get_post($header[0]->ID);
				// If we are going to wrap the results in a div...
				if($wrap === true)
				{
					// Return the image wrapped in a div.
					$return = '<div id="topspin-header"><img class="topspin-header" src="'. $imagePost->guid . '" alt="Header Image" /></div>';
				}
				// ... otherwise no wrapping.
				else
				{
					// Return the image by itself.
					$return = '<img class="topspin-header" src="'. $imagePost->guid . '" alt="Header Image" />';
				}
					// If we've said the image is within the administrative section, and the user has valid privileges...
				if(($admin === true) && (is_user_logged_in()) && (is_admin()))
				{
					// ... add and edit link.
					$return .= '<p><a href="' . get_edit_post_link($header[0]->ID, 'display') . '" title="Edit site header">Edit site header</a></p>';
				}
			}
			// ... otherwise, return an error.
			else
			{
				// If we are going to wrap the results in a div...
				if($wrap === true)
				{
					// Return the invalid image error wrapped in a div.
					$return = '<div id="topspin-header" class="alert-300"><p>You have not set up your site header.</p><a href="' . get_bloginfo('url') . '/wp-admin/media-new.php" title="Create site header"><p>Click here</a> to upload an image.  <em>Once uploaded, ensure it is named "topspin-header"</em>.</p></div>';
				}
				// ... otherwise no wrapping.
				else
				{
					// Return the invalid image error by itself.
					$return = '<p class="alert-300">You have not set up your site header.  <a href="' . get_bloginfo('url') . '/wp-admin/media-new.php" title="Create site header">Click here</a> to upload an image.  <em>Once uploaded, ensure it is named "topspin-header".</p>';
				}
				
			}
			return $return;
	}
}

?>