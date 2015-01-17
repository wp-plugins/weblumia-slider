<?php
/********************************************************/
/*                        Storing                       */
/********************************************************/
function save_slider_name( $id ) {
	global $wpdb;
	$table_name		=	$wpdb->prefix . "lumiaslider";
	$sliders		=	$_POST;
	$slider			=	is_array( $sliders ) ? $sliders : unserialize( $sliders );
	if( $_POST['title'] != '' ){
		if( $id ){	
			$wpdb->query(
				$wpdb->prepare( "UPDATE $table_name SET
									name = '%s', 
									date_m = '%d' WHERE id = %d",
									$_POST['title'],
									time(),
									$id 
				)
			);
		} else {
			$wpdb->query(
				$wpdb->prepare( "INSERT INTO $table_name
									( name, date_c, date_m )
								VALUES
									(
										'%s', '%d', '%d'
									)",
									$_POST['title'],
									time(),
									time()
				)
			);
		}
	}else{
		wp_redirect( 'admin.php?page=lumia_sliders&required=false' );
		exit;
	}
}
function save_slider( $slider_id, $image_id ) {
	
	global $wpdb;
	
	$table_name		=	$wpdb->prefix . "lumiaslider_images";
	
	$sliders		=	$_POST;
	$slider			=	is_array( $sliders ) ? $sliders : unserialize( $sliders );
	if( $_POST['stitle'] != '' ){
		
		if( $image_id ){
			$wpdb->query(
				$wpdb->prepare( "UPDATE $table_name SET
										data = '%s', date_m = '%d' WHERE id = %d",
										json_encode( $slider ),
										time(),
										$image_id
					)
			);
			wp_redirect( 'admin.php?page=lumia_sliders&action=all_images&slider_id=' . $slider_id . '&save=true' );
			exit;
		} else {
			$wpdb->query(
						 
				$wpdb->prepare( "INSERT INTO $table_name
										( sid, data, date_c, date_m )
									VALUES
										(
											'%s', '%s', '%d', '%d'
										)",
										$slider_id,
										json_encode( $slider ),
										time(),
										time()
					)
			);
			wp_redirect( 'admin.php?page=lumia_sliders&action=all_images&slider_id=' . $slider_id . '&save=true' );
			exit;
		}
	}else{ 
		if( isset($_POST['image_id'] ) ){
			wp_redirect( 'admin.php?page=lumia_sliders&action=edit_image&image_id=' . $_POST['image_id'] . '&slider_id=' . $_POST['slider_id'] . '&required=false' );
		} else {
			wp_redirect( 'admin.php?page=lumia_sliders&action=add_image&slider_id=' . $_POST['image_id'] . '&required=false' );
		}
	}
}
function populate_slider_lists(){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider";
	$i					=	1;
	$slider				=	$wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE 1 ORDER BY date_c DESC" , ARRAY_A );
	
	if( $slider ){
		
		foreach( $slider as $sliderObj ){
			$html			.=	'<tr>
									<td>' . $i . '</td>
									<td><a href="?page=lumia_sliders&amp;action=edit&amp;id=' . $sliderObj['id'] . '">' . $sliderObj['name'] . '</a></td>
									<td>[lumiaslider id="' . $sliderObj['id'] . '"]</td>
									<td>
										<a href="?page=lumia_sliders&amp;action=edit&amp;id=' . $sliderObj['id'] . '">Edit</a> |
										<a class="remove" href="?page=lumia_sliders&amp;action=remove&amp;id=' . $sliderObj['id'] . '" onclick="return confirm( \'Are you sure want to delete ?\' );">Remove</a>
									</td>
									<td>' . date( 'F d, Y', $sliderObj['date_c'] ) . '</td>
									<td>' . date( 'F d, Y', $sliderObj['date_m'] ) . '</td>
									<td valign="middle"><a class="add-new-h2" href="?page=lumia_sliders&amp;action=all_images&amp;slider_id=' . $sliderObj['id'] . '">Add Images</a></td>
								</tr>';
			$i++;
		}
	}else{
		$html			=	'<tr><td colspan="7">No data to display</td></tr>';
	}
	return $html;
}
function slider_list_by_id( $id ){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider";
	$slider				=	$wpdb->get_row( "SELECT * FROM " . $table_name . " WHERE id = " . ( int ) $id . "" , ARRAY_A );
	
	return $slider;
}
function remove_slider( $id ){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider";
	$slider				=	$wpdb->get_row( "SELECT * FROM " . $table_name . " WHERE id=" . ( int ) $id . "" , ARRAY_A );
	
	if( $slider ){
		
		$slider			=	$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $table_name . ' WHERE id = ' . ( int ) $id . '' , ARRAY_A ) );
	}
	
	return $slider;
}
function slidername_by_id( $id ){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider";
	$slider				=	$wpdb->get_row( "SELECT * FROM " . $table_name . " WHERE id = " . ( int ) $id . "" , ARRAY_A );
	
	return $slider['name'];
}
function populate_slider_images_by_id( $id ){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider_images";
	$i					=	1;
	$slider				=	$wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE sid = " . ( int ) $id . " ORDER BY date_c DESC" , ARRAY_A );
	
	if( $slider ){
		foreach( $slider as $sliderObj ){
			$sliderdata		=	json_decode( $sliderObj['data'] );
			$html			.=	'<tr>
									<td>' . $i . '</td>
									<td>' . $sliderdata->stitle . '</td>
									<td><img src="' . $sliderdata->image . '" width="80" alt="" /></td>
									<td>' . $sliderdata->link_url . '</td>
									<td>
										<a href="?page=lumia_sliders&amp;action=edit_image&amp;image_id=' . $sliderObj['id'] . '&slider_id=' . $sliderObj['sid'] . '">Edit</a> |
										<a class="remove" href="?page=lumia_sliders&amp;action=remove_image&amp;image_id=' . $sliderObj['id'] . '&slider_id=' . $sliderObj['sid'] . '" onclick="return confirm( \'Are you sure want to delete ?\' );">Remove</a>
									</td>
									<td>' . date( 'F d, Y', $sliderObj['date_c'] ) . '</td>
									<td>' . date( 'F d, Y', $sliderObj['date_m'] ) . '</td>
								</tr>';
			$i++;
		}
	}else{
		$html			=	'<tr><td colspan="7">No data to display</td></tr>';
	}
	
	return $html;
}
function slider_image_by_id( $id, $sid ){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider_images";
	$slider				=	$wpdb->get_row( "SELECT * FROM " . $table_name . " WHERE id = " . ( int ) $id . " AND sid = " . ( int ) $sid , ARRAY_A );
	
	return $slider;
}
function remove_image( $image_id, $slider_id ){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider_images";
	$slider				=	$wpdb->get_row( "SELECT * FROM " . $table_name . " WHERE id = " . ( int ) $image_id , ARRAY_A );
	if( $slider ){
		
		$slider			=	$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $table_name . ' WHERE id = ' . ( int ) $image_id . '' , ARRAY_A ) );
	}
	wp_redirect( 'admin.php?page=lumia_sliders&action=all_images&slider_id=' . $slider_id . '&removed=true' );
	exit;
}
function populate_slider_images( $slider_id ){
	
	global $wpdb;
	$table_name			=	$wpdb->prefix . "lumiaslider_images";
	$slider				=	$wpdb->get_results( "SELECT * FROM $table_name WHERE sid = " . ( int ) $slider_id . " ORDER BY date_c DESC" , ARRAY_A );
	
	return $slider;
}
?>