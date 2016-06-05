<?php
add_action( 'admin_menu', 'wpels_add_admin_menu' );
add_action( 'admin_init', 'wpels_settings_init' );


function wpels_add_admin_menu() {
	add_options_page( 'WP Simple Elasticsearch', 'WP Simple Elasticsearch', 'manage_options', 'wp_elasticsearch', 'wpels_options_page' );

}


function wpels_settings_init() {

	register_setting( 'wp_elasticsearch', 'wpels_settings' );
	register_setting( 'aws_elasticsearch_service', 'wpels_settings' );

	add_settings_section(
		'wpels_wp_elasticsearch_section',
		__( '', 'wp-elasticsearch' ),
		'wpels_settings_section_callback',
		'wp_elasticsearch'
	);

	add_settings_field(
		'endpoint',
		__( 'Elasticsearch Endpoint(not http://)', 'wp-elasticsearch' ),
		'endpoint_render',
		'wp_elasticsearch',
		'wpels_wp_elasticsearch_section'
	);

	add_settings_field(
		'port',
		__( 'Port', 'wp-elasticsearch' ),
		'port_render',
		'wp_elasticsearch',
		'wpels_wp_elasticsearch_section'
	);

	add_settings_field(
		'index',
		__( 'index', 'wp-elasticsearch' ),
		'index_render',
		'wp_elasticsearch',
		'wpels_wp_elasticsearch_section'
	);

	add_settings_field(
		'type',
		__( 'type', 'wp-elasticsearch' ),
		'type_render',
		'wp_elasticsearch',
		'wpels_wp_elasticsearch_section'
	);

	add_settings_field(
		'custom_fields',
		__( 'Costom Fields', 'wp-elasticsearch' ),
		'custom_fields_render',
		'wp_elasticsearch',
		'wpels_wp_elasticsearch_section'
	);
	
	add_settings_section(
		'wpels_aws_elasticsearch_service_section',
		__( 'For AWS Users', 'wp-elasticsearch' ),
		'aws_elasticsearch_service_section_callback',
		'aws_elasticsearch_service'
	);
	
	add_settings_field(
		'aws_auth',
		__( 'Use AWS IAM Setting', 'wp-elasticsearch' ),
		'aws_auth_render',
		'aws_elasticsearch_service',
		'wpels_aws_elasticsearch_service_section'
	);

	add_settings_field(
		'access_key',
		__( 'AWS Access Key', 'wp-elasticsearch' ),
		'access_key_render',
		'aws_elasticsearch_service',
		'wpels_aws_elasticsearch_service_section'
	);

	add_settings_field(
		'secret_key',
		__( 'AWS Secret Key', 'wp-elasticsearch' ),
		'secret_key_render',
		'aws_elasticsearch_service',
		'wpels_aws_elasticsearch_service_section'
	);

	add_settings_field(
		'region',
		__( 'Region', 'wp-elasticsearch' ),
		'region_render',
		'aws_elasticsearch_service',
		'wpels_aws_elasticsearch_service_section'
	);

}


function endpoint_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[endpoint]' style="width:60%" placeholder="example.com/elasticsearch" value='<?php echo isset($options['endpoint']) ? $options['endpoint'] : ''; ?>'>
	<?php

}


function port_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[port]' placeholder="443" value='<?php echo isset($options['port']) ? $options['port'] : ''; ?>'>
	<?php

}


function index_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[index]' placeholder="wordpress" value='<?php echo isset($options['index']) ? $options['index'] : ''; ?>'>
	<?php

}


function type_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[type]' placeholder="blog" value='<?php echo isset($options['type']) ? $options['type'] : ''; ?>'>
	<?php

}


function custom_fields_render() {

	$options = get_option( 'wpels_settings' );
	
	?>
	<textarea cols='40' rows='5' name='wpels_settings[custom_fields]'><?php echo isset($options['custom_fields']) ? $options['custom_fields'] : ''; ?></textarea>
	<?php

}

function aws_auth_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input id="aws_auth_setting" type='checkbox' name='wpels_settings[aws_auth]' value='true' <?php if(!empty($options['aws_auth'])) { echo 'checked'; } ?>>
	<?php

}


function access_key_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[access_key]' value='<?php echo isset($options['access_key']) ? $options['access_key'] : ''; ?>'>
	<?php

}


function secret_key_render() {

	$options = get_option( 'wpels_settings' );
	?>
	<input type='text' name='wpels_settings[secret_key]' value='<?php echo isset($options['secret_key']) ? $options['secret_key'] : ''; ?>'>
	<?php

}


function region_render() {

	$options = get_option( 'wpels_settings' );
	if ( !isset( $options['region'] ) ) {
		$options['region'] = '';
	}
	
	?>
	<select class="aws_auth_setting" name='wpels_settings[region]'>
	  <option <?php selected( $options['region'], 'us-east-1' ) ?> value="us-east-1">US East (N. Virginia)</option>
	  <option <?php selected( $options['region'], 'us-west-2' ) ?> value="us-west-2">US West (Oregon)</option>
	  <option <?php selected( $options['region'], 'us-west-1' ) ?> value="us-west-1">US West (N. California)</option>
	  <option <?php selected( $options['region'], 'eu-west-1' ) ?> value="eu-west-1">EU (Ireland)</option>
	  <option <?php selected( $options['region'], 'eu-central-1' ) ?> value="eu-central-1">EU (Frankfurt)</option>
	  <option <?php selected( $options['region'], 'ap-southeast-1' ) ?> value="ap-southeast-1">Asia Pacific (Singapore)</option>
	  <option <?php selected( $options['region'], 'ap-northeast-1' ) ?> value="ap-northeast-1">Asia Pacific (Tokyo)</option>
	  <option <?php selected( $options['region'], 'ap-southeast-2' ) ?> value="ap-southeast-2">Asia Pacific (Sydney)</option>
	  <option <?php selected( $options['region'], 'ap-northeast-2' ) ?> value="ap-northeast-2">Asia Pacific (Seoul)</option>
	  <option <?php selected( $options['region'], 'sa-east-1' ) ?> value="sa-east-1">South America (São Paulo)</option>
	</select>
	<?php

}


function wpels_settings_section_callback() {

	echo __( '', 'wp-elasticsearch' );

}

function aws_elasticsearch_service_section_callback() {

	echo __( 'If you use Elasticsearch Service in AWS, you can IAM setting below', 'wp-elasticsearch' );

}


function wpels_options_page() {

	?>
	<form action='options.php' method='post'>

		<h1>WP Simple Elasticsearch</h1>
		<?php
		settings_fields( 'wp_elasticsearch' );
		do_settings_sections( 'wp_elasticsearch' );
		
		settings_fields( 'aws_elasticsearch_service' );
		do_settings_sections( 'aws_elasticsearch_service' );
		submit_button();
		?>

	</form>

	<form action='' method='post'>
		<?php
		wp_nonce_field( 'data_sync', 'wp_elasticsearch_datasync' );
		submit_button( __( 'Post Data sync to Elasticsearch', 'wp-elasticsearch' ) );
		?>
	</form>
	<?php

}

add_action( 'admin_head', function() {
?>
<script type="text/javascript">
  jQuery(document).ready(function($){
    if ( !$('#aws_auth_setting').is(':checked') ) {
      $('#aws_auth_setting').closest('tr').nextAll().css('display', 'none');
    }

    $('#aws_auth_setting').click(function() {
      $('#aws_auth_setting').closest('tr').nextAll().slideToggle(this.checked);
    });
  });
</script>
<?php
});