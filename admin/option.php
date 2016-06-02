<?php
add_action( 'admin_menu', 'wpels_add_admin_menu' );
add_action( 'admin_init', 'wpels_settings_init' );


function wpels_add_admin_menu() {

	add_options_page( 'WP Elasticsearch', 'WP Elasticsearch', 'manage_options', 'wp_elasticsearch', 'wpels_options_page' );

}


function wpels_settings_init() {

	register_setting( 'wpElasticsearch', 'wpels_settings' );

	add_settings_section(
		'wpels_wpElasticsearch_section',
		__( '', 'wp-elasticsearch' ),
		'wpels_settings_section_callback',
		'wpElasticsearch'
	);

	add_settings_field(
		'endpoint',
		__( 'Elasticsearch Endpoint', 'wp-elasticsearch' ),
		'endpoint_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'port',
		__( 'Port', 'wp-elasticsearch' ),
		'port_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'aws_auth',
		__( 'AWS Auth', 'wp-elasticsearch' ),
		'aws_auth_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'access_key',
		__( 'AWS Access Key', 'wp-elasticsearch' ),
		'access_key_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'secret_key',
		__( 'AWS Secret Key', 'wp-elasticsearch' ),
		'secret_key_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'region',
		__( 'Region', 'wp-elasticsearch' ),
		'region_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'index',
		__( 'index', 'wp-elasticsearch' ),
		'index_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'type',
		__( 'type', 'wp-elasticsearch' ),
		'type_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

	add_settings_field(
		'custom_fields',
		__( 'Costom Fields', 'wp-elasticsearch' ),
		'custom_fields_render',
		'wpElasticsearch',
		'wpels_wpElasticsearch_section'
	);

}


function endpoint_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[endpoint]' value='<?php echo $options['endpoint']; ?>'>
	<?php

}


function port_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[port]' value='<?php echo $options['port']; ?>'>
	<?php

}


function aws_auth_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='checkbox' name='wpels_settings[aws_auth]' value='true' <?php if(!empty($options['aws_auth'])) { echo 'checked'; } ?>>
	<?php

}


function access_key_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[access_key]' value='<?php echo $options['access_key']; ?>'>
	<?php

}


function secret_key_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[secret_key]' value='<?php echo $options['secret_key']; ?>'>
	<?php

}


function region_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[region]' value='<?php echo $options['region']; ?>'>
	<?php

}


function index_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[index]' value='<?php echo $options['index']; ?>'>
	<?php

}


function type_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[type]' value='<?php echo $options['type']; ?>'>
	<?php

}


function custom_fields_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<textarea cols='40' rows='5' name='wpels_settings[custom_fields]'><?php echo $options['custom_fields']; ?></textarea>
	<?php

}


function wpels_settings_section_callback() {

	echo __( '', 'wp-elasticsearch' );

}


function wpels_options_page() {

	?>
	<form action='options.php' method='post'>

		<h2>WP Elasticsearch</h2>
		<?php
		settings_fields( 'wpElasticsearch' );
		do_settings_sections( 'wpElasticsearch' );
		submit_button();
		?>

	</form>

	<form action='' method='post'>
		<?php
		wp_nonce_field( 'data_sync', 'wpElasticsearchDatasync' );
		submit_button( __( 'Post Data sync to Elasticsearch', 'wp-elasticsearch' ) );
		?>
	</form>
	<?php

}
