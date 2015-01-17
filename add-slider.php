<div class="wrap">
	<?php if( $_REQUEST['action'] == 'edit_image' && isset( $_REQUEST['image_id'] ) ){
		$slider_image		=	slider_image_by_id( $_REQUEST['image_id'], $_REQUEST['slider_id'] );
		$sliderdata			=	json_decode( $slider_image['data'] );
	}
	?>
    <?php if( $_REQUEST['required'] == 'false' ){?>
        <div class="updated below-h2" id="message"><p>Some fields are empty..., no slider data saved</p></div>
    <?php }?>
    <h2><?php _e( 'Save new slider', 'lumiaslider' ) ?></h2>
    <form method="post" action="admin.php?page=lumia_sliders&action=add&slider_saved=true">
    	<input type="hidden" name="slider_id" value="<?php echo $_REQUEST['slider_id'];?>" />
    	<input type="hidden" name="image_id" value="<?php echo $_REQUEST['image_id'];?>" />
        <div class="wl-box wl-slider-list">
            <table>
                <thead>
                    <tr>
                        <td colspan="4">
                            <h4><?php _e( 'Slider Attributes', 'lumiaslider' ) ?></h4>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    	<td><?php _e( 'Slider Title', 'lumiaslider' ) ?></td>
                        <td colspan="3"><input type="text" id="input_stitle" value="<?php echo $sliderdata->stitle;?>" name="stitle" style="width:100%;" /></td>
                    </tr>
                    <tr>
                    	<td><?php _e( 'Image', 'lumiaslider' ) ?></td>
                        <td>
                            <input type="text" id="input_image" value="<?php echo $sliderdata->image;?>" name="image" /><a title="Featured Image" data-editor="content" id="upload_image_button" href="#" class="imgbutton button insert-media add_media">Add</a>
                         </td>
                        <td><?php _e( 'Thumbnail', 'lumiaslider' ) ?></td>
                        <td>
                            <input type="text" id="input_image_thumb" value="<?php echo $sliderdata->image_thumb;?>" name="image_thumb" /><a title="Thumb Image" data-editor="content" id="upload_image_button" href="#" class="thumbbutton button insert-media add_media">Add</a>
                        </td>
                    </tr>
                    <tr class="imagediv" <?php if ( isset( $_REQUEST['image_id'] ) ){?>style="display:table-row;"<?php } else {?>style="display:none;"<?php }?>>
                    	<td><?php _e( '', 'lumiaslider' ) ?></td>
                        <td>
                            <img src="<?php echo $sliderdata->image;?>" title="" alt="" id="image_src" width="150" <?php if ( isset( $_REQUEST['image_id'] ) ){?>style="display:block;"<?php } else {?>style="display:none;"<?php }?>/>
                         </td>
                        <td><?php _e( '', 'lumiaslider' ) ?></td>
                        <td>
                            <img src="<?php echo $sliderdata->image_thumb;?>" title="" alt="" id="image_thumb_src" width="80" <?php if ( isset( $_REQUEST['image_id'] ) ){?>style="display:block;"<?php } else {?>style="display:none;"<?php }?> />
                        </td>
                    </tr>
                    <tr>
                    	<td><?php _e( 'Link URL', 'lumiaslider' ) ?></td>
                        <td>
                            <input type="text" id="link_url" value="<?php echo $sliderdata->link_url;?>" name="link_url" class="wl-large"/>
                        </td>
                        <td><?php _e( 'Link target', 'lumiaslider' ) ?></td>
                        <td>
                            <select name="layer_link_target">
                                <option value="_self"<?php if( $sliderdata->layer_link_target == '_self' ){?> selected="selected"<?php }?>>_self</option>
                                <option value="_blank"<?php if( $sliderdata->layer_link_target == '_blank' ){?> selected="selected"<?php }?>>_blank</option>
                                <option value="_parent"<?php if( $sliderdata->layer_link_target == '_parent' ){?> selected="selected"<?php }?>>_parent</option>
                                <option value="_top"<?php if( $sliderdata->layer_link_target == '_top' ){?> selected="selected"<?php }?>>_top</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td><?php _e( 'Custom HTML Content', 'lumiaslider' ) ?></td>
                        <td colspan="3">
                            <textarea id="custom_html" name="custom_html" rows="10"><?php echo stripslashes( $sliderdata->custom_html );?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
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
</div>