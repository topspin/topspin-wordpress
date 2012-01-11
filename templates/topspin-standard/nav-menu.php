<?php 


global $store;
$menu = $store->getSetting('topspin_navmenu');
$storesListInOrder = $store->getStoresInOrder();

# generate list of store page ids in order
$storeIDs = array();
foreach ($storesListInOrder as $item) {
  array_push($storeIDs, $item->ID);
}
?>

<!-- Only print out menu if the menu is activated -->
<?php if ($menu == 'on') : ?>

<?php 
    $args = array(
        'include'  => $storeIDs,
        'orderby' => 'post_date',
        'post_type'=> 'page',
    );

    /* Get posts according to arguments defined above */
    $pages = get_posts($args);
    $pages = array_reverse($pages);

    echo "<ul id='topspin-navmenu'>";

    /* Loop through the array returned by get_posts() */
    foreach ($pages as $page) {

        /* Grab the page id */
        $pageId = $page->ID;

        /* Get page title */
        $title = $page->post_title;

	/* Get page slug */
	$slug = $page->post_name;

        echo "<li><a href='" . get_bloginfo('url') . "/$slug'>$title</a>";         

        /* Output child pages if any */
  	$pageKids = get_pages("child_of=".$pageId."&sort_column=menu_order");
        if ($pageKids) { 
 		echo "<ul>";
		foreach ($pageKids as $kid) { 
			echo "<li><a href='" . get_bloginfo('url') . "/$kid->post_name'>".$kid->post_title."</a></li>";
 		}
		echo "</ul>";
	}

	echo "</li>";
    } 
    echo "</ul>";
?>

<script type="text/javascript">
sfHover = function() {
	var sfEls = document.getElementById("topspin-navmenu").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);


</script>



<?php endif; ?>
