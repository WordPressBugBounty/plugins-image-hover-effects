<?php 	
	class LA_Caption_Hover {
	    public function __construct() {
	        add_action( 'admin_enqueue_scripts', array( $this, 'admin_options_page_scripts' ) );
	        add_action( 'admin_menu', array( $this, 'caption_hover_admin_options' ) );
	        add_action( 'wp_ajax_la_save_caption_options', array( $this, 'save_caption_options' ) );
	        add_shortcode( 'image-caption-hover', array($this,'render_caption_hovers') );
	    }

	    public function admin_options_page_scripts($hook) {
	        if( $hook == 'toplevel_page_caption_hover' || $hook == 'image-hover-effects_page_caption_hover_pro_settings' ) {
	            wp_enqueue_media();
	            wp_enqueue_style( 'wp-color-picker' );
	            wp_enqueue_style('wdo-style-css', plugin_dir_url(__FILE__) . 'admin/style.css', array(), '1.0.0');
	            wp_enqueue_style( 'wdo-ui-css', plugin_dir_url( __FILE__ ) . 'admin/jquery-ui.min.css', array(), '1.0.0' );
	        }
	        wp_enqueue_script( 'wdo-admin-js', plugin_dir_url( __FILE__ ) . 'admin/admin.js', array( 'jquery', 'jquery-ui-accordion', 'wp-color-picker' ), '1.0', true );
	        wp_localize_script( 'wdo-admin-js', 'laAjax', array( 
	            'url' => admin_url( 'admin-ajax.php' ),
	            'nonce' => wp_create_nonce( 'laajax-nonce' )
	        ));
	    }

	    public function caption_hover_admin_options() {
	        add_menu_page( 'Image Hover Effects', 'Image Hover Effects', 'manage_options', 'caption_hover', array( $this, 'render_menu_page' ), 'dashicons-format-image' );
	    }


	    public function pro_version_settings() {
	        include 'includes/pro-version-settings.php';
	    }

	    public function save_caption_options() {
	        if ( ! current_user_can( 'manage_options' ) || ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'laajax-nonce' ) ) {
	            exit;
	        }
	        if ( isset( $_REQUEST ) && current_user_can( 'manage_options' ) ) {
	            update_option( 'la_caption_hover', $_REQUEST );
	        }
	    }

	    public function render_menu_page(){ ?>
	    	<?php $saved_captions = get_option( 'la_caption_hover' ); ?>
	    	<div class="wrapper" id="caption">
	    		    <div class="se-saved-con"></div>
	    		    <div class="overlay-message">
	    		       <p><?php esc_html_e( 'Changes Saved..!', 'image-hover-effects' ); ?></p>
	    		    </div>
	    		    <h2 style="text-align: center;font-size: 30px;"><?php esc_html_e( 'Image Hover Effects', 'image-hover-effects' ); ?></h2>
	    		    <p style="text-align: center;font-size: 18px;margin-bottom: 30px;"><?php esc_html_e( 'An easy and best way to display images with text and animations.', 'image-hover-effects' ); ?></p>
	    				    <div id="faqs-container" class="accordian">
	    				        <?php if ( isset( $saved_captions['posts'] ) ) : ?>
	    				            <?php foreach ( $saved_captions['posts'] as $key => $data ) : ?>
	    				                <h3>
	    				                    <a href="#">
	    				                       <?php echo esc_html( $data['cat_name'] !== '' ? $data['cat_name'] : esc_html__( 'Image Caption Hover', 'image-hover-effects' ) ); ?>
	    				                    </a>
	    				                    <button class="button topshortcode" id="<?php echo esc_attr( $data['shortcode'] ); ?>">
	                                           <b title="<?php esc_attr_e( 'Get Shortcode', 'image-hover-effects' ); ?>" class="dashicons dashicons-shortcode"></b>
	                                           <?php esc_html_e( 'Get Shortcode', 'image-hover-effects' ); ?>
	                                       	</button>
	    				                    <button class="button addcattop">
	                                            <b title="<?php esc_attr_e( 'Add New Category', 'image-hover-effects' ); ?>" class="dashicons dashicons-insert"></b>
	                                        </button>
	    				                   	<button class="button removecattop">
	    				                        <span class="dashicons dashicons-dismiss" title="<?php esc_attr_e( 'Remove Category', 'image-hover-effects' ); ?>"></span>
	    				                    </button>
	    				                </h3>
	    				                <div class="accordian content">
	    				                    <?php foreach ($data['allcapImages'] as $key => $data2) : ?>
	    				                            <h3>
	    				                                <a href="#">
	    				                                    <?php 
	    				                                    if ($data2['img_name'] !== '') {
	    				                                        echo esc_html($data2['img_name']);
	    				                                    } else {
	    				                                        echo "image";
	    				                                    }
	    				                                    ?>
	    				                                </a>
	    				                                <button class="button addimgtop"><b title="Add New Image" class="dashicons dashicons-insert"></b></button>
	    				                                <button class="button removeimgtop"><span class="dashicons dashicons-dismiss" title="Remove Image"></span></button>
	    				                            </h3>
	    				                            <div>
	    				                                <table class="form-table">
	    				                                    <tr>
	    				                                        <td style="width:30%">
	    				                                            <strong><?php esc_attr_e('Category Name', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td style="width:30%">
	    				                                            <input type="text" class="catname widefat form-control" value="<?php echo isset($data2['cat_name']) ? esc_attr($data2['cat_name']) : ''; ?>">
	    				                                        </td>
	    				                                        <td style="width:40%">
	    				                                            <p class="description"><?php esc_attr_e('Name the category which would be shown on tab. It is only for reference. Category name should be same for every image.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Image Name', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <input type="text" class="imgname widefat form-control" value="<?php echo isset($data2['img_name']) ? esc_attr($data2['img_name']) : ''; ?>">
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Name to be shown on current inner tab. It will be for your reference only.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Upload Image', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <button class="addimage button"><?php esc_attr_e('Upload Image', 'image-hover-effects'); ?></button>
	    				                                            <span class="image">
	    				                                                <?php if (isset($data2['cap_img']) && $data2['cap_img'] != '') : ?>
	    				                                                    <span>
	    				                                                        <img src="<?php echo esc_url($data2['cap_img']); ?>">
	    				                                                        <span class="dashicons dashicons-dismiss"></span>
	    				                                                    </span>
	    				                                                <?php endif; ?>
	    				                                            </span><br>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Upload Image on which hover effect with caption to be added.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                </table>
	    				                                <table class="form-table">
	    				                                    <tr>
	    				                                        <td style="width:30%">
	    				                                            <strong><?php esc_attr_e('Caption Heading', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td style="width:30%">
	    				                                            <input type="text" class="widefat capheading form-control" value="<?php echo esc_attr($data2['cap_head']); ?>">
	    				                                        </td>
	    				                                        <td style="width:40%">
	    				                                            <p class="description"><?php esc_attr_e('Give heading to be shown on image.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <?php if (current_user_can('unfiltered_html')) : ?>
	    				                                        <tr>
	    				                                            <td>
	    				                                                <strong><?php esc_attr_e('Caption Description', 'image-hover-effects'); ?></strong>
	    				                                            </td>
	    				                                            <td>
	    				                                                <textarea class="widefat capdesc form-control" rows="10"><?php echo esc_textarea($data2['cap_desc']); ?></textarea>
	    				                                            </td>
	    				                                            <td>
	    				                                                <p class="description"><?php esc_attr_e('Give description to be shown on image under heading.', 'image-hover-effects'); ?></p>
	    				                                            </td>
	    				                                        </tr>
	    				                                    <?php endif; ?>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Caption Link', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <input type="url" class="widefat caplink form-control" value="<?php echo esc_url($data2['cap_link']); ?>">
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Give complete URL of link which opens when the user clicks over the image.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Open Link in New Tab', 'image-hover-effects'); ?> <b><a class="pro-feature" href="<?php echo esc_url($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <select class="capnewtab form-control">
	    				                                                <option value="no" <?php selected($data2['cap_new_tab'], 'no'); ?>><?php esc_attr_e('No', 'image-hover-effects'); ?></option>
	    				                                                <option value="yes" <?php selected($data2['cap_new_tab'], 'yes'); ?>><?php esc_attr_e('Yes', 'image-hover-effects'); ?></option>
	    				                                            </select>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Choose whether to open the link in a new tab or not.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Heading Color', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td class="insert-picker">
	    				                                            <input type="text" class="head-color" value="<?php echo esc_attr($data2['cap_headcolor']); ?>">
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Choose font color for caption heading.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Description Color', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td class="insert-picker">
	    				                                            <input type="text" class="desc-color" value="<?php echo esc_attr($data2['cap_desccolor']); ?>">
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Choose font color for caption description.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                </table>
	    				                                <table class="form-table">
	    				                                    <tr>
	    				                                        <td style="width:30%">
	    				                                            <strong><?php esc_attr_e('Image Width', 'image-hover-effects'); ?> <b><a class="pro-feature" href="<?php echo esc_url($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				                                        </td>
	    				                                        <td style="width:30%">
	    				                                            <input type="number" class="form-control thumbwidth">
	    				                                        </td>
	    				                                        <td style="width:40%">
	    				                                            <p class="description"><?php esc_attr_e('Give width (keep width and height same for circle style) for the image.<b>Default(220px)</b>', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Image Height', 'image-hover-effects'); ?> <b><a class="pro-feature" href="<?php echo esc_url($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>        
	    				                                        </td>
	    				                                        <td>
	    				                                            <input type="number" class="form-control thumbheight">
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Give height (keep width and height same for circle style) for the image. <b>Default(220px)</b>', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Image Shape', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <select class="styleopt form-control widefat">
	    				                                                <option value="circle" <?php selected($data2['cap_style'], 'circle'); ?>><?php esc_attr_e('Circle', 'image-hover-effects'); ?></option>
	    				                                                <option value="square" <?php selected($data2['cap_style'], 'square'); ?>><?php esc_attr_e('Square', 'image-hover-effects'); ?></option> 
	    				                                            </select>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Select shape of image. It could be square or circle.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Select Hover Effect', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                           <select class="effectopt form-control widefat">
	    				                                               <?php
	    				                                               for ($i = 1; $i <= 20; $i++) :
	    				                                               // translators: Placeholder %d represents the effect number.
	    				                                               ?>
	    				                                               <option <?php selected($data2['cap_effect'], 'effect' . esc_attr($i)); ?> value="effect<?php echo esc_attr($i); ?>">
	    				                                               	<?php // translators: Placeholder %d represents the effect number. ?>
	    				                                               	<?php echo sprintf(esc_html(__('Effect%d', 'image-hover-effects')), esc_html($i)); ?>
	    				                                               		
	    				                                               	</option>
	    				                                               <?php endfor; ?>
	    				                                           </select>

	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Select hover animation.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Hover Animation Direction', 'image-hover-effects'); ?></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <select class="directionopt form-control widefat">
	    				                                                <?php
	    				                                                $directions = array(
	    				                                                    'left_to_right' => __('Left To Right', 'image-hover-effects'),
	    				                                                    'right_to_left' => __('Right To Left', 'image-hover-effects'),
	    				                                                    'top_to_bottom' => __('Top To Bottom', 'image-hover-effects'),
	    				                                                    'bottom_to_top' => __('Bottom To Top', 'image-hover-effects')
	    				                                                );
	    				                                                foreach ($directions as $key => $direction) : ?>
	    				                                                	<option <?php selected($data2['cap_direction'], esc_attr($key)); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($direction); ?></option>
	    				                                                <?php endforeach; ?>
	    				                                            </select>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Select direction in which animation occur.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Images Per Row', 'image-hover-effects'); ?></strong> <br>
	    				                                        </td>
	    				                                        <td>
	    				                                            <select class="capgrid form-control widefat">
	    				                                                <?php for ($i = 1; $i <= 4; $i++) : ?>
	    				                                                	<option value="<?php echo esc_attr(12 / $i); ?>" <?php selected($data2['cap_grid'], esc_attr(12 / $i)); ?>><?php echo esc_html($i); ?></option>
	    				                                                <?php endfor; ?>
	    				                                            </select>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Select how many images show in one row.Keep it same for every image.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr>
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Select Caption Background Type', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_url($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <select class="capbgtype form-control widefat">
	    				                                                <option value="image"><?php esc_attr_e('Image', 'image-hover-effects'); ?></option>
	    				                                                <option value="color"><?php esc_attr_e('Color', 'image-hover-effects'); ?></option>
	    				                                            </select>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Choose background type. It can be image or color. <b>Image over image</b> effect can be achieved with this option.', 'image-hover-effects'); ?></p>
	    				                                            <a style="color:#428bca;font-weight: bold;" href="https://demo.webdevocean.com/#image-over-image-section" target="_blank"><?php esc_attr_e('Image over image effect example', 'image-hover-effects'); ?></a>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr class="bgcolorrow"> 
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Caption Background Color', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_url($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <input type="text" class="form-control custom capbgcolor" value="#fff" />
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Choose background color for the caption. (Default #1a4a72)', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr class="bgimagerow"> 
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Choose Background Image', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_url($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <button class="bgimage button"><?php esc_attr_e('Upload Image', 'image-hover-effects'); ?></button>
	    				                                            <span class="backgroundimage"></span>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Choose background color for the caption. (Default #1a4a72)', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>
	    				                                    <tr class="bgimagerow"> 
	    				                                        <td>
	    				                                            <strong><?php esc_attr_e('Background Size', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_url($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				                                        </td>
	    				                                        <td>
	    				                                            <select class="capbgsize form-control widefat">
	    				                                                <option value="cover"><?php esc_attr_e('Cover', 'image-hover-effects'); ?></option>
	    				                                                <option value="contain"><?php esc_attr_e('Contain', 'image-hover-effects'); ?></option>
	    				                                                <option value="100% 100%"><?php esc_attr_e('Full', 'image-hover-effects'); ?></option>
	    				                                            </select>
	    				                                        </td>
	    				                                        <td>
	    				                                            <p class="description"><?php esc_attr_e('Choose background size.', 'image-hover-effects'); ?></p>
	    				                                        </td>
	    				                                    </tr>


	    				                                </table>

	    				                                <span class="moreimages">
	    				                                    <button class="button moreimg"><b title="Add New" class="dashicons dashicons-plus-alt"></b> <?php esc_attr_e('Add New Image', 'image-hover-effects'); ?></button>
	    				                                    <button class="button-primary addcat"><?php esc_attr_e('Add New Category', 'image-hover-effects'); ?></button>
	    				                                    <button class="button-primary fullshortcode pull-right" id="<?php echo esc_attr($data2['shortcode']); ?>"><?php esc_attr_e('Get Shortcode', 'image-hover-effects'); ?></button>
	    				                                </span>
	    				                                <div class="preview-container" style="display: none;">
	    				                                    <?php echo do_shortcode("[image-caption-hover id='".$data2['shortcode']."']"); ?>
	    				                                </div>
	    				                            </div>
	    				                    <?php endforeach; ?>
	    				                </div>
	    				            <?php endforeach; ?>
	    				            <?php else: ?>
	    				            	<h3>
	    				            	    <a href="#"><?php esc_attr_e('Image Caption Hover', 'image-hover-effects'); ?></a>
	    				            	    <button class="button topshortcode" id="<?php echo esc_attr($data['shortcode']); ?>"><b title="<?php esc_attr_e('Get Shortcode', 'image-hover-effects'); ?>" class="dashicons dashicons-shortcode"></b>  &nbsp; <?php esc_attr_e('Get Shortcode', 'image-hover-effects'); ?></button>
	    				            	    <button class="button addcattop"><b title="<?php esc_attr_e('Add New Category', 'image-hover-effects'); ?>" class="dashicons dashicons-insert"></b></button>
	    				            	    <button class="button removecattop"><span class="dashicons dashicons-dismiss" title="<?php esc_attr_e('Delete Image', 'image-hover-effects'); ?>"></span></button>
	    				            	</h3> 
	    				            	<div class="accordian content">
	    				            	    <h3>
	    				            	        <a href="#"><?php esc_attr_e('Image', 'image-hover-effects'); ?></a>
	    				            	        <button class="button addimgtop"><b title="<?php esc_attr_e('Add New Image', 'image-hover-effects'); ?>" class="dashicons dashicons-insert"></b></button>
	    				            	        <button class="button removeimgtop"><span class="dashicons dashicons-dismiss" title="<?php esc_attr_e('Remove Image', 'image-hover-effects'); ?>"></span></button>
	    				            	    </h3>
	    				            	    <div>
	    				            	        <table class="form-table">
	    				            	            <!-- Category Name -->
	    				            	            <tr>
	    				            	                <td style="width:30%">
	    				            	                    <strong><?php esc_attr_e('Category Name', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td style="width:30%">
	    				            	                    <input type="text" class="catname widefat form-control">
	    				            	                </td>
	    				            	                <td style="width:40%">
	    				            	                    <p class="description"><?php esc_attr_e('Name the category which would be shown on tab. It is only for reference. Category name should be same for every image.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Image Name', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <input type="text" class="imgname widefat form-control" value="">
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Name to be shown on current inner tab. It will be for your reference only.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Upload Image', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <button class="addimage button"><?php esc_attr_e('Upload Image', 'image-hover-effects'); ?></button>
	    				            	                    <span class="image">
	    				            	                        <?php
	    				            	                        if (isset($data2['cap_img']) && $data2['cap_img'] != '') {
	    				            	                        	echo '<span><img src="' . esc_url($data2['cap_img']) . '"><span class="dashicons dashicons-dismiss"></span></span>';
	    				            	                        }
	    				            	                        ?>
	    				            	                    </span><br>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Upload Image on which hover effect with caption to be added.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>

	    				            	        </table>
	    				            	        <table class="form-table">
	    				            	            <tr>
	    				            	                <td style="width:30%">
	    				            	                    <strong><?php esc_attr_e('Caption Heading', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td style="width:30%">
	    				            	                    <input type="text" class="widefat capheading form-control">
	    				            	                </td>
	    				            	                <td style="width:40%">
	    				            	                    <p class="description"><?php esc_attr_e('Give heading to be shown on image.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Caption Description', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <textarea class="widefat capdesc form-control" id="" cols="30" rows="10"></textarea>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Give description to be shown on image under heading.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Caption Link', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <input type="text" class="widefat caplink form-control">
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Give complete URL of the link which opens when the user clicks over the image.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Open Link in New Tab', 'image-hover-effects'); ?> <b><a class="pro-feature" href="<?php echo esc_html($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <select class="capnewtab form-control">
	    				            	                        <option value="no"><?php esc_attr_e('No', 'image-hover-effects'); ?></option>
	    				            	                        <option value="yes"><?php esc_attr_e('Yes', 'image-hover-effects'); ?></option>
	    				            	                    </select>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Choose whether to open the link in a new tab or not.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Heading Color', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td class="insert-picker">
	    				            	                    <input type="text" class="head-color" value="#fff">
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Choose font color for the caption heading.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Description Color', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td class="insert-picker">
	    				            	                    <input type="text" class="desc-color" value="#fff">
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Choose font color for the caption description.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	        </table>
	    				            	        <table class="form-table">
	    				            	            <tr>
	    				            	                <td style="width:30%">
	    				            	                    <strong><?php esc_attr_e('Image Width', 'image-hover-effects'); ?> <b><a class="pro-feature" href="<?php echo esc_html($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				            	                </td>
	    				            	                <td style="width:30%">
	    				            	                    <input type="number" class="form-control thumbwidth">
	    				            	                </td>
	    				            	                <td style="width:40%">
	    				            	                    <p class="description"><?php esc_attr_e('Give width (keep width and height the same for circle style) for the image. <b>Default(220px)</b>', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Image Height', 'image-hover-effects'); ?> <b><a class="pro-feature" href="<?php echo esc_html($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <input type="number" class="form-control thumbheight">
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Give height (keep width and height the same for circle style) for the image. <b>Default(220px)</b>', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Image Shape', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <select class="styleopt form-control widefat">
	    				            	                        <option value="circle"><?php esc_attr_e('Circle', 'image-hover-effects'); ?></option>
	    				            	                        <option value="square"><?php esc_attr_e('Square', 'image-hover-effects'); ?></option>
	    				            	                    </select>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Select the shape of the image. It could be square or circle.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Select Hover Effect', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <select class="effectopt form-control widefat">
	    				            	                        <?php for ($i = 1; $i <= 20; $i++) : ?>
	    				            	                        	<option value="effect<?php echo esc_html($i); ?>"><?php echo esc_html('Effect' . $i); ?></option>
	    				            	                        <?php endfor; ?>
	    				            	                    </select>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Select hover animation.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Hover Animation Direction', 'image-hover-effects'); ?></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <select class="directionopt form-control widefat">
	    				            	                        <option value="left_to_right"><?php esc_attr_e('Left To Right', 'image-hover-effects'); ?></option>
	    				            	                        <option value="right_to_left"><?php esc_attr_e('Right To Left', 'image-hover-effects'); ?></option>
	    				            	                        <option value="top_to_bottom"><?php esc_attr_e('Top To Bottom', 'image-hover-effects'); ?></option>
	    				            	                        <option value="bottom_to_top"><?php esc_attr_e('Bottom To Top', 'image-hover-effects'); ?></option>
	    				            	                    </select>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Select the direction in which the animation occurs.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Images Per Row', 'image-hover-effects'); ?></strong> <br>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <select class="capgrid form-control widefat">
	    				            	                        <option value="12">1</option>
	    				            	                        <option value="6">2</option>
	    				            	                        <option value="4">3</option>
	    				            	                        <option value="3">4</option>
	    				            	                    </select>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Select how many images show in one row. Keep it the same for every image.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr>
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Select Caption Background Type', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_html($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <select class="capbgtype form-control widefat">
	    				            	                        <option value="image"><?php esc_attr_e('image', 'image-hover-effects'); ?></option>
	    				            	                        <option value="color"><?php esc_attr_e('color', 'image-hover-effects'); ?></option>
	    				            	                    </select>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Choose the background type. It can be an image or color. <b>Image over image</b> effect can be achieved with this option.', 'image-hover-effects'); ?></p>
	    				            	                    <a style="color:#428bca;font-weight: bold;" href="https://www.youtube.com/watch?v=vJByUSE_P1k&feature=youtu.be" target="_blank">How to add image over image effect</a>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr class="bgcolorrow">
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Caption Background Color', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_html($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <input type="text" class="form-control custom capbgcolor" value="#fff" />
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Choose the background color for the caption. (Default #1a4a72)', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr class="bgimagerow">
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Choose Background Image', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_html($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <button class="bgimage button"><?php esc_attr_e('Upload Image', 'image-hover-effects'); ?></button>
	    				            	                    <span class="backgroundimage"></span>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Choose the background image for the caption. (Default #1a4a72)', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	            <tr class="bgimagerow">
	    				            	                <td>
	    				            	                    <strong><?php esc_attr_e('Background Size', 'image-hover-effects'); ?><b> <a class="pro-feature" href="<?php echo esc_html($pro_link); ?>" target="_blank">(PRO Feature)</a></b></strong>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <select class="capbgsize form-control widefat">
	    				            	                        <option value="cover"><?php esc_attr_e('Cover', 'image-hover-effects'); ?></option>
	    				            	                        <option value="contain"><?php esc_attr_e('Contain', 'image-hover-effects'); ?></option>
	    				            	                        <option value="100% 100%"><?php esc_attr_e('Full', 'image-hover-effects'); ?></option>
	    				            	                    </select>
	    				            	                </td>
	    				            	                <td>
	    				            	                    <p class="description"><?php esc_attr_e('Choose the background size.', 'image-hover-effects'); ?></p>
	    				            	                </td>
	    				            	            </tr>
	    				            	        </table><br>
	    			            	        	<span class="moreimages">
	    			            	        	    <button class="button moreimg">
	    			            	        	        <b title="Add New" class="dashicons dashicons-plus-alt"></b><?php esc_html_e( 'Add New Image', 'image-hover-effects' ); ?>
	    			            	        	    </button>
	    			            	        	    <button class="button-primary addcat">
	    			            	        	        <?php esc_html_e( 'Add New Category', 'image-hover-effects' ); ?>
	    			            	        	    </button>
	    			            	        	    <button class="button-primary fullshortcode pull-right" id="1">
	    			            	        	        <?php esc_html_e( 'Get Shortcode', 'image-hover-effects' ); ?>
	    			            	        	    </button>
	    			            	        	</span>

	    				            	    </div>
	    				            	</div>


	    				        <?php endif; ?>
	    				    </div>
	    	    	        <button class="btn btn-success save-meta">
	    	    	            <?php esc_html_e( 'Save Changes', 'image-hover-effects' ); ?>
	    	    	        </button>
	    	    	        <br>
	    	                <span id="la-loader" class="pull-right"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/ajax-loader.gif' ); ?>"></span>
	    	                <span id="la-saved"><strong><?php esc_html_e( 'Changes Saved!', 'image-hover-effects' ); ?></strong></span>
	    				</div>
	    				<!-- <div style="text-align: center;">
	    				    <a href="https://webdevocean.com/product/image-hover-effects-pro-plugin/" target="_blank" style="text-decoration: none;">
	    				        <h4 style="border-radius: 20px; padding: 20px; background: #b72121; color: #fff; width: 40%; margin: 30px auto 0 auto; font-size: 24px;">
	    				            <?php //esc_html_e( 'Now Get PRO Version in Just $13', 'image-hover-effects' ); ?>
	    				        </h4>
	    				    </a>
	    				</div> -->
	    				<p class="clearfix"></p>

	    			
	    <?php } 

	    function render_caption_hovers($atts) {
	        $saved_captions = get_option('la_caption_hover');

	        if (isset($saved_captions['posts'])) {
	            $total_cols = 0;
	            ob_start(); ?>
	            <div class="image-hover-page-container animatedParent">
	                <div class="row">
	                    <?php foreach ($saved_captions['posts'] as $key => $data) : ?>
	                        <?php foreach ($data['allcapImages'] as $key => $data2) : ?>
	                            <?php if ($atts['id'] == $data2['shortcode']) : ?>
	                                <?php
	                                wp_enqueue_style('wdo-ihe-hover-css', plugins_url('css/image-hover.min.css', __FILE__), array(), '1.0');
	                                wp_enqueue_script('wdo-hover-front-js', plugins_url('js/front.js', __FILE__), array('jquery'), '1.0', true);
	                                $total_cols += $data2['cap_grid'];
	                                ?>
	                                <div class="col-lg-<?php echo esc_attr($data2['cap_grid']); ?> col-sm-6">
	                                    <div class="ih-item <?php echo esc_attr($data2['cap_style']); ?> <?php echo esc_attr($data2['cap_effect']); ?> <?php echo ($data2['cap_effect'] == 'effect6' && $data2['cap_style'] == 'circle') ? 'scale_up' : ''; ?> <?php echo ($data2['cap_effect'] == 'effect8' && $data2['cap_style'] == 'square') ? 'scale_up' : ''; ?> <?php echo ($data2['cap_effect'] == 'effect1' && $data2['cap_style'] == 'square' && $data2['cap_direction'] == 'left_to_right') ? 'left_and_right' : esc_attr($data2['cap_direction']); ?>">
	                                        <a class="taphover" href="<?php echo ($data2['cap_link'] != '') ? esc_url($data2['cap_link']) : 'javascript:void(0)'; ?>">
	                                            <?php if ($data2['cap_effect'] == 'effect1' && $data2['cap_style'] == 'circle') : ?>
	                                                <div class='spinner'></div>
	                                            <?php endif; ?>
	                                            <div class="img">
	                                                <img style="height:100%;" src="<?php echo ($data2['cap_img'] != '') ? esc_url($data2['cap_img']) : 'http://www.gemologyproject.com/wiki/images/5/5f/Placeholder.jpg'; ?>" alt="img">
	                                                <?php if ($data2['cap_effect'] == 'effect4' && $data2['cap_style'] == 'square') : ?>
	                                                    <div class='mask1'></div><div class='mask2'></div>
	                                                <?php endif; ?>
	                                            </div>
	                                            <?php if ($data2['cap_effect'] == 'effect8') : ?>
	                                                <div class="info-container">
	                                                    <div class="info">
	                                                        <h3 style="color:<?php echo esc_attr($data2['cap_headcolor']); ?><?php echo ($data2['cap_head'] == '') ? 'opacity:0;' : ''; ?>">
	                                                        	<?php echo ($data2['cap_head'] != '') ? wp_kses_data($data2['cap_head']) : ''; ?>
	                                                        </h3>
	                                                        <p style="color:<?php echo esc_attr($data2['cap_desccolor']); ?>">
	                                                            <?php echo ($data2['cap_desc'] != '') ? esc_html($data2['cap_desc']) : ''; ?>
	                                                        </p>
	                                                    </div>
	                                                </div>
	                                            <?php elseif ($data2['cap_effect'] == 'effect1' || $data2['cap_effect'] == 'effect5' || $data2['cap_effect'] == 'effect13' || $data2['cap_effect'] == 'effect18' || $data2['cap_effect'] == 'effect20' || $data2['cap_effect'] == 'effect9') : ?>
	                                                <div class="info" style="height:inherit;">
	                                                    <div class="info-back">
	                                                        <h3 style="color:<?php echo esc_attr($data2['cap_headcolor']); ?><?php echo ($data2['cap_head'] == '') ? 'opacity: 0;' : ''; ?>">
	                                                        	<?php echo ($data2['cap_head'] != '') ? wp_kses_post($data2['cap_head']) : ''; ?>
	                                                        </h3>
	                                                        <p style="color:<?php echo esc_attr($data2['cap_desccolor']); ?>">
	                                                            <?php echo ($data2['cap_desc'] != '') ? esc_html($data2['cap_desc']) : ''; ?>
	                                                        </p>
	                                                    </div>
	                                                </div>
	                                            <?php else : ?>
	                                                <div class="info">
	                                                    <h3 style="color:<?php echo esc_attr($data2['cap_headcolor']); ?><?php echo ($data2['cap_head'] == '') ? 'opacity: 0;' : ''; ?>">
	                                                    	<?php echo ($data2['cap_head'] != '') ? wp_kses_post(stripcslashes($data2['cap_head'])) : ''; ?>
	                                                    </h3>
	                                                    <p style="color:<?php echo esc_attr($data2['cap_desccolor']); ?>">
	                                                        <?php echo ($data2['cap_desc'] != '') ? esc_html($data2['cap_desc']) : ''; ?>
	                                                    </p>
	                                                </div>
	                                            <?php endif; ?>
	                                        </a>
	                                    </div>
	                                </div>
	                            <?php endif; ?>
	                            <?php if ($total_cols == 12) : ?>
	                                </div><div class="row">
	                            <?php endif; ?>
	                        <?php endforeach; ?>
	                    <?php endforeach; ?>
	                </div>
	            </div>
	            <?php
	        }
	        return ob_get_clean();
	    }

	}
?>