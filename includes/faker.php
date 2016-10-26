<?php
	require 'autoload.php';
	function et_faker_add_post() {
		$post_type = $_POST['post_type'];
		//$count_post = $_POST['count_post'];
		$custom_field = $_POST['custom_field'];
		$taxonomy_objects = get_object_taxonomies($post_type);
		$data = array();
		$faker = Faker\Factory::create();
	    $title = $faker->sentence(5);
	    $image = $faker->imageUrl($width = 250, $height = 250);
	    /*$args_user = array( 'role' => 'author',);
		$users = get_users( $args_user );
		if($users){
			$user_arr = array();
			foreach ($users as $key => $user) {
				$user_arr[] = $user->ID;
			}
			$post_author = $user_arr[ rand(0, count($user_arr)-1) ];
		} else
			$post_author = 1;*/
		if($custom_field){
			$meta_post = array();
			foreach ($custom_field as $key => $field) {
				$arr = explode("|", $field);
				if($arr){
					$value = trim($arr[1]);
					$meta_post[trim($arr[0])] = $faker->$value;
				}
			}
		}
		//var_dump($value);
		//var_dump($meta_post);
	    /*$meta_post = array(
	    				'et_location_lng'  => $faker->longitude(103,108) ,
	    				'et_location_lat'  => $faker->latitude(9,22),
    					'et_full_location' => $faker->address,
				        'et_phone'		   => $faker->phoneNumber,
				        'et_url'           => $faker->url,
				        'et_fb_url'        => $faker->url,
				        'et_google_url'    => $faker->url,
				        'et_twitter_url'   => $faker->url,
				        'et_carousels'	   => 1,
	    			);*/
	    $args = array(
	    	'post_type'		=> $post_type,
		    'post_title'    => $title,
		    'post_content'  => $faker->text,
		    'post_status'   => 'publish',
		    'post_author'   => $post_author,
		    'meta_input'	=> $meta_post,
		);
		if($taxonomy_objects){
			$arr_tax = array();
			foreach ($taxonomy_objects as $key => $tax) {
				$terms = get_terms($tax,array('hide_empty' => false));
				if($terms){
					foreach ($terms as $key => $term) {
						$arr_term[] = $term->term_id;
					}
					$arr_tax[] = array('name' => $tax, 'value' => $arr_term[rand(0, count($arr_term)-1)]);
				}
			}
		}
	    $post_id = wp_insert_post($args);
	    if($post_id){
	    	$data[] = array('post_id' => $post_id, 'url' => get_the_permalink($post_id), 'title' => get_the_title($post_id));
	    	//Assign taxonomy
	    	if($arr_tax){
	    		foreach ($arr_tax as $key => $value) {
	    			wp_set_object_terms( $post_id, $value['value'], $value['name']);
	    		}
	    	}
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents($image);
			$filename = str_replace(" ", "-", $title) . '.jpg';
			if(wp_mkdir_p($upload_dir['path']))
			    $file = $upload_dir['path'] . '/' . $filename;
			else
			    $file = $upload_dir['basedir'] . '/' . $filename;
			file_put_contents($file, $image_data);
			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
			    'post_mime_type' => $wp_filetype['type'],
			    'post_title' => sanitize_file_name($filename),
			    'post_status' => 'auto',
			    'post_content' => '',
			    'post_status' => 'inherit',
			);
			$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
			require_once(ABSPATH . '/wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			update_post_meta( $post_id, '_thumbnail_id', $attach_id );
	    }
		if($data)
			wp_send_json(array(
	            'success'   => true,
	            'data'	    => $data,
	            'meta_post' => $meta_post,
	        ));
		else
			wp_send_json(array(
	            'success'    => false,
	            'data'	     => $data,
	        ));
	}
	add_action( 'wp_ajax_et_faker_add_post', 'et_faker_add_post' );
	add_action( 'wp_ajax_nopriv_et_faker_add_post', 'et_faker_add_post' );