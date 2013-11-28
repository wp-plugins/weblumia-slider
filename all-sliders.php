<?php include( 'lumia-slider-functions.php' );?>
<?php $sliderid					=	( $_REQUEST['id'] ) ? $_REQUEST['id'] : '';?>

<?php /* code for save slider name */?>
<?php if( $_REQUEST['page'] == 'lumia_sliders' && $_REQUEST['slider_saved'] == 'true' && $_REQUEST['action'] != 'add' ) {
	save_slider_name( $_POST['sid'] );
}

/* code for getting slider data */
if( $_REQUEST['action'] == 'edit' && isset( $_REQUEST['id'] ) ){
	$slider				=	slider_list_by_id( $_REQUEST['id'] );
}

/* code for remove slider name */
if( $_REQUEST['action'] == 'remove' && isset( $_REQUEST['id'] ) ){
	remove_slider( $_REQUEST['id'] );
}

/* code for list slider images */
if( strstr( $_SERVER['REQUEST_URI'], 'lumia_sliders' ) && $_REQUEST['action'] == 'all_images' && isset( $_REQUEST['slider_id'] ) ){
	lumia_list_slider();
}

/* code for add slider images */
if( strstr( $_SERVER['REQUEST_URI'], 'lumia_sliders' ) && $_REQUEST['action'] == 'add_image' && isset( $_REQUEST['slider_id'] ) ){
	lumia_add_slider();
}

/* code for edit slider images */
if( strstr( $_SERVER['REQUEST_URI'], 'lumia_sliders' ) && $_REQUEST['action'] == 'edit_image' && isset( $_REQUEST['slider_id'] ) ){
	lumia_add_slider();
}

/* code for save slider images */
if( $_REQUEST['page'] == 'lumia_sliders' && $_REQUEST['slider_saved'] == 'true' && $_REQUEST['action'] == 'add' ) {
	save_slider( $_POST['slider_id'], $_POST['image_id'] );
}

/* code for delete slider images */
if( $_REQUEST['action'] == 'remove_image' && isset( $_REQUEST['image_id'] ) ){
	remove_image( $_REQUEST['image_id'], $_REQUEST['slider_id'] );
}	
?>
<?php if( ( $_REQUEST['action'] != 'all_images' && !isset( $_REQUEST['slider_id'] ) ) || ( $_REQUEST['action'] != 'edit_image' && !isset( $_REQUEST['slider_id'] ) ) || $_REQUEST['action'] == 'remove' || $_REQUEST['action'] == 'edit' ){?>
<div class="wrap">
    <h2><?php _e( 'Lumia Slider Lists', 'lumiaslider' ) ?></h2>
    <?php if( $_REQUEST['slider_saved'] == 'true' ){?>
        <div class="updated below-h2" id="message"><p>One slider data saved</p></div>
    <?php }?>
    <?php if( $_REQUEST['required'] == 'false' ){?>
        <div class="updated below-h2" id="message"><p>Some fields are empty..., no slider data saved</p></div>
    <?php }?>
    <form method="post" action="admin.php?page=lumia_sliders&slider_saved=true">
        <div id="titlediv">
            <input type="text" placeholder="Type your slider name here" autocomplete="off" id="title" value="<?php echo $slider['name'];?>" name="title" />
            <input type="hidden" name="sid" value="<?php echo $sliderid;?>" />
        </div>
        <div class="wl-box wl-publish">
            <h3 class="header"><?php _e('Publish', 'lumiasliders') ?></h3>
            <div class="inner">
                <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'lumiaslider' ); ?>" />
                <p class="wl-saving-warning"></p>
                <div class="clear"></div>
            </div>
        </div>
    </form>
    <div class="wl-box wl-slider-list">
        <table>
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Name</td>
                    <td>Shortcode</td>
                    <td>Actions</td>
                    <td>Created</td>
                    <td>Modified</td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <?php echo populate_slider_lists();?>
            </tbody>
        </table>
    </div>
</div>
<?php }?>