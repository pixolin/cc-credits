<?php
/*
* This is the Class being set up for the plugin.
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;


class CC_Credits
{
	private $prefix;
	private $custom_meta_fields;

	function __construct() {

		//Set prefix to distinguish meta box fields
		$this->prefix = 'ccc_';

		//This defines the fields used in the meta box
		$this->custom_meta_fields = array(
	    array(
	        'label'=> __('Title of Image', 'cc-credits'),
	        'id'    => $this->prefix.'image',
	        'type'  => 'text'
	    ),
	    array(
	        'label'=> __('Year of Creation', 'cc-credits'),
	        'id'    => $this->prefix.'year',
	        'type'  => 'text'
	    ),
	    array(
	        'label'=> __('Author', 'cc-credits'),
	        'id'    => $this->prefix.'author',
	        'type'  => 'text'
	    ),
	    array(
	        'label'=> __('URL', 'cc-credits'),
	        'id'    => $this->prefix.'url',
	        'type'  => 'text'
	    ),
	    array(
	        'label'=> __('License', 'cc-credits'),
	        'id'    => $this->prefix.'license',
	        'type'  => 'select',
	        'options' => array (
	            'BY'        => array (
	                'label' => __('Attribution', 'cc-credits'),
	                'value' => 'BY'
	            ),
	            'BY-SA'     => array (
	                'label' => __('Attribution | ShareAlike', 'cc-credits'),
	                'value' => 'BY-SA'
	            ),
	            'BY-ND'     => array (
	                'label' => __('Attribution | NoDerivs', 'cc-credits'),
	                'value' => 'BY-ND'
	            ),
	            'BY-NC'     => array (
	                'label' => __('Attribution | NonCommercial', 'cc-credits'),
	                'value' => 'BY-NC'
	            ),
	            'BY-NC-SA'  => array (
	                'label' => __('Attribution | NonCommercial | ShareAlike', 'cc-credits'),
	                'value' => 'BY-NC-SA'
	            ),
	            'BY-NC-ND'  => array (
	                'label' => __('Attribution | NonCommercial | NoDerivs', 'cc-credits'),
	                'value' => 'BY-NC-ND'
	            ),
	            'None'      => array (
	                'label' => __('None/Public Domain', 'cc-credits'),
	                'value' => 'None'
	            )
						)
	    ),
	    array(
	        'label'=> __('Version', 'cc-credits'),
	        'id'    => $this->prefix.'version',
	        'type'  => 'select',
	        'options' => array (
	            '2.0' => array (
	                'label' => '2.0',
	                'value' => '2.0'
	            ),
	            '3.0' => array (
	                'label' => '3.0',
	                'value' => '3.0'
	            ),
	            '4.0' => array (
	                'label' => '4.0',
	                'value' => '4.0'
	            ),
	            '1.0' => array (
	                'label' => '1.0',
	                'value' => '1.0'
	            )
	        )
	    )
  );

	//Now that we set up some fields for a meta box, let's run some functions
	$this->run();

	} //ends function __construct()


	public function run() {
		// hooks & filters
		add_action( 'add_meta_boxes', array( $this, 'ccc_add_metabox' ) );
		add_action( 'save_post',      array( $this, 'ccc_save_meta' ) );
		add_filter( 'the_content',    array( $this, 'ccc_attribution' ) );

		// other stuff
		load_plugin_textdomain( 'cc-credits', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


  // Add a meta box
	public function ccc_add_metabox() {

		// define Post Types that will show Meta Box (can be extended for Custom Post Types)
    $post_types = array( 'post', 'page' );

    // Now we want a metabox for each post type
    foreach( $post_types as $post_type ) {
        add_meta_box(
            'ccc_meta_box', // $id
            'Creative Commons Credits', // $title
            array( $this, 'ccc_meta_box_callback'), // $callback
            $post_type, // $page
            'normal', // $context
            'high' // $priority
        );
    }

	}

	// Callback function for meta box
	public function ccc_meta_box_callback() {

			global $post;

	    // Use nonce for verification
	    wp_nonce_field( basename( __FILE__ ), 'custom_meta_box_nonce' );

	    // Begin the field table and loop
	    echo '<table class="form-table">';
	    foreach ($this->custom_meta_fields as $field) {
	        // get value of this field if it exists for this post
	        $meta = get_post_meta($post->ID, $field['id'], true);

	        echo '<tr>
	                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
	                <td>';
	                switch($field['type']) {
	                    // text
	                    case 'text':
	                    echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />';
	                    break;
	                    // select
	                    case 'select':
	                    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
	                    foreach ($field['options'] as $option) {
	                        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
	                    }
	                    break;
	                } //end switch
	        echo '</td></tr>';
	    } // end foreach
	    echo '</table>'; // end table


 	    echo $this->ccc_get_post_custom( $post->ID );


	}

	// after we added a meta box, created a callback we want to save the data
	public function ccc_save_meta( $post_id ) {

    // verify nonce
    if (!isset($_POST['custom_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }
    // loop through fields and save the data
    foreach ($this->custom_meta_fields as $field) {
        if(!empty($_POST[$field['id']])) {
            update_post_meta($post_id, $field['id'], wp_kses( $_POST[$field['id']], $allowed_html ));
        } else {
            delete_post_meta($post_id, $field['id']);
        }
    } // end foreach

	}

	//finally, we want the correct attibution (if any) to be added underneath each post and page
	function ccc_attribution($content) {
			global $post;
			$attribution = $this->ccc_get_post_custom($post->ID);

	    if(is_main_query()) {
	      return $content . $attribution;
	    }
	}

	function ccc_get_post_custom($post_id) {

			$ccc_fields = get_post_custom($post_id);

	    if(!empty($ccc_fields['ccc_image'][0])) {

	        $ccc_url     = $ccc_fields['ccc_url'][0];
	        $ccc_image   = $ccc_fields['ccc_image'][0];
	        $ccc_year    = $ccc_fields['ccc_year'][0];
	        $ccc_author  = $ccc_fields['ccc_author'][0];
	        $ccc_license = $ccc_fields['ccc_license'][0];
	        $ccc_version = $ccc_fields['ccc_version'][0];
	        //check if URL contains http:// or https:// and add, if it doesn't
	        $ccc_url_scheme = parse_url( $ccc_url );
	        if ( empty( $ccc_url_scheme['scheme'] ) ) {
	            $ccc_url = 'http://' . $ccc_url;
	        }
	        return '<hr>'.
	                __('Image', 'cc-credits').': <a href="'.esc_url($ccc_url).'">'.$ccc_image.'</a>
	                &copy' .$ccc_year.' '.$ccc_author.',
	                <a href="http://creativecommons.org/licenses/'.strtolower($ccc_license).'/'.$ccc_version.'" target="_blank" rel="nofollow">CC-'.$ccc_license.'</a>,

	                '.$ccc_version;
    	}
	}



} //ends Class CC_Credits