<form method="GET" action="/custom-rss">

<?php 

	//Categories
	echo '<h3>Choose by Category</h3>';
		
	// get all the categories	
	$listCat = get_categories(); // include any categories that should be excluded here
	
	// for each category, loop through the following, creating a checkbox input						
	foreach ($listCat as $cat) {
	  	$checkbox = '<p class="basic"><input name="category[]" type="checkbox" value="'.$cat->cat_ID.'" />';
		$checkbox .= '<label>'.$cat->cat_name.'</label></p>';
		echo $checkbox;
	}
							
	//Authors
	echo '<h3>Choose by Author</h3>';

	// get all of the blog's authors
	$listAuthor = get_users_of_blog();
	
	//User must have posted since following date to appear - year month day
	$cutOffDate = 20100101; 
	
	// loop through each author and outut a checkbox
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
