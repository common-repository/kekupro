<?php
/*
Plugin Name: KeKu Pro: Drive sales calls from your website. Analyze and forward calls for free.
Plugin URI: http://keku.pro/
Description: With KeKu Pro widget, customers call you from around the world for free. Put a free widget on your website. Receive calls on your phone in the USA or Canada for free. Track and analyze calls and website visitors’ behavior. Listen to call recordings. Integrate with your IVR. Get a free account at http://keku.pro/ It takes 3 minutes or less.
Author: kekupro
Version: 1.0
*/

add_action('admin_menu', 'kekupro_admin_menu');
add_action('wp_footer', 'kekupro');
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'kekupro_settings_link');

function kekupro()
{
	global $current_user;
	//$current_user = wp_get_current_user();

	$config = get_kekupro_config();

	if (empty($config['widgetId']))
	{
		echo '<!-- Error: keku.pro Plugin is not installed properly. Check plugin settings. -->';
		return false;
	}

	get_currentuserinfo();

	$userId = $current_user->ID;
	$userEmail = $current_user->user_email;
	$userLogin = $current_user->user_login;
	$userName = $current_user->display_name;

	if (empty($userName))
	{
		$userName = $current_user->user_firstname . ' ' . $current_user->user_lastname;
	}

	echo "
	<!-- KeKu Pro Widget -->
	<script>
		(function(ke,ku,p,r,o,w,d){ if (ke[o]) return; ke[o] = { widgetId: '{$config['widgetId']}' }; w = ku.createElement(p); w.async = 1; w.src = r; d = ku.getElementsByTagName(p)[0]; d.parentNode.insertBefore(w, d); })(window,document,'script','https://widget.keku.pro/loader.js','kekuProAPIConfig');
		try {
			kekuPro('track_user', {
				'id': '{$userId}',
				'email': '{$userEmail}',
				'login': '{$userEmail}',
				'name': '{$userName}'
			});
		} catch (e) {};
	</script>
	<!-- End KeKu Pro Widget -->
	";
}

function get_kekupro_config()
{
	$defaults = array(
	  'widgetId' => ''
	);

	$config = wp_parse_args(get_option('kekupro_settings'), $defaults);

	return $config;
}

function set_kekupro_config($config)
{
	update_option('kekupro_settings', $config);
}

function kekupro_admin_menu()
{
	add_options_page('KeKu Pro Settings', 'KeKu Pro Settings', 'administrator', __FILE__, 'kekupro_config_page');
}

