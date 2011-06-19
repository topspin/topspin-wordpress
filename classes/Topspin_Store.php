<?php

/*
 *	Class:				Topspin Store
 *
 *	Last Modified:		April 12, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
 *	2011-04-12
 		- updated getStoreItems()
 			added GROUP BY item's ID in manual sorting query string
 *	2011-04-11
 		- new method setError()
 		- new method getError()
 		- updated getFilteredItems()
 			Added the 'default_image', 'default_image_large', and 'images' keys
 			Fixed the campaign object (unserialized to object)
 *	2011-04-08
 		- updated rebuildItems()
 			added caching for 'poster_image_source'
 		- updated getItem()
 			select new field 'poster_image_source'
 		- updated getFilteredItems()
 			select new field 'poster_image_source'
 		- updated getStoreItems()
 			select new field 'poster_image_source'
 			set default image depending on 'poster_image_source'
 		- new method getItemDefaultImage()
 *	2011-04-06
 		- updated setAPICredentials()
 			fixed api_username, api_key
 		- added new method getArtistsList()
 *	2011-04-05
 		- updated getItems()
 			now caches product images to the database in a new table
 		- new method getItemImages()
 		- updated getStoreItems()
 			calls getItemImages()
 		- updated getStoreTags()
 			query string prepare input
 		- updated getStores()
 			query string prepare input
 *	2011-03-23
 		- updated getTagList()
 			set default tag status (php warning fix)
 		- updated rebuildItems()
 			check if tag array exists before calling the loop, checks for offer, and mobile url (php warning fix)
 			recoded method to INSERT ON DUPLICATE KEY UPDATE and deletes old items (that are not found in the API retrieval), now returns a timestamp
 		- updated rebuildAll()
 			updates last cache with returend timestamp from rebuildItems()
 *
 */

class Topspin_Store {

	private $wpdb;
	private $artist_id;
	private $api_key;
	private $api_username;
	private $_error = '';

	private $offer_types = array(
		'key' => array(), //key value pairs
		'data' => array() //table data
	);

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	### GENERAL METHODS
	
	public function setError($msg) {
		$this->_error = $msg;
	}
	
	public function getError() {
		return $this->_error;
	}

	private function cacheImage($url,$width,$square=0) {
		$upload_dir = wp_upload_dir();
		$filepath = $upload_dir["basedir"] . "/topspin/" . md5($url) . "_" . $width . "_" . $square . ".jpg";
		if (!file_exists($filepath)) {
			if (!file_exists($upload_dir["basedir"] . "/topspin/"))
				mkdir($upload_dir["basedir"] . "/topspin");
			$basefilepath = $upload_dir["basedir"] . "/topspin/" . md5($url) . ".jpg";
			if (!file_exists($basefilepath)) {
				// Test if curl is installed, or not like on debian
				if (function_exists('curl_init') ) {
					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
					$rawdata = curl_exec($ch);
					curl_close($ch);
				} else {
				  // get file without curl
				  	$rawdata = file_get_contents(urlencode($url));
				}

				$fp = fopen($basefilepath,'x');
				fwrite($fp, $rawdata);
				fclose($fp);
			}
			$filedata = getimagesize($basefilepath);
			$img = "";
			switch ($filedata["mime"]) {
				case "image/jpeg":
					$img = imagecreatefromjpeg($basefilepath);
					break;
				case "image/png":
					$img = imagecreatefrompng($basefilepath);
					break;
				case "image/gif":
					$img = imagecreatefromgif($basefilepath);
					break;
			}
			if (!empty($img)) {
				$oldw = $filedata[0];
				$oldh = $filedata[1];
				$newh = round($oldh / $oldw * $width);
				$offset1 = 0;
				$offset2 = 0;
				if ($square == 1) {
					$forcedh = round($oldw / 1.3);
					if ($oldh > $forcedh) {
						$offset2 = round(($oldh - $forcedh) / 2);
						$oldh = $forcedh;
					} else {
						$offset1 = round((round($width / 1.3) - $newh) / 2);
						$oldh = $forcedh;
					}
					$newh = round($width / 1.3);
				}
				$dest = ImageCreateTrueColor($width, $newh);
				imagecopyresampled($dest,$img,0,$offset1,0,$offset2,$width,$newh,$oldw,$oldh);
				imagejpeg($dest,$filepath);
				imagedestroy($dest);
			}
		}
		if (!file_exists($filepath)) 
			return $url;
		else
			return $upload_dir["baseurl"] . '/topspin/' . md5($url) . "_" . $width . "_" . $square . ".jpg";
	}
	
	public function setAPICredentials($artist_id,$api_username,$api_key) {
		$this->artist_id = $artist_id;
		$this->api_username = $api_username;
		$this->api_key = $api_key;
	}
	
	### API METHODS
	
	public function checkAPI() {
		##	RETURN
		##		Returns the error object on authorization/misc failure
		$url = 'http://app.topspin.net/api/v1/offers';
		$post_args = array(
			'artist_id' => $this->artist_id
		);
		$data = json_decode($this->process($url,$post_args,false));
		if(isset($data->error_detail) && strlen($data->error_detail)) { return $data; }
	}

