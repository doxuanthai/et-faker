<?php
	require 'autoload.php';
	//Add post
	function et_faker_add_post() {
		$post_type = $_POST['post_type'];
		$custom_field = isset($_POST['custom_field']) ? $_POST['custom_field'] : '';
		$post_author = isset($_POST['post_author']) ? $_POST['post_author'] : '';
		$post_status = isset($_POST['post_status']) ? $_POST['post_status'] : '';
		$taxonomy_objects = get_object_taxonomies($post_type);
		$data = array();
		$faker = Faker\Factory::create();
	    $title = $faker->sentence(5);
	    $image = $faker->imageUrl($width = 250, $height = 250);
	    if(!$post_author){
	    	$users = get_users( $args_user );
			if($users){
				$user_arr = array();
				foreach ($users as $key => $user) {
					$user_arr[] = $user->ID;
				}
				$post_author = $user_arr[ rand(0, count($user_arr)-1) ];
			} else
				$post_author = 1;
	    }
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
					if($value)
						$meta_post[trim($arr[0])] = $faker->$value;
				}
			}
		}
	    $args = array(
	    	'post_type'		=> $post_type,
		    'post_title'    => $title,
		    'post_content'  => $faker->text,
		    'post_status'   => $post_status,
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
	//Add user
	function et_faker_add_user(){
		$user_role = isset($_POST['user_role']) ? $_POST['user_role'] : '';
		$faker = Faker\Factory::create();
		$user_login = $faker->userName;
		$args = array(
	    	'user_login'    => $user_login,
		    'user_email'    => $faker->email,
		    'first_name'    => $faker->firstName,
		    'last_name'     => $faker->lastName,
		    'role'			=> strtolower($user_role),
		);
		$user_id = wp_insert_user($args);
		if($user_id){
			$data[] = array('id' => $user_id, 'user_login' => $user_login, 'url' => get_author_posts_url($user_id));
		}
		if($data)
			wp_send_json(array(
	            'success'   => true,
	            'data'	    => $data,
	        ));
		else
			wp_send_json(array(
	            'success'    => false,
	            'data'	     => $data,
	        ));
	}
	add_action( 'wp_ajax_et_faker_add_user', 'et_faker_add_user' );
	add_action( 'wp_ajax_nopriv_et_faker_add_user', 'et_faker_add_user' );