function kekupro_config_page()
{
	$configUpdateRequested = !empty(sanitize_text_field($_POST['saveConfig']));
	$mainMessage = '';
	$subMessage = '';

	echo '<div class="wrap">';

	if ($configUpdateRequested
		&& check_admin_referer('update_config', 'update_config_nonce')
		&& current_user_can('administrator')
	) {
		$config = array(
			'widgetId' => (string)sanitize_key($_POST['widgetId'])
		);
		set_kekupro_config($config);
		$mainMessage = 'KeKu Pro settings were saved';
	}
	else
	{
		$config = get_kekupro_config();
	}

	/*if (empty($config['widgetId']))
	{
		$subMessage = 'Provide your Widget ID below</span>';
	}*/

	if (!empty($mainMessage) || !empty($subMessage))
	{
		echo '<div id="kekupro-saved" class="updated fade"><p><strong>' . $mainMessage . '</strong> ' . $subMessage . '</p></div>';
	}

	if (function_exists('wp_cache_clean_cache'))
	{
		global $file_prefix;
		wp_cache_clean_cache($file_prefix);
	}

	$pluginFolder = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), '', plugin_basename(__FILE__));

	echo '
	<style type="text/css">
		.kekupro-instructions {
			margin-bottom: 32px;
		}
		.kekupro-instructions-screenshot-block {
			display: inline-block;
			margin: 0 48px 24px 0;
			max-width: 300px;
			max-height: 300px;
		}
		.kekupro-instructions-screenshot-image {
			max-width: 300px;
			max-height: 300px;
			border-radius: 4px;
		}

		.kekupro-settings-form {
			background-color: #ffe;
			padding: 12px 24px 0 12px;
		}
			.kekupro-important-setting {
				font-size: 24px;
				margin: 0;
			}
			.kekupro-important-setting-input {
				font-size: 24px;
				max-width: 100%;
				width: 30%;
			}
			.kekupro-submit-settings-button {
				margin: 0;
				font-size: 24px;
				font-weight: bold;
			}
	</style>

	<h2>
		<img src="' . $pluginFolder . 'favicon.png" style="vertical-align: middle; width: 64px; height: 64px; border-radius: 4px; margin-right: 10px;"/>
		<a target="_blank" href="http://keku.pro/?utm_source=wordpress-plugin&utm_campaign=wodpress-plugin-settings-page">KeKu Pro</a>: Free calls from your website with analytics and call recordings
	</h2>';

	if (empty($config['widgetId'])) {
		echo '<div class="kekupro-instructions">
			<h2>How to get your widget ID</h2>

			<div class="kekupro-instructions-screenshot-block">
				<a href="https://keku.pro/dashboard/widgets?utm_source=wordpress-plugin&utm_campaign=wodpress-plugin-settings-page" target="_blank">
					<img src="' . $pluginFolder . 'setup-screenshots/01-signin.jpg" srcset="2x ' . $pluginFolder . '/setup-screenshots/01-signin@2x.jpg" class="kekupro-instructions-screenshot-image" />
				</a>
				<p>Go to <a href="https://keku.pro/dashboard/widgets?utm_source=wordpress-plugin&utm_campaign=wodpress-plugin-settings-page" target="_blank">keku.pro</a> and create a free account. If you have an account, sign in.</p>
			</div>

			<div class="kekupro-instructions-screenshot-block">
				<a href="https://keku.pro/dashboard/widgets?utm_source=wordpress-plugin&utm_campaign=wodpress-plugin-settings-page" target="_blank">
					<img src="' . $pluginFolder . 'setup-screenshots/02-widgets.jpg" srcset="2x ' . $pluginFolder . '/setup-screenshots/02-widgets@2x.jpg" class="kekupro-instructions-screenshot-image" />
				</a>
				<p>Go to <a href="https://keku.pro/dashboard/widgets?utm_source=wordpress-plugin&utm_campaign=wodpress-plugin-settings-page" target="_blank">Widgets Settings</a> and then tap on the widget.</p>
			</div>

			<div class="kekupro-instructions-screenshot-block">
				<img src="' . $pluginFolder . 'setup-screenshots/03-settings.jpg" srcset="2x ' . $pluginFolder . '/setup-screenshots/03-settings@2x.jpg" class="kekupro-instructions-screenshot-image" />
				<p>In the right pane, tap on “Copy Widget ID” button. <strong>Paste widget ID below.</strong></p>
			</div>
		</div>';
	}

	echo '<form method="post" action="" class="kekupro-settings-form'
		. (empty($config['widgetId']) ? " kekupro-settings-form-requires-additional-information" : "")
		. '">';

	if (function_exists('wp_nonce_field')) {
		wp_nonce_field('update_config', 'update_config_nonce');
	}

	echo (empty($config['widgetId']) ? '<h2>Set up your widget to work with Wordpress</h2>' : '')
		. '<p class="kekupro-important-setting">
			<label for="widgetId"><strong>KeKu Pro Widget ID: </strong></label>
			<input type="text" class="kekupro-important-setting-input" name="widgetId" id="widgetId" placeholder="(Required)"'
			. ' value="' . $config['widgetId'] . '"'
			. (current_user_can('administrator') ? '' : ' disabled="disabled"')
			. '/>
		</p>

		<div class="submit">
			<input type="submit" class="kekupro-submit-settings-button" name="saveConfig" id="saveConfig" value="Save"'
			. (current_user_can('administrator') ? '' : ' disabled="disabled"')
			. '/>
		</div>
	</form>
	';
}

function kekupro_settings_link($links)
{
	$settings_link = '<a href="options-general.php?page=kekupro/kekupro.php">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

function kekupro_activate()
{
	add_option('kekupro_settings', '', 'kekupro_settings', 'yes');
}

function kekupro_deactivate()
{
	delete_option('kekupro_settings');
}

function kekupro_admin_notices()
{
	global $hook_suffix;

	$config = get_kekupro_config();

	if ($hook_suffix == 'plugins.php' && empty($config['widgetId']))
	{
		echo "<div id='kekupro-cmp-warning' class='updated fade'><p><strong>Please finish <a href='options-general.php?page=kekupro/kekupro.php'>KeKu Pro plugin setup</a>.</strong></p></div>";
	}
}

register_activation_hook( __FILE__, 'kekupro_activate');
register_deactivation_hook( __FILE__, 'kekupro_deactivate');
add_action('admin_notices', 'kekupro_admin_notices');
?>