	private function process($url,$post_args=null,$post=true) {
		##	PARAMETERS
		##		@url					The URL To make a call to
		##		@post_args				The additional parameters to pass
		##		@post					Whether the type of call is a POST call (false = GET)
		##
		##	RETURN
		##		The returned resource on success
		##		False on failure
		$post_args = (is_array($post_args)) ? $post_args : array();
		## Build URL Query String if Not Post
		if(!$post) {
			if($post_args) {
				$url .= '?';
				$count = 0;
				foreach($post_args as $key=>$value) {
					if($count) { $url .= '&'; }
					$url .= $key.'='.$value;
					$count++;
				}
			}
		}
		// Use curl if installed on the system
		if (function_exists('curl_init') ) {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_USERPWD,$this->api_username.':'.$this->api_key);
			curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
			if($post) {
				curl_setopt($ch,CURLOPT_POST,true);
				curl_setopt($ch,CURLOPT_POSTFIELDS,$post_args);
			}
			$res = curl_exec($ch);
			$res_info = curl_getinfo($ch);
			$res_error = curl_error($ch);
			## CURL ERROR
			if($res_error) {
				$ts = $res_error;
				$res = '{"error_detail":"'.$ts.'","request_url":"'.$url.'"}';
			}
			else {
				## RESPONSE ERROR
				if($res_info['http_code']!=200) {
					$ts = '';
					switch($res_info['http_code']) {
						case '401':
							$ts = '401 Unauthorized request. Please check your API username and key.';
							break;
						case '404':
							$ts = '404 Target not found.';
							break;
						case '500':
							$ts = '500 Internal server error.';
							break;
						default:
							$ts = $res['http_code'].' Unknown error.';
							break;
					}
					$res = '{"error_detail":"'.$ts.'","request_url":"'.$url.'"}';
				}
			}
			curl_close($ch);
		} else {
			$context = @stream_context_create( array(
				'http' => array(
					'method' => 'POST',
					'header' => "Authorization: Basic " .
						base64_encode("$this->api_username:$this->api_key").
						"\r\nContent-type: application/x-www-form-urlencoded\r\n",
					'content' => $post_args,
				)
			));
			// get file without curl
	  		$res = file_get_contents(urlencode($url), false, $context);
			if ( $res === flase ) {
				// handle errors
				$ts = $http_response_header[0];
				if ( ereg( '^HTTP\\[0-9]+\.[0-9]+ 401 .*', $ts ) )
					$ts = '401 Unauthorized request. Please check your API username and key.';
				$res = '{"error_detail":"'.$ts.'","request_url":"'.$url.'"}';
			}
		}
		if($res) { return $res; }
	}
	
	public function getArtists() {
		##	Retrieves the list of artists from the Artist Search API
		##	https://docs.topspin.net/tiki-index.php?page=Artist+Search+API
		##
		##	RETURN
		##		A standard object containing the artist data
		$url = 'http://app.topspin.net/api/v1/artist';
		$data = json_decode($this->process($url,null,false));
		$artists = array();
		if(isset($data->artists) && count($data->artists)) {
			foreach($data->artists as $item) {
				array_push($artists,$item);
			}
		}
		return $artists;
	}
	
	public function getArtist() {
		##	Retrieves the artist information from the Artist Search API
		##	https://docs.topspin.net/tiki-index.php?page=Artist+Search+API
		##
		##	RETURN
		##		A standard object containing the artist data
		$url = 'http://app.topspin.net/api/v1/artist';
		$data = json_decode($this->process($url,null,false));
		$artist = null;
		if(isset($data->artists) && count($data->artists)) {
			foreach($data->artists as $item) {
				if($item->id==$this->artist_id) {
					$artist = $item;
					break;
				}
			}
		}
		return $artist;
	}
	
	public function getTotalPages() {
		##	Retrieves the items/products/offers from the Store API
		##	https://docs.topspin.net/tiki-index.php?page=Store+API
		##
		##	RETURN
		##		An array containing the list of items
		$url = 'http://app.topspin.net/api/v1/offers';
		$post_args = array(
			'artist_id' => $this->artist_id
		);
		$data = json_decode($this->process($url,$post_args,false));
		if(isset($data->total_pages)) { return $data->total_pages; }
	}

	public function getItems($page=1) {
		##	Retrieves the items/products/offers from the Store API
		##	https://docs.topspin.net/tiki-index.php?page=Store+API
		##
		##	PARAMETERS
		##		@page				Requested page number.
		##		@offer_type			Return offers of the given type. Valid types are: buy_button, email_for_media, bundle_widget (multi-track streaming player in the app) or single_track_player_widget.
		##		@product_type		Return offers for the given product type. Valid types: image, video, track, album, package, other_media, merchandise.
		##		@tags				Select spins by tag. Include multiple tags by separating them with a comma.
		##
		##	RETURN
		##		An array containing the list of items
		$url = 'http://app.topspin.net/api/v1/offers';
		$post_args = array(
			'artist_id' => $this->artist_id,
			'page' => $page
		);
		$data = json_decode($this->process($url,$post_args,false));
		if($data) { return $data->offers; }
	}

	public function getTags() {
		##	Retrieves all spin tags under the account
		##
		##	RETURN
		##		An array containing all of the spin tags
		$artist = $this->getArtist();
		if($artist) { return $artist->spin_tags; }
	}
	
	### REBUILD CACHE METHODS
	
	public function rebuildAll() {
		##	Rebuilds the items, and tags database tables
		$timestamp = $this->rebuildItems();
		$this->rebuildArtists();
		$this->rebuildTags();
		$this->setSetting('topspin_last_cache_all',$timestamp);
	}

	public function rebuildItems() {
		##	Rebuild and syncs the items table with Topspin
		##
		##	RETURNS
		##		The last modified timestamp
		$lastModified = time();
		$addedIDs = array();
		$totalPages = $this->getTotalPages();
		if($totalPages) {
			for($i=1;$i<=$totalPages;$i++) {
				$items = $this->getItems($i);
				foreach($items as $item) {
					$data = array(
						'id' => $item->id,
						'artist_id' => $item->artist_id,
						'reporting_name' => $item->reporting_name,
						'embed_code' => $item->embed_code,
						'width' => $item->width,
						'height' => $item->height,
						'url' => $item->url,
						'poster_image' => $item->poster_image,
						'poster_image_source' => (isset($item->poster_image_source)) ? $item->poster_image_source : '',
						'product_type' => $item->product_type,
						'offer_type' => $item->offer_type,
						'description' => $item->description,
						'currency' => $item->currency,
						'price' => $item->price,
						'name' => $item->name,
						'campaign' => serialize($item->campaign),
						'offer_url' => (isset($item->offer_url)) ? $item->offer_url : '',
						'mobile_url' => (isset($item->mobile_url)) ? $item->mobile_url : '',
						'last_modified' => date('Y-m-d H:i:s',$lastModified)
					);
					$format = array('%d','%d','%s','%s','%d','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');

					## Get Keys
					$tableFields = implode(',',array_keys($data));
					$tableFormat = implode(',',$format);
					$onDuplicateKeyUpdate = '';
					$keyIndex = 0;
					foreach($data as $key=>$field) {
						if($keyIndex>0) { $onDuplicateKeyUpdate .= ', '; }
						$onDuplicateKeyUpdate .= $key.'=VALUES('.$key.')';
						$keyIndex++;
					}
					## Add item
					$sql = <<<EOD
					INSERT INTO {$this->wpdb->prefix}topspin_items ({$tableFields}) VALUES ({$tableFormat})
					ON DUPLICATE KEY UPDATE {$onDuplicateKeyUpdate}
EOD;
					$this->wpdb->query($this->wpdb->prepare($sql,$data));

					##	Deletes old item tags
					$sql = 'DELETE FROM '.$this->wpdb->prefix.'topspin_items_tags WHERE item_id = %d';
					$this->wpdb->query($this->wpdb->prepare($sql,array($item->id)));
					##	Adds new item tag
					if(isset($item->tags) && is_array($item->tags)) {
						$tagFormat = array('%d','%s');
						foreach($item->tags as $tag) {
							$tagData = array(
								'item_id' => $item->id,
								'tag_name' => $tag
							);
							$this->wpdb->insert($this->wpdb->prefix.'topspin_items_tags',$tagData,$tagFormat);
						}
					} // end if tags exist

					##	Deletes old item images
					$sql = 'DELETE FROM '.$this->wpdb->prefix.'topspin_items_images WHERE item_id = %d';
					$this->wpdb->query($this->wpdb->prepare($sql,array($item->id)));
					##	Adds new item images
					if(isset($item->campaign->product->images) && is_array($item->campaign->product->images)) {
						$imageFormat = array('%d','%s','%s','%s','%s');
						foreach($item->campaign->product->images as $key=>$image) {
							$imageData = array(
								'item_id' => $item->id,
								'source_url' => $item->campaign->product->images[$key]->source_url,
								'small_url' => $item->campaign->product->images[$key]->small_url,
								'medium_url' => $item->campaign->product->images[$key]->medium_url,
								'large_url' => $item->campaign->product->images[$key]->large_url
							);
							$this->wpdb->insert($this->wpdb->prefix.'topspin_items_images',$imageData,$imageFormat);
						}
					} //end if images exist

					array_push($addedIDs,$item->id);
				} //end for each item
			} //end for each page

			##	Removes all items that is not modified/inserted
			$sql = 'DELETE FROM '.$this->wpdb->prefix.'topspin_items WHERE last_modified < %s';
			$this->wpdb->query($this->wpdb->prepare($sql,array(date('Y-m-d H:i:s',$lastModified))));

			##	Removes all item tags that is not modified/inserted
			$sql = 'DELETE FROM '.$this->wpdb->prefix.'topspin_items_tags WHERE item_id NOT IN (SELECT id FROM '.$this->wpdb->prefix.'topspin_items)';
			$this->wpdb->query($this->wpdb->prepare($sql));

			##	Removes all item images that is not modified/inserted
			$sql = 'DELETE FROM '.$this->wpdb->prefix.'topspin_items_images WHERE item_id NOT IN (SELECT id FROM '.$this->wpdb->prefix.'topspin_items)';
			$this->wpdb->query($this->wpdb->prepare($sql));
			return $lastModified;
		}
	}
	
	public function rebuildArtists() {
		##	Rebuild and syncs the artsits table with Topspin
		$sql = 'TRUNCATE TABLE '.$this->wpdb->prefix.'topspin_artists';
		$this->wpdb->query($sql);
		
		$artists = $this->getArtists();
		if($artists && count($artists)) {
			foreach($artists as $artist) {
				$data = array(
					'id' => $artist->id,
					'name' => $artist->name,
					'avatar_image' => $artist->avatar_image,
					'url' => $artist->url,
					'description' => $artist->description,
					'website' => $artist->website
				);
				$this->wpdb->insert($this->wpdb->prefix.'topspin_artists',$data,array('%d','%s','%s','%s','%s','%s'));
			}
		}
	}

	public function rebuildTags() {
		##	Rebuild and syncs the tags table with Topspin
		$sql = 'TRUNCATE TABLE '.$this->wpdb->prefix.'topspin_tags';
		$this->wpdb->query($sql);

		$tags = $this->getTags();
		if($tags && count($tags)) {
			foreach($tags as $tag) {
				$data = array('name' => strtolower($tag));
				$this->wpdb->insert($this->wpdb->prefix.'topspin_tags',$data,array('%s'));
			}
		}
	}
	
	#### SETTINGS METHODS
	
	public function setSetting($name,$value) {
		##	Sets the value of a specified setting
		##
		##	PARAMETERS
		##		@name				The name of the settings key
		##		@value				The value to set
		##
		##	RETURN
		##		False
		if(!$this->settingExist($name)) {
			$data = array(
				'name' => $name,
				'value' => $value
			);
			$this->wpdb->insert($this->wpdb->prefix.'topspin_settings',$data,array('%s','%s'));
		}
		else {
			$this->wpdb->update($this->wpdb->prefix.'topspin_settings',array('value'=>$value),array('name'=>$name),array('%s'));
		}
		##	API Credentials
		switch($name) {
			case 'topspin_artist_id':
				$this->artist_id = $value;
				break;
			case 'topspin_api_key':
				$this->api_key = $value;
				break;
			case 'topspin_api_username':
				$this->api_username = $value;
				break;
		}
	}
	
	public function getSetting($name) {
		##	Retrieves the value of a specified setting
		##
		##	PARAMETERS
		##		@name				The name of the settings key
		##
		##	RETURN
		##		The value of the selected setting on success
		##		False on failure
		$sql = <<<EOD
		SELECT
			value
		FROM
			{$this->wpdb->prefix}topspin_settings
		WHERE
			name = %s
EOD;
		return $this->wpdb->get_var($this->wpdb->prepare($sql,$name));
	}
	
	public function settingExist($name) {
		##	Checks if a specified setting exists
		##
		##	PARAMETERS
		##		@name				The name of the settings key
		##
		##	RETURN
		##		The count number of the specified settings key
		$sql = <<<EOD
		SELECT
			COUNT(id)
		FROM
			{$this->wpdb->prefix}topspin_settings
		WHERE
			name = %s
EOD;
		return $this->wpdb->get_var($this->wpdb->prepare($sql,$name));
	}

	public function getStores($status='publish') {
		##	Retrieves a list of Stores
		##
		##	PARAMETERS
		##		@status				Enumeration: publish, trash
		##
		##	RETURN
		##		The stores table as a multi-dimensional array
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}posts.ID,
			{$this->wpdb->prefix}posts.post_title,
			{$this->wpdb->prefix}posts.post_name,
			{$this->wpdb->prefix}topspin_stores.store_id,
			{$this->wpdb->prefix}topspin_stores.status,
			{$this->wpdb->prefix}topspin_stores.created_date,
			{$this->wpdb->prefix}topspin_stores.items_per_page,
			{$this->wpdb->prefix}topspin_stores.show_all_items,
			{$this->wpdb->prefix}topspin_stores.grid_columns,
			{$this->wpdb->prefix}topspin_stores.default_sorting,
			{$this->wpdb->prefix}topspin_stores.default_sorting_by,
			{$this->wpdb->prefix}topspin_stores.items_order
		FROM {$this->wpdb->prefix}topspin_stores
		LEFT JOIN
			{$this->wpdb->prefix}posts ON {$this->wpdb->prefix}topspin_stores.post_id = {$this->wpdb->prefix}posts.ID
		WHERE
			{$this->wpdb->prefix}topspin_stores.status = '%s'
