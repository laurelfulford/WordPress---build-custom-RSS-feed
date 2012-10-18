<form method="GET" action="/custom-rss">

<?php 

//Categories
	echo '<h3 style="font-size:14px;color:#999;margin-bottom:20px;">Choose by Category</h3>';
		
	$listCat = get_categories('exclude=131,1381'); 
							
	foreach ($listCat as $cat) {
	  	$checkbox = '<p class="basic"><input name="category[]" type="checkbox" value="'.$cat->cat_ID.'" />';
		$checkbox .= '<label>'.$cat->cat_name.'</label></p>';
		echo $checkbox;
	}
							
//Authors
	echo '<h3 style="font-size:14px;color:#999;margin-bottom:20px;">Choose by Author</h3>';

	$listAuthor = get_users_of_blog();
	
	//User must have posted since following date to appear - year month day
	$cutOffDate = 20100101; 
	
	foreach ($listAuthor as $author) {
		$user = get_userdata($author->user_id);
		$lastpost = query_posts('author='.$user->ID.'&posts_per_page=1');
									
		foreach ($lastpost as $post) {
			$lastPostDate = mysql2date('Ymd', $post->post_date);
		}
		
		if($lastPostDate > $cutOffDate) {
			$checkbox = '<p class="basic"><input name="byline[]" type="checkbox" value="'.$user->ID.'" />';
			$checkbox .= '<label>'.$user->user_firstname . ' ' . $user->user_lastname.'</label></p>';
			echo $checkbox;
		}
		
	}
				
 ?>
<input type="submit" value="submit" id="submit-custom-rss" />
</form>