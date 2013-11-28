<div class="wrap">
    <h2><?php _e( 'All slider images for " ' . slidername_by_id( $_REQUEST['slider_id'] ) . ' "', 'lumiaslider' ) ;?>&nbsp;<a href="?page=lumia_sliders&amp;action=add_image&amp;slider_id=1" class="add-new-h2">Add Images</a></h2>
    <?php if( $_REQUEST['save'] == 'true' ){?>
        <div class="updated below-h2" id="message"><p>One slider added</p></div>
    <?php }?>
    <?php if( $_REQUEST['required'] == 'false' ){?>
        <div class="updated below-h2" id="message"><p>Some fields are empty..., no slider added</p></div>
    <?php }?>
    <div class="wl-box wl-slider-list">
        <table>
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Title</td>
                    <td>Image</td>
                    <td>Link URL</td>
                    <td>Actions</td>
                    <td>Created</td>
                    <td>Modified</td>
                </tr>
            </thead>
            <tbody>
                <?php echo populate_slider_images_by_id( $_REQUEST['slider_id'] );?>
            </tbody>
        </table>
    </div>
</div>