EOD;
		return $this->wpdb->get_results($this->wpdb->prepare($sql,array($status)));
	}
	
	public function createStore($post,$page_id) {
		##	Creates the Store entry with the attached Page
		##
		##	PARAMETERS
		##		@post				The post data array
		##		@page_id			The newly created page ID to tie this store to
		##
		##	RETURN
		##		The newly created store ID	
		$data = array(
			'post_id' => $page_id,
			'status' => 'publish',
			'items_per_page' => $post['items_per_page'],
			'show_all_items' => $post['show_all_items'],
			'grid_columns' => $post['grid_columns'],
			'default_sorting' => $post['default_sorting'],
			'default_sorting_by' => $post['default_sorting_by'],
			'items_order' => $post['items_order'],
			'featured_item' => $post['featured_item']
		);
		$this->wpdb->insert($this->wpdb->prefix.'topspin_stores',$data,array('%d','%s','%d','%d','%d','%s','%s','%s','%d'));
		$store_id = $this->wpdb->insert_id;
		## Add Offer Types
		$this->createStoreOfferTypes($post['offer_types'],$store_id);
		## Add Tags
		$this->createStoreTags($post['tags'],$store_id);
		return $store_id;
	}
	
	public function createStoreOfferTypes($offer_types,$store_id) {
		##	Adds the order offer types into the database
		##
		##	PARAMETERS
		##		@offer_types		The offer type array
		##		@store_id			The storeID
		$key = 0;
		$types_added = array();
		## Add active types
		foreach($offer_types as $type) {
			$data = array(
				'store_id' => $store_id,
				'type' => $type,
				'order_num' => $key,
				'status' => 1
			);
			$format = array(
				'%d',
				'%s',
				'%d',
				'%d'
			);
			$this->wpdb->insert($this->wpdb->prefix.'topspin_stores_offer_type',$data,$format);
			$key++;
			$types_added[] = $type;
		}
		## Add inactive types
		$types_list = $this->getOfferTypes();
		foreach($types_list as $type) {
			if(!in_array($type['type'],$types_added)) {
				$data = array(
					'store_id' => $store_id,
					'type' => $type['type'],
					'order_num' => $key,
					'status' => 0
				);
				$format = array(
					'%d',
					'%s',
					'%d',
					'%d'
				);
				$this->wpdb->insert($this->wpdb->prefix.'topspin_stores_offer_type',$data,$format);
				$key++;
				$types_added[] = $type;
			}
		}
	}
	
	public function createStoreTags($tags,$store_id) {
		##	Adds the order tags into the database
		##
		##	PARAMETERS
		##		@tags				The tags array
		##		@store_id			The storeID
		$key = 0;
		$tags_added = array();
		## Add active tags
		foreach($tags as $tag) {
			$data = array(
				'store_id' => $store_id,
				'tag' => $tag,
				'order_num' => $key,
				'status' => 1
			);
			$format = array(
				'%d',
				'%s',
				'%d',
				'%d'
			);
			$this->wpdb->insert($this->wpdb->prefix.'topspin_stores_tag',$data,$format);
			$key++;
			$tags_added[] = $tag;
		}
		## Add inactive tags
		$tags_list = $this->getTagList();
		foreach($tags_list as $tag) {
			if(!in_array($tag['name'],$tags_added)) {
				$data = array(
					'store_id' => $store_id,
					'tag' => $tag['name'],
					'order_num' => $key,
					'status' => 0
				);
				$format = array(
					'%d',
					'%s',
					'%d',
					'%d'
				);
				$this->wpdb->insert($this->wpdb->prefix.'topspin_stores_tag',$data,$format);
				$key++;
				$tags_list[] = $tag['name'];
			}
		}
	}
	
	public function updateStore($post,$store_id) {
		##	Updates the specified store entry
		##
		##	PARAMETERS
		##		@post				The store data array
		##		@store_id			The store ID to edit
		##
		##	RETURN
		##		True on success
		##		False on failure
		$data = array(
			'items_per_page' => $post['items_per_page'],
			'show_all_items' => $post['show_all_items'],
			'grid_columns' => $post['grid_columns'],
			'default_sorting' => $post['default_sorting'],
			'default_sorting_by' => $post['default_sorting_by'],
			'items_order' => $post['items_order'],
			'featured_item' => $post['featured_item']
		);
		$this->wpdb->update($this->wpdb->prefix.'topspin_stores',$data,array('store_id'=>$store_id),array('%d','%d','%d','%s','%s','%s','%d'),array('%d'));
		## Add Offer Types
		$this->updateStoreOfferTypes($post['offer_types'],$store_id);
		## Add Tags
		$this->updateStoreTags($post['tags'],$store_id);
	}
	
	public function updateStoreOfferTypes($offer_types,$store_id) {
		##
		##	PARAMETERS
		##		@offer_types		The offer type array
		##		@store_Id			The store ID
		$sql = 'DELETE FROM '.$this->wpdb->prefix.'topspin_stores_offer_type WHERE store_id='.$store_id;
		$this->wpdb->query($sql);
		$this->createStoreOfferTypes($offer_types,$store_id);
	}
	
	public function updateStoreTags($tags,$store_id) {
		##
		##	PARAMETERS
		##		@tags				The tags array
		##		@store_Id			The store ID
		$sql = 'DELETE FROM '.$this->wpdb->prefix.'topspin_stores_tag WHERE store_id='.$store_id;
		$this->wpdb->query($sql);
		$this->createStoreTags($tags,$store_id);
	}
	
	public function deleteStore($store_id) {
		##	Deletes a trash (sends to trash)
		##
		##	PARAMETERS
		##		@store_id			The store ID to set status as "trash"
		##
		##	RETURN
		##		True
		$this->wpdb->update($this->wpdb->prefix.'topspin_stores',array('status'=>'trash'),array('store_id'=>$store_id),array('%s'),array('%d'));
		return true;
	}

	public function getStore($store_id) {
		##	Retrieves the Store entry with the attached Page
		##
		##	PARAMETERS
		##		@store_id			The store ID
		##
		##	RETURN
		##		The store object array
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}posts.ID AS post_id,
			{$this->wpdb->prefix}posts.post_title AS name,
			{$this->wpdb->prefix}posts.post_name AS slug,
			{$this->wpdb->prefix}topspin_stores.store_id AS id,
			{$this->wpdb->prefix}topspin_stores.status,
			{$this->wpdb->prefix}topspin_stores.created_date,
			{$this->wpdb->prefix}topspin_stores.items_per_page,
			{$this->wpdb->prefix}topspin_stores.show_all_items,
			{$this->wpdb->prefix}topspin_stores.grid_columns,
			{$this->wpdb->prefix}topspin_stores.default_sorting,
			{$this->wpdb->prefix}topspin_stores.default_sorting_by,
			{$this->wpdb->prefix}topspin_stores.items_order,
			{$this->wpdb->prefix}topspin_stores.featured_item
		FROM {$this->wpdb->prefix}topspin_stores
		LEFT JOIN
			{$this->wpdb->prefix}posts ON {$this->wpdb->prefix}topspin_stores.post_id = {$this->wpdb->prefix}posts.ID
		WHERE
			{$this->wpdb->prefix}topspin_stores.store_id = '%d'
