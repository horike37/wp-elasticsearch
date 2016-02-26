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
		__( 'Endpoint', 'wp-elasticsearch' ),
		'endpoint_render',
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
	<?php

}
