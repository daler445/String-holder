<?php
/*
Plugin Name: String Holder
Plugin URI: https://github.com/daler445/String-holder
Description: Store strings and use it in frontend as shortcode or php function
Version: 1.0
Author: Azimov Daler
Author URI: https://github.com/daler445
Text Domain: string-holder
*/

	$textDomain = "string-holder";

	/**
	 *	Creates admin menu
	 */
	add_action('admin_menu', 'stho_plugin_menu');
	function stho_plugin_menu() {
		add_menu_page('String Holder', 'String Holder', 'administrator', 'string_holder', 'stho_admin_page', 'dashicons-editor-textcolor');
	}

	/**
	 *	Adds data to database
	 *	@global wpdb
	 *	@var name (text)
	 */
	function stho_add_data($name) {
		if (($name == null) || ($name == "")) {
			echo "<div class='notice notice-error inline is-dismissible'><p>Error. Data is null</p></div>";
		} else {
			$data = array(
				'stho_id'=>null,
				'stho_text'=>$name
			);
			$format = array(null, '%s');
			global $wpdb;
			$table_name = $wpdb->prefix . 'stho_data';
			$wpdb->insert($table_name, $data, $format); 
			$rowid = $wpdb->insert_id;
			echo "<div class='notice notice-success inline is-dismissible'><p>Added. ID: $rowid</p></div>";
		}
	}
	/**
	 *	Deleted data from database
	 *	@global wpdb
	 *	@var id
	 */
	function stho_delete_data($id) {
		if (($id == null) || ($id == "") || ($id == 0)) {
			echo "<div class='notice notice-error inline is-dismissible'><p>Error. Data is null</p></div>";
		} else {
			global $wpdb;
			$table_name = $wpdb->prefix . 'stho_data';
			$wpdb->query(
				"DELETE FROM `$table_name` WHERE `stho_id` = $id"
			);
			echo "<div class='notice notice-success inline is-dismissible'><p>Deleted.</p></div>";
		}
	}
	/**
	 *	Edit data on database
	 *	@global wpdb
	 *	@var id
	 *	@var newData (text)
	 */
	function stho_edit_data($id, $newData) {
		if (($id == null) || ($newData == null)) {
			echo "<div class='notice notice-error inline is-dismissible'><p>Error. No data to edit</p></div>";
		} else {
			global $wpdb;
			$table_name = $wpdb->prefix . 'stho_data';
			$wpdb->query(
				"UPDATE `$table_name` SET `stho_text` = '$newData' WHERE `stho_id` = $id;"
			);
			echo "<div class='notice notice-success inline is-dismissible'><p>Updated.</p></div>";
		}
	}
	
	/**
	 *	Display admin interface, errors and notices
	 *	@global wpdb
	 *	@var post_act
	 *
	 *	@var new_name_txt
	 *
	 *	@var delete_id
	 *
	 *	@var edit_id
	 *	@var current_text
	 *
	 *	@var edited_id
	 *	@var edit_name_txt
	 */
	function stho_admin_page() {
		if ($_POST["post_act"] == 1) {
			stho_add_data($_POST["new_name_txt"]);
		} else if ($_POST["post_act"] == 2) {
			stho_delete_data( $_POST["delete_id"] );
		}
		else if ($_POST["post_act"] == 4) {
			$edit_id = $_POST["edited_id"];
			$edited_text = $_POST["edit_name_txt"];
			stho_edit_data($edit_id, $edited_text);
		}
		if ($_POST["post_act"] == 3) {
			$edit_id = $_POST["edit_id"];
			$current_text = $_POST["current_text"];
?>
	<section id="new_data">
		<h3>Edit data</h3>
		<h4>ID: <?php echo $edit_id; ?></h4>
		<form action="#" method="POST">
			<input type="hidden" name="post_act" value="4">
			<input type="hidden" name="edited_id" value="<?php echo $edit_id; ?>">
			<input type="text" class="regular-text" placeholder="Enter text here" name="edit_name_txt" value="<?php echo $current_text; ?>">
			<br /><br />
			<input type="submit" class="button-primary" value="Edit">
	</section>
<?php
		} else {
?>
	<section id="display_data">
		<h3>List</h3>
		<table class="widefat">
			<thead>
			<tr>
				<th class="row-title">ID</th>
				<th>Value</th>
				<th>Shortcode</th>
				<th>PHP get code</th>
				<th>PHP echo code</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
			</thead>
			<tbody>
				<?php
					global $wpdb;
					$table_name = $wpdb->prefix . 'stho_data';
					$sql = "SELECT * FROM `$table_name`";
					$rows_ffdb = $wpdb->get_results("SELECT * FROM `$table_name`");
					if ($rows_ffdb) {
						foreach ($rows_ffdb as $row) {
							$id = $row->stho_id;
							$text = $row->stho_text;
							$shortcode_txt = "<code>[stho id=$id]</code>";
							$phpgetcode_txt = "<code><&#63;php get_stho($id); &#63;></code>";
							$phpechocode_txt = "<code><&#63;php the_stho($id); &#63;></code>";
							echo $output = "
								<tr>
									<td>$id</td>
									<td>$text</td>
									<td>$shortcode_txt</td>
									<td>$phpgetcode_txt</td>
									<td>$phpechocode_txt</td>
									<td>
							<form action='#' method='POST'>
								<input type='hidden' name='post_act' value='3'>
								<input type='hidden' name='edit_id' value='$id'>
								<input type='hidden' name='current_text' value='$text'>
								<input type='submit' value='Edit' class='button-primary'>
							</form>
									</td>
									<td>
										<form action='#' method='POST'>
											<input type='hidden' name='post_act' value='2'>
											<input type='hidden' name='delete_id' value='$id'>
											<input type='submit' value='Delete' class='button-primary'>
										</form>
									</td>
								</tr>
							";
						}
					}
				?>
			</tbody>
		</table>
	</section>
	<section id="new_data">
		<h3>Add new</h3>
		<form action="#" method="POST">
			<input type="hidden" name="post_act" value="1">
			<input type="text" class="regular-text" placeholder="Enter new name" name="new_name_txt">
			<br /><br />
			<input type="submit" class="button-primary" value="Add">
	</section>
<?php
	}
	}

	/**
	 *	Create database if not exists on plugin activation
	 *	@global wpdb
	 */
	function stho_db_create() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$table_name = $wpdb->prefix . 'stho_data';


		$sql = "CREATE TABLE IF NOT EXISTS `$table_name`  ( 
			`stho_id` INT(255) NOT NULL AUTO_INCREMENT, 
			`stho_text` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			PRIMARY KEY (`stho_id`)
		) $charset_collate;";
		dbDelta($sql);
	}
	register_activation_hook( __FILE__, 'stho_db_create' );



	/**
	 *	Create shortcode [stho] if not exists
	 */
	if ( ! shortcode_exists( 'stho' ) ) {
		/**
		 *	Shortcode that return translatable text
		 *	@global wpdb
		 *	@var id
		 *	@return text
		 */
		function stho_shortcode_func($atts) {
			$a = shortcode_atts( array(
				'id' => null,
			), $atts );
			$id = $a['id'];
			if (($id != null) && ($id != 0) && (is_numeric($id))) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'stho_data';
				$rows_sh = $wpdb->get_results("SELECT * FROM `$table_name` WHERE stho_id = $id");
				if ($rows_sh[0]) {
					return __($rows_sh[0]->stho_text, $textDomain);
				} else {
					return null;
				}
			}
			else {
				return null;
			}
		}
		add_shortcode('stho', 'stho_shortcode_func');
	}

	/**
	 *	Create function "the_stho" if not exists
	 */
	if ( ! function_exists( 'the_stho' ) ) {
		/**
		 *	Function that print translatable text
		 *	@global wpdb
		 *	@var id
		 *	@return text
		 */
		function the_stho($id) {
			if (($id != null) && ($id != 0) && (is_numeric($id))) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'stho_data';
				$rows_sh = $wpdb->get_results("SELECT * FROM `$table_name` WHERE stho_id = $id");
				if ($rows_sh[0]) {
					_e( $rows_sh[0]->stho_text, $textDomain );
				}
			}
		}
	}

	/**
	 *	Create function "get_stho" if not exists
	 */
	if ( ! function_exists( 'the_stho' ) ) {
		/**
		 *	Function that return translatable text
		 *	@global wpdb
		 *	@var id
		 *	@return text
		 */
		function get_stho($id) {
			if (($id != null) && ($id != 0) && (is_numeric($id))) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'stho_data';
				$rows_sh = $wpdb->get_results("SELECT * FROM `$table_name` WHERE stho_id = $id");
				if ($rows_sh[0]) {
					return __($rows_sh[0]->stho_text, $textDomain);
				}
			}
		}
	}


?>