EOD;
		$data = $this->wpdb->get_row($this->wpdb->prepare($sql,array($store_id)),ARRAY_A);
		## Get Offer Types
		$data['offer_types'] = $this->getStoreOfferTypes($data['id']);
		## Get Tags
		$data['tags'] = $this->getStoreTags($data['id']);
		return $data;
	}
	
	public function getStoreId($post_id) {
		##	Retrieves the Store ID by Post ID
		##
		##	PARAMETERS
		##		@post_id
		##
		##	RETURN
		##		The store ID
		$sql = <<<EOD
		SELECT
			store_id
		FROM {$this->wpdb->prefix}topspin_stores
		WHERE
			post_id = {$post_id}
EOD;
		$data = $this->wpdb->get_var($sql);
		return $data;
	}
	
	public function getStoreItems($store_id,$show_hidden=true,$artist_id=null) {
		##	Retrieves the items list from the specified store
		##
		##	PARAMETERS
		##		@store_id			The store ID
		##		@show_hidden		(Optional) Boolean to show hidden manual items
		##		@artist_id			(Optional) The artist's ID
		##
		##	RETURN
		##		The items table as a multi-dimensional array
		if(!$artist_id) { $artist_id = $this->artist_id; }
		$storeData = $this->getStore($store_id);
		$addedIDs = array();
		$sortedItems = array();
		## In Offer Types
		$in_offer_type = '';
		$total_offer_types = 0;
		foreach($storeData['offer_types'] as $key=>$offer_type) {
			if($offer_type['status']) {
				$total_offer_types++;
				if($key==0) { $in_offer_type .= '\''.$offer_type['type'].'\''; }
				else { $in_offer_type .= ', \''.$offer_type['type'].'\''; }
			}
		}
		$WHERE_IN_OFFER_TYPE  = ($total_offer_types) ? ' AND '.$this->wpdb->prefix.'topspin_items.offer_type IN ('.$in_offer_type.')' : '';
		## In Tags
		$in_tags = '';
		$total_tags = 0;
		foreach($storeData['tags'] as $key=>$tag) {
			if($tag['status']) {
				$total_tags++;
				if($key==0) { $in_tags .= '\''.$tag['name'].'\''; }
				else { $in_tags .= ', \''.$tag['name'].'\''; }
			}
		}
		$WHERE_IN_TAGS  = ($total_tags) ? ' AND '.$this->wpdb->prefix.'topspin_items_tags.tag_name IN ('.$in_tags.')' : '';
		## Order By
		$order_by = ($storeData['default_sorting']=='alphabetical') ? $this->wpdb->prefix.'topspin_items.name ASC' : $this->wpdb->prefix.'topspin_items.id ASC';
		## Switch Sorting By
		switch($storeData['default_sorting_by']) {
			case "offertype":
				## Fetch By Offer Type
				## If an offer type is checked, filter by offer type
				if($total_offer_types) {
					foreach($storeData['offer_types'] as $offer_type) {
						$sql = <<<EOD
						SELECT
							{$this->wpdb->prefix}topspin_items.id,
							{$this->wpdb->prefix}topspin_items.artist_id,
							{$this->wpdb->prefix}topspin_items.reporting_name,
							{$this->wpdb->prefix}topspin_items.embed_code,
							{$this->wpdb->prefix}topspin_items.width,
							{$this->wpdb->prefix}topspin_items.height,
							{$this->wpdb->prefix}topspin_items.url,
							{$this->wpdb->prefix}topspin_items.poster_image,
							{$this->wpdb->prefix}topspin_items.poster_image_source,
							{$this->wpdb->prefix}topspin_items.product_type,
							{$this->wpdb->prefix}topspin_items.offer_type,
							{$this->wpdb->prefix}topspin_offer_types.name AS offer_type_name,
							{$this->wpdb->prefix}topspin_items.description,
							{$this->wpdb->prefix}topspin_items.price,
							{$this->wpdb->prefix}topspin_items.name,
							{$this->wpdb->prefix}topspin_items.campaign,
							{$this->wpdb->prefix}topspin_items.offer_url,
							{$this->wpdb->prefix}topspin_items.mobile_url,
							{$this->wpdb->prefix}topspin_items_tags.tag_name,
							{$this->wpdb->prefix}topspin_currency.currency,
							{$this->wpdb->prefix}topspin_currency.symbol
						FROM
							{$this->wpdb->prefix}topspin_items
						LEFT JOIN
							{$this->wpdb->prefix}topspin_items_tags ON {$this->wpdb->prefix}topspin_items.id = {$this->wpdb->prefix}topspin_items_tags.item_id
						LEFT JOIN
							{$this->wpdb->prefix}topspin_currency ON {$this->wpdb->prefix}topspin_items.currency = {$this->wpdb->prefix}topspin_currency.currency
						LEFT JOIN
							{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_items.offer_type = {$this->wpdb->prefix}topspin_offer_types.type
						WHERE
							{$this->wpdb->prefix}topspin_items.artist_id = {$artist_id}
							AND {$this->wpdb->prefix}topspin_items.offer_type = '{$offer_type['type']}'
							{$WHERE_IN_TAGS}
						ORDER BY
							{$order_by}
EOD;
						$result = $this->wpdb->get_results($sql,ARRAY_A);
						foreach($result as $row) {
							## If not yet in the sorted items list
							if(!in_array($row['id'],$addedIDs)) {
								array_push($sortedItems,$row);
								array_push($addedIDs,$row['id']);
							}
						}
					}
				}
				else {
					$sql = <<<EOD
					SELECT
						{$this->wpdb->prefix}topspin_items.id,
						{$this->wpdb->prefix}topspin_items.artist_id,
						{$this->wpdb->prefix}topspin_items.reporting_name,
						{$this->wpdb->prefix}topspin_items.embed_code,
						{$this->wpdb->prefix}topspin_items.width,
						{$this->wpdb->prefix}topspin_items.height,
						{$this->wpdb->prefix}topspin_items.url,
						{$this->wpdb->prefix}topspin_items.poster_image,
						{$this->wpdb->prefix}topspin_items.poster_image_source,
						{$this->wpdb->prefix}topspin_items.product_type,
						{$this->wpdb->prefix}topspin_items.offer_type,
						{$this->wpdb->prefix}topspin_offer_types.name AS offer_type_name,
						{$this->wpdb->prefix}topspin_items.description,
						{$this->wpdb->prefix}topspin_items.price,
						{$this->wpdb->prefix}topspin_items.name,
						{$this->wpdb->prefix}topspin_items.campaign,
						{$this->wpdb->prefix}topspin_items.offer_url,
						{$this->wpdb->prefix}topspin_items.mobile_url,
						{$this->wpdb->prefix}topspin_items_tags.tag_name,
						{$this->wpdb->prefix}topspin_currency.currency,
						{$this->wpdb->prefix}topspin_currency.symbol
					FROM
						{$this->wpdb->prefix}topspin_items
					LEFT JOIN
						{$this->wpdb->prefix}topspin_items_tags ON {$this->wpdb->prefix}topspin_items.id = {$this->wpdb->prefix}topspin_items_tags.item_id
					LEFT JOIN
						{$this->wpdb->prefix}topspin_currency ON {$this->wpdb->prefix}topspin_items.currency = {$this->wpdb->prefix}topspin_currency.currency
					LEFT JOIN
						{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_items.offer_type = {$this->wpdb->prefix}topspin_offer_types.type
					WHERE
						{$this->wpdb->prefix}topspin_items.artist_id = {$artist_id}
						{$WHERE_IN_TAGS}
					ORDER BY
						{$order_by}
EOD;
					$result = $this->wpdb->get_results($sql,ARRAY_A);
					foreach($result as $row) {
						## If not yet in the sorted items list
						if(!in_array($row['id'],$addedIDs)) {
							array_push($sortedItems,$row);
							array_push($addedIDs,$row['id']);
						}
					}
				}
				break;
			case "tag":
				## Fetch By Tags
				## If a Tag is checked, filter by tags
				if($total_tags) {
					foreach($storeData['tags'] as $key=>$tag) {
						if($tag['status']) {
							$sql = <<<EOD
							SELECT
								{$this->wpdb->prefix}topspin_items.id,
								{$this->wpdb->prefix}topspin_items.artist_id,
								{$this->wpdb->prefix}topspin_items.reporting_name,
								{$this->wpdb->prefix}topspin_items.embed_code,
								{$this->wpdb->prefix}topspin_items.width,
								{$this->wpdb->prefix}topspin_items.height,
								{$this->wpdb->prefix}topspin_items.url,
								{$this->wpdb->prefix}topspin_items.poster_image,
								{$this->wpdb->prefix}topspin_items.poster_image_source,
								{$this->wpdb->prefix}topspin_items.product_type,
								{$this->wpdb->prefix}topspin_items.offer_type,
								{$this->wpdb->prefix}topspin_offer_types.name AS offer_type_name,
								{$this->wpdb->prefix}topspin_items.description,
								{$this->wpdb->prefix}topspin_items.price,
								{$this->wpdb->prefix}topspin_items.name,
								{$this->wpdb->prefix}topspin_items.campaign,
								{$this->wpdb->prefix}topspin_items.offer_url,
								{$this->wpdb->prefix}topspin_items.mobile_url,
								{$this->wpdb->prefix}topspin_items_tags.tag_name,
								{$this->wpdb->prefix}topspin_currency.currency,
								{$this->wpdb->prefix}topspin_currency.symbol
							FROM
								{$this->wpdb->prefix}topspin_items
							LEFT JOIN
								{$this->wpdb->prefix}topspin_items_tags ON {$this->wpdb->prefix}topspin_items.id = {$this->wpdb->prefix}topspin_items_tags.item_id
							LEFT JOIN
								{$this->wpdb->prefix}topspin_currency ON {$this->wpdb->prefix}topspin_items.currency = {$this->wpdb->prefix}topspin_currency.currency
							LEFT JOIN
								{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_items.offer_type = {$this->wpdb->prefix}topspin_offer_types.type
							WHERE
								{$this->wpdb->prefix}topspin_items.artist_id = {$artist_id}
								AND {$this->wpdb->prefix}topspin_items_tags.tag_name = '{$tag['name']}'
								{$WHERE_IN_OFFER_TYPE}
							ORDER BY
								{$order_by}
							{$LIMIT}
EOD;
							$result = $this->wpdb->get_results($sql,ARRAY_A);
							foreach($result as $row) {
								## If not yet in the sorted items list
								if(!in_array($row['id'],$addedIDs)) {
									array_push($sortedItems,$row);
									array_push($addedIDs,$row['id']);
								}
							}
						}
					}
				}
				## Else, do not filter tags and show all
				else {
					$sql = <<<EOD
					SELECT
						{$this->wpdb->prefix}topspin_items.id,
						{$this->wpdb->prefix}topspin_items.artist_id,
						{$this->wpdb->prefix}topspin_items.reporting_name,
						{$this->wpdb->prefix}topspin_items.embed_code,
						{$this->wpdb->prefix}topspin_items.width,
						{$this->wpdb->prefix}topspin_items.height,
						{$this->wpdb->prefix}topspin_items.url,
						{$this->wpdb->prefix}topspin_items.poster_image,
						{$this->wpdb->prefix}topspin_items.poster_image_source,
						{$this->wpdb->prefix}topspin_items.product_type,
						{$this->wpdb->prefix}topspin_items.offer_type,
						{$this->wpdb->prefix}topspin_offer_types.name AS offer_type_name,
						{$this->wpdb->prefix}topspin_items.description,
						{$this->wpdb->prefix}topspin_items.price,
						{$this->wpdb->prefix}topspin_items.name,
						{$this->wpdb->prefix}topspin_items.campaign,
						{$this->wpdb->prefix}topspin_items.offer_url,
						{$this->wpdb->prefix}topspin_items.mobile_url,
						{$this->wpdb->prefix}topspin_items_tags.tag_name,
						{$this->wpdb->prefix}topspin_currency.currency,
						{$this->wpdb->prefix}topspin_currency.symbol
					FROM
						{$this->wpdb->prefix}topspin_items
					LEFT JOIN
						{$this->wpdb->prefix}topspin_items_tags ON {$this->wpdb->prefix}topspin_items.id = {$this->wpdb->prefix}topspin_items_tags.item_id
					LEFT JOIN
						{$this->wpdb->prefix}topspin_currency ON {$this->wpdb->prefix}topspin_items.currency = {$this->wpdb->prefix}topspin_currency.currency
					LEFT JOIN
						{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_items.offer_type = {$this->wpdb->prefix}topspin_offer_types.type
					WHERE
						{$this->wpdb->prefix}topspin_items.artist_id = {$artist_id}
						{$WHERE_IN_OFFER_TYPE}
					ORDER BY
						{$order_by}
EOD;
					$result = $this->wpdb->get_results($sql,ARRAY_A);
					foreach($result as $row) {
						## If not yet in the sorted items list
						if(!in_array($row['id'],$addedIDs)) {
							array_push($sortedItems,$row);
							array_push($addedIDs,$row['id']);
						}
					}
				}
				break;
			case "manual":
				$sql = <<<EOD
				SELECT
					{$this->wpdb->prefix}topspin_items.id,
					{$this->wpdb->prefix}topspin_items.artist_id,
					{$this->wpdb->prefix}topspin_items.reporting_name,
					{$this->wpdb->prefix}topspin_items.embed_code,
					{$this->wpdb->prefix}topspin_items.width,
					{$this->wpdb->prefix}topspin_items.height,
					{$this->wpdb->prefix}topspin_items.url,
					{$this->wpdb->prefix}topspin_items.poster_image,
					{$this->wpdb->prefix}topspin_items.poster_image_source,
					{$this->wpdb->prefix}topspin_items.product_type,
					{$this->wpdb->prefix}topspin_items.offer_type,
					{$this->wpdb->prefix}topspin_offer_types.name AS offer_type_name,
					{$this->wpdb->prefix}topspin_items.description,
					{$this->wpdb->prefix}topspin_items.price,
					{$this->wpdb->prefix}topspin_items.name,
					{$this->wpdb->prefix}topspin_items.campaign,
					{$this->wpdb->prefix}topspin_items.offer_url,
					{$this->wpdb->prefix}topspin_items.mobile_url,
					{$this->wpdb->prefix}topspin_items_tags.tag_name,
					{$this->wpdb->prefix}topspin_currency.currency,
					{$this->wpdb->prefix}topspin_currency.symbol
				FROM
					{$this->wpdb->prefix}topspin_items
				LEFT JOIN
					{$this->wpdb->prefix}topspin_items_tags ON {$this->wpdb->prefix}topspin_items.id = {$this->wpdb->prefix}topspin_items_tags.item_id
				LEFT JOIN
					{$this->wpdb->prefix}topspin_currency ON {$this->wpdb->prefix}topspin_items.currency = {$this->wpdb->prefix}topspin_currency.currency
				LEFT JOIN
					{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_items.offer_type = {$this->wpdb->prefix}topspin_offer_types.type
				WHERE
					{$this->wpdb->prefix}topspin_items.artist_id = {$artist_id}
					{$WHERE_IN_TAGS}
					{$WHERE_IN_OFFER_TYPE}
				GROUP BY {$this->wpdb->prefix}topspin_items.id
EOD;
				$result = $this->wpdb->get_results($sql,ARRAY_A);
				if($storeData['items_order']) {
					## Global Items ID array
					$itemsIDs = array();
					foreach($result as $row) { array_push($itemsIDs,$row['id']); }
					## Adds the ordered items first
					$items_order = explode(',',$storeData['items_order']);
					foreach($items_order as $item) {
						$item = explode(':',$item);
						## Skip if don't show hidden, and item is not public
						if(!$show_hidden && !$item[1]) { continue; }
						## Only add items not in the added items list and in the global list
						if(!in_array($item[0],$addedIDs) && in_array($item[0],$itemsIDs)) {
							$the_item = $this->getItem($item[0]);
							$the_item['is_public'] = $item[1];
							array_push($sortedItems,$the_item); //Add to sortedItems array
							array_push($addedIDs,$the_item['id']); //Add to added items
						}
					}
					## Add other items only if show hidden is true
					if($show_hidden) {
						## Then add the new/unsorted items to the end of the items list
						foreach($result as $item) {
							## Only add items not in the added items list
							if(!in_array($item['id'],$addedIDs)) {
								$the_item = $item;
								$the_item['is_public'] = 0;
								array_push($sortedItems,$item); //Add to sortedItems array
								array_push($addedIDs,$item['id']); //Add to added items
							}
						}
					}
				}
				else { $sortedItems = $result; }
		}
		foreach($sortedItems as $key=>$item) {
			##	Add Images
			$sortedItems[$key]['images'] = $this->getItemImages($item['id']);
			##	Get Default Image
			$sortedItems[$key]['default_image'] = (strlen($item['poster_image_source'])) ? $this->getItemDefaultImage($item['id'],$item['poster_image_source']) : $item['poster_image'];
			$sortedItems[$key]['default_image_large'] = (strlen($item['poster_image_source'])) ? $this->getItemDefaultImage($item['id'],$item['poster_image_source'],'large') : $item['poster_image'];
		}
		return $sortedItems;
	}
	
	public function getStoreItemsPage($items,$per_page,$page=1) {
		##	Takes the items list and returns only the specific page
		##
		##	PARAMETERS
		##		@items				The items list
		##		@per_page			A number specifying how many items per page to display
		##		@page				The page number to display
		$offset = ($page>1) ? (($page-1)*$per_page) : 0;
		return array_slice($items,$offset,$per_page);
	}
	
	public function getStoreFeaturedItem($store_id) {
		##	Retrieves the featured item of the specified store
		##
		##	PARAMETERS
		##		@store_id			The store ID
		##
		##	RETURN
		##		The item's array
		$sql = 'SELECT featured_item FROM '.$this->wpdb->prefix.'topspin_stores WHERE store_id = %d';
		$featured_item = $this->wpdb->get_var($this->wpdb->prepare($sql,$store_id));
		if($featured_item) {
			return $this->getItem($featured_item);
		}
	}

	public function getFilteredItems($offer_types,$tags,$artist_id=null) {
		##	Retrieves the list of items with the set filters
		##
		##	PARAMETERS
		##		@offer_types		An array containing the list of offer types
		##		@tags				An array containing the list of tags
		##		@artist_id			(Optional) The artist's ID
		if(!$artist_id) { $artist_id = $this->artist_id; }
		$addedIDs = array();
		$addedItems = array();
		## In Offer Types
		$in_offer_type = '';
		$total_offer_types = 0;
		foreach($offer_types as $key=>$offer_type) {
			if(strlen($offer_type)) {
				$total_offer_types++;
				if($key==0) { $in_offer_type .= '\''.$offer_type.'\''; }
				else { $in_offer_type .= ', \''.$offer_type.'\''; }
			}
		}
		$WHERE_IN_OFFER_TYPE  = ($total_offer_types) ? ' AND '.$this->wpdb->prefix.'topspin_items.offer_type IN ('.$in_offer_type.')' : '';
		## In Tags
		$in_tags = '';
		$total_tags = 0;
		foreach($tags as $key=>$tag) {
			if(strlen($tag)) {
				$total_tags++;
				if($key==0) { $in_tags .= '\''.$tag.'\''; }
				else { $in_tags .= ', \''.$tag.'\''; }
			}
		}
		$WHERE_IN_TAGS  = ($total_tags) ? ' AND '.$this->wpdb->prefix.'topspin_items_tags.tag_name IN ('.$in_tags.')' : '';
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_items.id,
			{$this->wpdb->prefix}topspin_items.artist_id,
			{$this->wpdb->prefix}topspin_items.reporting_name,
			{$this->wpdb->prefix}topspin_items.embed_code,
			{$this->wpdb->prefix}topspin_items.width,
			{$this->wpdb->prefix}topspin_items.height,
			{$this->wpdb->prefix}topspin_items.url,
			{$this->wpdb->prefix}topspin_items.poster_image,
			{$this->wpdb->prefix}topspin_items.poster_image_source,
			{$this->wpdb->prefix}topspin_items.product_type,
			{$this->wpdb->prefix}topspin_items.offer_type,
			{$this->wpdb->prefix}topspin_offer_types.name AS offer_type_name,
			{$this->wpdb->prefix}topspin_items.description,
			{$this->wpdb->prefix}topspin_items.price,
			{$this->wpdb->prefix}topspin_items.name,
			{$this->wpdb->prefix}topspin_items.campaign,
			{$this->wpdb->prefix}topspin_items.offer_url,
			{$this->wpdb->prefix}topspin_items.mobile_url,
			{$this->wpdb->prefix}topspin_items_tags.tag_name,
			{$this->wpdb->prefix}topspin_currency.currency,
			{$this->wpdb->prefix}topspin_currency.symbol
		FROM {$this->wpdb->prefix}topspin_items
		LEFT JOIN
			{$this->wpdb->prefix}topspin_items_tags ON {$this->wpdb->prefix}topspin_items.id = {$this->wpdb->prefix}topspin_items_tags.item_id
		LEFT JOIN
			{$this->wpdb->prefix}topspin_currency ON {$this->wpdb->prefix}topspin_items.currency = {$this->wpdb->prefix}topspin_currency.currency
		LEFT JOIN
			{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_items.offer_type = {$this->wpdb->prefix}topspin_offer_types.type
		WHERE
			{$this->wpdb->prefix}topspin_items.artist_id = {$artist_id}
			{$WHERE_IN_TAGS}
			{$WHERE_IN_OFFER_TYPE}
EOD;
		$data = $this->wpdb->get_results($sql,ARRAY_A);
		foreach($data as $key=>$row) {
			$row['campaign'] = unserialize($row['campaign']);
			##	Add Images
			$row['images'] = $this->getItemImages($row['id']);
			##	Get Default Image
			$row['default_image'] = (strlen($row['poster_image_source'])) ? $this->getItemDefaultImage($row['id'],$row['poster_image_source']) : $row['poster_image'];
			$row['default_image_large'] = (strlen($row['poster_image_source'])) ? $this->getItemDefaultImage($row['id'],$row['poster_image_source'],'large') : $row['poster_image'];
			if(!in_array($row['id'],$addedIDs)) {
				array_push($addedIDs,$row['id']);
				array_push($addedItems,$row);
			}
		}
		return $addedItems;
	}

	public function getItem($item_id) {
		##	Retrieves the specified item
		##
		##	PARAMETERS
		##		@item_id			The item ID
		##
		##	RETURN
		##		The item's array
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_items.id,
			{$this->wpdb->prefix}topspin_items.artist_id,
			{$this->wpdb->prefix}topspin_items.reporting_name,
			{$this->wpdb->prefix}topspin_items.embed_code,
			{$this->wpdb->prefix}topspin_items.width,
			{$this->wpdb->prefix}topspin_items.height,
			{$this->wpdb->prefix}topspin_items.url,
			{$this->wpdb->prefix}topspin_items.poster_image,
			{$this->wpdb->prefix}topspin_items.poster_image_source,
			{$this->wpdb->prefix}topspin_items.product_type,
			{$this->wpdb->prefix}topspin_items.offer_type,
			{$this->wpdb->prefix}topspin_offer_types.name AS offer_type_name,
			{$this->wpdb->prefix}topspin_items.description,
			{$this->wpdb->prefix}topspin_items.price,
			{$this->wpdb->prefix}topspin_items.name,
			{$this->wpdb->prefix}topspin_items.campaign,
			{$this->wpdb->prefix}topspin_items.offer_url,
			{$this->wpdb->prefix}topspin_items.mobile_url,
			{$this->wpdb->prefix}topspin_items_tags.tag_name,
			{$this->wpdb->prefix}topspin_currency.currency,
			{$this->wpdb->prefix}topspin_currency.symbol
		FROM {$this->wpdb->prefix}topspin_items
		LEFT JOIN
			{$this->wpdb->prefix}topspin_items_tags ON {$this->wpdb->prefix}topspin_items.id = {$this->wpdb->prefix}topspin_items_tags.item_id
		LEFT JOIN
			{$this->wpdb->prefix}topspin_currency ON {$this->wpdb->prefix}topspin_items.currency = {$this->wpdb->prefix}topspin_currency.currency
		LEFT JOIN
			{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_items.offer_type = {$this->wpdb->prefix}topspin_offer_types.type
		WHERE
			{$this->wpdb->prefix}topspin_items.id = '{$item_id}'
EOD;
		$item = $this->wpdb->get_row($sql,ARRAY_A);
		##	Add Images
		$item['images'] = $this->getItemImages($item['id']);
		##	Get Default Image
		$item['default_image'] = (strlen($item['poster_image_source'])) ? $this->getItemDefaultImage($item['id'],$item['poster_image_source']) : $item['poster_image'];
		$item['default_image_large'] = (strlen($item['poster_image_source'])) ? $this->getItemDefaultImage($item['id'],$item['poster_image_source'],'large') : $item['poster_image'];
		return $item;
	}
	
	public function getItemImages($item_id) {
		##	Retrieves the item's images
		##
		##	RETURN
		##		An array containing all the item's images and their sizes
		##
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_items_images.source_url,
			{$this->wpdb->prefix}topspin_items_images.small_url,
			{$this->wpdb->prefix}topspin_items_images.medium_url,
			{$this->wpdb->prefix}topspin_items_images.large_url
		FROM {$this->wpdb->prefix}topspin_items_images
		WHERE
			{$this->wpdb->prefix}topspin_items_images.item_id = '%d'
EOD;
		return $this->wpdb->get_results($this->wpdb->prepare($sql,array($item_id)),ARRAY_A);
	}
	
	public function getItemDefaultImage($item_id,$poster_image_source,$image_size='medium') {
		##	Retrieves the item's default image
		##
		##	PARAMETERS
		##		@item_id
		##		@poster_image_source
		##		@image_size
		##
		##	RETURN
		##		The image url string if it exists (and the poster_image_source if it doesn't)
		##
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_items_images.{$image_size}_url
		FROM {$this->wpdb->prefix}topspin_items_images
		WHERE
			{$this->wpdb->prefix}topspin_items_images.item_id = '%d'
			AND {$this->wpdb->prefix}topspin_items_images.source_url = '%s'
EOD;
		$image = $this->wpdb->get_var($this->wpdb->prepare($sql,array($item_id,$poster_image_source)));
		return ($image) ? $image : $poster_image_source;
	}
	
	public function getSortByTypes() {
		##	Retrieves the sort by types
		##
		##	RETURN
		##		An array containing all the sort by type keys and names
		##
		return 	array(
			'offertype' => 'Offer Types',
			'tag' => 'Tags',
			'manual' => 'Manual'
		);
	}
	
	public function getOfferTypes() {
		##	Retrieves the offer types used by the Store API
		##
		##	RETURN
		##		An array containing all the offer type keys and names
		##
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_offer_types.type,
			{$this->wpdb->prefix}topspin_offer_types.name,
			{$this->wpdb->prefix}topspin_offer_types.status
		FROM {$this->wpdb->prefix}topspin_offer_types
EOD;
		$data = $this->wpdb->get_results($sql,ARRAY_A);
		## Store as Class Property
		$this->offer_types['data'] = $data; ## Raw Data
		foreach($data as $row) { $this->offer_types['key'][$row['type']] = $row['name']; } ## Key Data
		return $data;
	}
	
	public function getStoreOfferTypes($store_id) {
		##	Retrieves the list of types for a store
		##
		##	PARAMETER
		##		@store_id			The store ID
		##
		##	RETURN
		##		The ordered offer types list
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_offer_types.name,
			{$this->wpdb->prefix}topspin_stores_offer_type.type,
			{$this->wpdb->prefix}topspin_stores_offer_type.order_num,
			{$this->wpdb->prefix}topspin_stores_offer_type.status
		FROM {$this->wpdb->prefix}topspin_stores_offer_type
		LEFT JOIN
			{$this->wpdb->prefix}topspin_offer_types ON {$this->wpdb->prefix}topspin_offer_types.type = {$this->wpdb->prefix}topspin_stores_offer_type.type
		WHERE
			{$this->wpdb->prefix}topspin_stores_offer_type.store_id = '%d'
		ORDER BY
			{$this->wpdb->prefix}topspin_stores_offer_type.order_num ASC
EOD;
		$data = $this->wpdb->get_results($this->wpdb->prepare($sql,array($store_id)),ARRAY_A);
		return $data;
	}
	
	public function getTagList() {
		##	Retrieves the entire list of tags from the database
		##
		##	RETURN
		##		The tags table as a multi-dimensional array
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_tags.name
		FROM {$this->wpdb->prefix}topspin_tags
EOD;
		$data = $this->wpdb->get_results($sql,ARRAY_A);
		##	Set Default Status
		foreach($data as $key=>$row) {
			$data[$key]['status'] = 0;
		}
		return $data;
	}
	
	public function getStoreTags($store_id) {
		##	Retrieves the list of tags for a store
		##
		##	PARAMETER
		##		@store_id			The store ID
		##
		##	RETURN
		##		The ordered tag list
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_tags.name,
			{$this->wpdb->prefix}topspin_stores_tag.order_num,
			{$this->wpdb->prefix}topspin_stores_tag.status
		FROM {$this->wpdb->prefix}topspin_tags
		LEFT JOIN
			{$this->wpdb->prefix}topspin_stores_tag ON {$this->wpdb->prefix}topspin_tags.name = {$this->wpdb->prefix}topspin_stores_tag.tag
		WHERE
			{$this->wpdb->prefix}topspin_stores_tag.store_id = '%d'
		ORDER BY
			{$this->wpdb->prefix}topspin_stores_tag.order_num ASC
EOD;
		return $this->wpdb->get_results($this->wpdb->prepare($sql,array($store_id)),ARRAY_A);
	}
	
	public function getArtistsList() {
		##	Retrieves the list of artists from the database
		##
		##	RETURN
		##		The artists list
		$sql = <<<EOD
		SELECT
			{$this->wpdb->prefix}topspin_artists.id,
			{$this->wpdb->prefix}topspin_artists.name,
			{$this->wpdb->prefix}topspin_artists.avatar_image,
			{$this->wpdb->prefix}topspin_artists.url,
			{$this->wpdb->prefix}topspin_artists.description,
			{$this->wpdb->prefix}topspin_artists.website
		FROM {$this->wpdb->prefix}topspin_artists
		ORDER BY
			{$this->wpdb->prefix}topspin_artists.id ASC
EOD;
		return $this->wpdb->get_results($this->wpdb->prepare($sql),ARRAY_A);
	}
















	### OLD FUNCTION
	public function displayTabs($info=array('id'=>0),$onoff) {
		$url = str_replace('%7E','~',$_SERVER['REQUEST_URI']);
		if(strpos($url,"id="))
			$url = preg_replace("/id=[0-9]+/","id=" . $info["id"], $url);
		else
			$url .= '&id=' . $info['id'];
		echo '<div style="float:left;overflow:hidden;font-weight:bold;';
		if($onoff)
			echo 'background:#FFF;margin-bottom:-1px;height:22px;';
		else
			echo 'background:#EEE;height:21px;';
		echo 'border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000;margin-right:5px;padding:3px 5px;-webkit-border-top-left-radius: 3px;-webkit-border-top-right-radius: 3px;-moz-border-radius-topleft: 3px;-moz-border-radius-topright: 3px;border-top-left-radius: 3px;border-top-right-radius: 3px;">';
		if($info['id']==0) {
			if($onoff)
				echo 'Create New Store';
			else
				echo '<a href="'.$url.'" style="text-decoration:none; color:#999;">Create New Store</a>';
		} else {
			if ($onoff)
				echo $info["store_name"];
			else
				echo '<a href="'.$url.'" style="text-decoration:none; color:#999;">'.$info['store_name'].'</a>';
		}
		echo '</div>';
	}

}

?>
