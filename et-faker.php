<?php
/*
Plugin Name: ET Faker
Plugin URI: https://www.enginethemes.com/
Description: Plugin giúp tạo dữ liệu mẫu cho post/custom post..
Version: 1.0
Author: Tuandq
Author URI: https://www.enginethemes.com/
License: GPLv2 or later
Text Domain: et-faker
*/

require_once 'includes/faker.php';

add_action('admin_menu', 'et_faker_create_menu');
function et_faker_create_menu() {
	/*$menu = add_menu_page('ET Faker', 'ET Faker', 'manage_options', 'et-faker', 'et_faker_add_post');
	$sub_add_post = add_submenu_page('et-faker', 'Add Post', 'Add Post', 'manage_options','et_faker_add_post');
	add_action( 'admin_print_styles-' . $menu, 'et_faker_load_styles' );
	add_action( 'admin_print_scripts-' . $menu, 'et_faker_load_scripts' );*/

	add_menu_page('ET Faker', 'ET Faker', 'manage_options', 'et-faker');
    $add_post_sub = add_submenu_page('et-faker', 'Add Post', 'Add Post', 'manage_options', 'et-faker-add-post','et_faker_add_post_menu');
    $add_user_sub = add_submenu_page('et-faker', 'Add User', 'Add User', 'manage_options', 'et-faker-add-user','et_faker_add_user_menu' );
    unset($GLOBALS['submenu']['et-faker'][0]);

    add_action( 'admin_print_styles-' . $add_post_sub, 'et_faker_load_styles' );
	add_action( 'admin_print_scripts-' . $add_post_sub, 'et_faker_load_scripts' );
	add_action( 'admin_print_styles-' . $add_user_sub, 'et_faker_load_styles' );
	add_action( 'admin_print_scripts-' . $add_user_sub, 'et_faker_load_scripts' );
}
function et_faker_load_styles($hook){
	wp_enqueue_style('et_faker_bootstrap', plugins_url('', __FILE__) . '/assets/css/bootstrap.min.css', false);
	wp_enqueue_style('et_faker_font_awesome', plugins_url('', __FILE__) . '/assets/css/font-awesome.min.css', false);
	wp_enqueue_style('et_faker_style', plugins_url('', __FILE__) . '/assets/css/et-faker.css', false);
}
function et_faker_load_scripts(){
	wp_enqueue_script('et_faker_js', plugins_url('', __FILE__) . '/assets/js/et-faker.js');
	wp_enqueue_script('et_faker_validate', plugins_url('', __FILE__) . '/assets/js/jquery.validate.js', array('jquery'));
}
function et_faker_add_post_menu() {

?>
<div class="wrap">
<h1>ET Faker - Add Post</h1>
<div class="container">
	<form class="form-horizontal">
	  	<div class="form-group">
	    	<label class="control-label col-sm-2" for="email"><?php _e('Post Types', 'et-faker')?></label>
	    	<div class="col-sm-4">
	    		<select class="form-control" id="post_type" name="post_type">
	    			<option value="post">Post</option>
	    			<option value="page">Page</option>
	    			<?php
		      			$args = array(
						   'public'   => true,
						   '_builtin' => false
						);
						$output = 'names';
						$operator = 'and';
						$post_types = get_post_types( $args, $output, $operator );
						foreach ( $post_types  as $post_type ) {
							echo '<option value='.$post_type.'>'.ucwords($post_type).'</option>';
						}
		      		?>
	    		</select>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<label class="control-label col-sm-2" for="pwd"><?php _e('Count Post', 'et-faker')?></label>
	    	<div class="col-sm-4">
	    		<select class="form-control" id="count_post" name="count_post">
	    			<option value="1">1</option>
	    			<option value="3">3</option>
	    			<option value="5">5</option>
	    			<option value="10">10</option>
	    		</select>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<label class="control-label col-sm-2" for="email"><?php _e('Author', 'et-faker')?></label>
	    	<div class="col-sm-4">
	    		<select class="form-control" id="post_author" name="author">
	    			<option value="0">Random</option>
	    			<?php
	    				$users = get_users();
	    				if($users)
	    				foreach ($users as $key => $user) {
	    					echo '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
	    				}
		      		?>
	    		</select>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<label class="control-label col-sm-2" for="email"><?php _e('Post Status', 'et-faker')?></label>
	    	<div class="col-sm-4">
	    		<select class="form-control" id="post_status" name="post_status">
	    			<option value="publish">Publish</option>
	    			<option value="draft">Draft</option>
	    			<option value="pending">Pending</option>
	    		</select>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<div class="col-sm-2">
	    	</div>
	    	<div class="col-sm-6">
	    		<div class="input_fields_wrap">
				    <button class="add_field_button btn btn-primary">Add Custom Field</button>
				    <!-- <a href="#" id="et-info"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span></a>
				    <a href="#" id="et-info-hide" style="display: none;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a> -->
				</div>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<div class="col-sm-offset-2 col-sm-10">
	      		<button id="add-post" type="button" class="btn btn-primary">Create Data</button>
	    	</div>
	  	</div>
	  	<input type="hidden" id="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
	</form>
	<pre id="et-help" class="col-md-6" style="display: none;">
		<a href="#" id="et-info-hide" style=""><i class="fa fa-times fa-2x" aria-hidden="true"></i></a>
		<span class="et-code">title</span><span class="et-exam">// 'Ms.'</span>
		<span class="et-code">firstName</span><span class="et-exam">// 'Maynard'</span>
		<span class="et-code">lastName</span><span class="et-exam">// 'Rachel'</span>
		<span class="et-code">word</span><span class="et-exam">// 'Sit vitae voluptas sint non voluptates.'</span>
		<span class="et-code">sentence</span><span class="et-exam">// 'Ms.'</span>
		<span class="et-code">text</span><span class="et-exam">// 'Fuga totam reiciendis qui architecto fugiat nemo. Consequatur recusandae qui cupiditate eos quod.'</span>
		<span class="et-code">address</span><span class="et-exam">// '8888 Cummings Vista Apt. 101, Susanbury, NY 95473'</span>
		<span class="et-code">country</span><span class="et-exam">// 'Falkland Islands (Malvinas)'</span>
		<span class="et-code">latitude</span><span class="et-exam">// '77.147489'</span>
		<span class="et-code">longitude</span><span class="et-exam">// '86.211205'</span>
		<span class="et-code">phoneNumber</span><span class="et-exam">// '201-886-0269 x3767'</span>
		<span class="et-code">email</span><span class="et-exam">// 'tkshlerin@collins.com'</span>
		<span class="et-code">userName</span><span class="et-exam">// 'wade55'</span>
		<span class="et-code">password</span><span class="et-exam">// 'k&|X+a45*2['</span>
		<span class="et-code">url</span><span class="et-exam">// 'http://www.skilesdonnelly.biz/aut-accusantium-ut-architecto-sit-et.html'</span>
	</pre>
	<pre id="log" class="col-md-6" style="display: none;">
		
	</pre>
</div>
</div>
<?php } ?>
<?php
function et_faker_add_user_menu() {
	
?>
<div class="wrap">
<h1>ET Faker - Add User</h1>
<div class="container">
	<form class="form-horizontal">
	  	<div class="form-group">
	    	<label class="control-label col-sm-2" for="pwd"><?php _e('Count Users', 'et-faker')?></label>
	    	<div class="col-sm-4">
	    		<select class="form-control" id="count_user" name="count_user">
	    			<option value="1">1</option>
	    			<option value="3">3</option>
	    			<option value="5">5</option>
	    			<option value="10">10</option>
	    		</select>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<label class="control-label col-sm-2" for="email"><?php _e('Roles', 'et-faker')?></label>
	    	<div class="col-sm-4">
	    		<select class="form-control" id="user_role" name="user_role">
	    			<?php
	    				$roles = get_editable_roles();
						if($roles)
						foreach ($roles as $role) {
							echo '<option value="'. $role['name'] .'"> '. $role['name'].' </option>';
						}
		      		?>
	    		</select>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<div class="col-sm-offset-2 col-sm-10">
	      		<button id="add-user" type="button" class="btn btn-primary">Create Data</button>
	    	</div>
	  	</div>
	  	<input type="hidden" id="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
	</form>
	<pre id="log" class="col-md-6" style="display: none;">

	</pre>
</div>
</div>
<?php } ?>