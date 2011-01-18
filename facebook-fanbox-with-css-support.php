<?php
/**
 * Plugin Name: Facebook Fanbox (with CSS Support)
 * Plugin URI: http://blog.ppfeufer.de/wordpress-plugin-facebook-fanbox-with-css-support/
 * Description: Add a sidebarwidget with a fully css-customisable facebook fanbox to your WordPress-Blog.
 * Version: 1.1.1
 * Author: H.-Peter Pfeufer
 * Author URI: http://ppfeufer.de
 */

/**
 * Changelog
 * = 1.1.1 (18.01.2011) =
 * Fix: fixed errormessage on firtst activation (thanks to <a href="http://bloggonaut.net/">Jonas</a> for reporting).
 *
 * = 1.1.0 (17.01.2011) =
 * Fix: Moved CSS to upload-dir, so its not effected on upadtes. **Please make sure to control and save your settings after this update**
 * Update: German translation
 *
 * = 1.0.1 (12.01.2011) =
 * Fix: Setting the current locale (<em>must be defined in wp-config.php</em>)
 *
 * = 1.0.0 (11.01.2011) =
 * Initial Release.
 * Added widget-settings.
 */

if(!defined('PPFEUFER_FLATTRSCRIPT')) {
	define('PPFEUFER_FLATTRSCRIPT', 'http://cdn.ppfeufer.de/js/flattr/flattr.js');
}
define('FACEBOOK_FANBOX_WITH_CSS_VERSION', '1.1.1');
define('FANBOX_CSS_FILE_DEFAULT', WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__), "", plugin_basename(__FILE__)) . 'css/facebook-fanbox.css');
define('FANBOX_CSS_FILE', WP_CONTENT_DIR . '/uploads/facebook-fanbox.css');
define('FANBOX_CSS_URI', WP_CONTENT_URL . '/uploads/facebook-fanbox.css');

class Facebook_Fanbox_With_CSS extends WP_Widget {
	/**
	 * Constructor
	 */
	function Facebook_Fanbox_With_CSS() {
		if(function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('facebook-fanbox-with-css', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/l10n', dirname(plugin_basename(__FILE__)) . '/l10n');
		}

		$widget_ops = array(
			'classname' => 'facebook_fanbox_with_css',
			'description' => __('Add a Facebook Fanbox to your sidebar wich is fully customisable via CSS.', 'facebook-fanbox-with-css')
		);

		$control_ops = array(
			'width' => 400
		);

		$this->WP_Widget('facebook_fanbox_with_css', __('Facebook Fanbox (with CSS)', 'facebook-fanbox-with-css'), $widget_ops, $control_ops);
	}

	/**
	 * Widgetformular erstellen
	 * @param array $instance
	 */
	function form($instance) {
		$var_bCssErrorDifference = false;

		$instance = wp_parse_args((array) $instance, array(
			'title' => '',
			'facebook-id' => '',
			'css' => @file_get_contents(FANBOX_CSS_FILE_DEFAULT),
			'css-timestamp' => ',',
			'number-of-connections' => '12',
			'width' => '220',
			'height' => '300'
		));

		$stream = ($instance['stream'] == 'true') ? ' checked="checked"' : '';
		$logobar = ($instance['logobar'] == 'true') ? ' checked="checked"' : '';

		// CSS prüfen
		$var_sFanboxCssDatabaseContent = $instance['css'];
		$var_sFanboxCssFileContent = @file_get_contents(FANBOX_CSS_FILE);
		if(!$var_sFanboxCssFileContent) {
			// CSS Datei existiert nicht, also wird sie erstellt
			$this->facebook_fanbox_css_update($var_sFanboxCssDatabaseContent);
		} else {
			if ($var_sFanboxCssFileContent != $var_sFanboxCssDatabaseContent) {
				$var_bCssErrorDifference = true;
			}
		}

		// Name and Version (hidden field)
		echo '<input id="' . $this->get_field_id('plugin-name') . '" name="' . $this->get_field_name('plugin-name') . '" type="hidden" value="Facebook Fanbox (with CSS Support)" />';
		echo '<input id="' . $this->get_field_id('plugin-version') . '" name="' . $this->get_field_name('plugin-version') . '" type="hidden" value="' . FACEBOOK_FANBOX_WITH_CSS_VERSION . '" />';

		// Title
		echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . __('Title', 'facebook-fanbox-with-css') . ':</strong></p>';
		echo '<p><span style="display:inline-block; width:150px;">' . __('Title', 'facebook-fanbox-with-css') . ': </span><input id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . strip_tags($instance['title']) . '" /></p>';
		echo '<p style="clear:both;"></p>';

		// Settings
		echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . __('Settings:', 'facebook-fanbox-with-css') . '</strong></p>';
		echo '<p><span style="display:inline-block; width:150px;">' . __('Fanpage-ID', 'facebook-fanbox-with-css') . ': </span><input id="' . $this->get_field_id('facebook-id') . '" name="' . $this->get_field_name('facebook-id') . '" type="text" value="' . strip_tags($instance['facebook-id']) . '" /></p>';
		echo '<p><span style="display:inline-block; width:150px;">' . __('Friends to show', 'facebook-fanbox-with-css') . ': </span><input id="' . $this->get_field_id('number-of-connections') . '" name="' . $this->get_field_name('number-of-connections') . '" type="text" value="' . strip_tags($instance['number-of-connections']) . '" /></p>';
		echo '<p><input class="checkbox" type="checkbox" ' . $stream . ' id="' . $this->get_field_id('stream') . '" name="' . $this->get_field_name('stream') . '" onchange="wpWidgets.save(jQuery(this).closest(\'div.widget\'),0,1,0);" /> <span style="display:inline-block;">' . __('Show Facebook-Stream', 'facebook-fanbox-with-css') . '</span></p>';
		if($stream != '') {
			echo '<p style="background-color:#ff0; padding:5px;">' . __('Needs ~ 300px more height !', 'facebook-fanbox-with-css') . '</p>';
		}
		echo '<p><input class="checkbox" type="checkbox" ' . $logobar . ' id="' . $this->get_field_id('logobar') . '" name="' . $this->get_field_name('logobar') . '" onchange="wpWidgets.save(jQuery(this).closest(\'div.widget\'),0,1,0);" /> <span style="display:inline-block;">' . __('Show Logobar', 'facebook-fanbox-with-css') . '</span></p>';
		if($logobar != '') {
			echo '<p style="background-color:#ff0; padding:5px;">' . __('Needs ~ 30px more height !', 'facebook-fanbox-with-css') . '</p>';
		}
		echo '<p><span style="display:inline-block; width:150px;">' . __('Width', 'facebook-fanbox-with-css') . ': </span><input id="' . $this->get_field_id('width') . '" name="' . $this->get_field_name('width') . '" type="text" value="' . strip_tags($instance['width']) . '" /></p>';
		echo '<p><span style="display:inline-block; width:150px;">' . __('Height', 'facebook-fanbox-with-css') . ': </span><input id="' . $this->get_field_id('height') . '" name="' . $this->get_field_name('height') . '" type="text" value="' . strip_tags($instance['height']) . '" /></p>';
		echo '<p style="clear:both;"></p>';

		// Own CSS
		echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . __('Custom CSS:', 'facebook-fanbox-with-css') . '</strong></p>';
		if ($var_bCssErrorDifference == true) {
			echo '<p style="background-color:#ff0; padding:5px;">' . __('There is a difference between the css-file and the saved css in database <em>(The database is your leading css)</em>. Please check your css here and save to clear this issue and update the css-file.', 'facebook-fanbox-with-css') . '</p>';
		}
		echo '<p><span style="display:inline-block;">' . __('Write your CSS here ...', 'facebook-fanbox-with-css') . '</span><textarea style="width:100%;' . $var_sReadOnlyStyle . '" id="' . $this->get_field_id('css') . '" rows="10" name="' . $this->get_field_name('css') . '">' . $instance['css'] . '</textarea></p>';
		echo '<p><input class="checkbox" type="checkbox" ' . $css_reset . ' id="' . $this->get_field_id('css-reset') . '" name="' . $this->get_field_name('css-reset') . '" onchange="wpWidgets.save(jQuery(this).closest(\'div.widget\'),0,1,0);" /> <span style="display:inline-block;">' . __('Reset to default css', 'facebook-fanbox-with-css') . '</span></p>';
		echo '<p style="clear:both;"></p>';

		// Flattr
		echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . __('Like this Plugin? Support the developer.', 'facebook-fanbox-with-css') . '</strong></p>';
		/**
		 * JavaScript für Flattr einfügen
		 */
		if(!defined('PPFEUFER_FLATTRSCRIPT_IS_LOADED')) {
			echo '<script type="text/javascript" src="' . PPFEUFER_FLATTRSCRIPT . '"></script>';
			define('PPFEUFER_FLATTRSCRIPT_IS_LOADED', true);
		}
		echo '<p><a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://blog.ppfeufer.de/wordpress-plugin-facebook-fanbox-with-css-support/"></a></p>';
		echo '<p style="clear:both;"></p>';
	}

	/**
	 * Widget erstellen
	 * @param array $args
	 * @param array $instance
	 */
	function widget($args, $instance) {
		extract($args);

		echo $before_widget;

		$title = (empty($instance['title'])) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);

		if(!empty($title)) {
			echo $before_title . $title . $after_title;
		}

		echo $this->facebook_fanbox_with_css_output($instance, 'widget');
		echo $after_widget;
	}

	/**
	 * Optionen updaten
	 * @param array $new_instance
	 * @param array $old_instance
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$new_instance = wp_parse_args((array) $new_instance, array(
			'plugin-name' => 'Facebook Fanbox (with CSS Support)',
			'plugin-version' => FACEBOOK_FANBOX_WITH_CSS_VERSION,
			'title' => '',
			'facebook-id' => '',
			'css' => '',
			'css-timestamp' => time(),
			'number-of-connections' => '12',
			'width' => '220',
			'height' => '300'
		));

		$instance['plugin-name'] = $new_instance['plugin-name'];
		$instance['plugin-version'] = $new_instance['plugin-version'];
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['facebook-id'] = strip_tags($new_instance['facebook-id']);
		$instance['css-timestamp'] = strip_tags($new_instance['css-timestamp']);
		$instance['number-of-connections'] = strip_tags($new_instance['number-of-connections']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['stream'] = $new_instance['stream'] ? 'true' : 'false';
		$instance['logobar'] = $new_instance['logobar'] ? 'true' : 'false';

		// CSS-Datei neu schreiben, sofern diese schreibbar ist
		if($new_instance['css-reset'] == true) {
			$var_sCssFanboxDefault = @file_get_contents(FANBOX_CSS_FILE_DEFAULT);
			$instance['css'] = strip_Ctags($var_sCssFanboxDefault);
			$this->facebook_fanbox_css_update($var_sCssFanboxDefault);
		} else {
			$instance['css'] = strip_tags($new_instance['css']);
			$this->facebook_fanbox_css_update($instance['css']);
		}

		return $instance;
	}

	/**
	 * Widget im Frontend ausgeben
	 * @param array $args
	 * @param string $position
	 */
	function facebook_fanbox_with_css_output($args = array(), $position) {
		?>
		<fb:fan profile_id="<?php echo $args['facebook-id'] ?>"
			css="<?php echo FANBOX_CSS_URI ?>?<?php echo $args['css-timestamp'] ?>"
			connections="<?php echo $args['number-of-connections'] ?>"
			stream="<?php echo $args['stream'] ?>"
			logobar="<?php echo $args['logobar'] ?>"
			width="<?php echo $args['width'] ?>px;"
			height="<?php echo $args['height'] ?>px;">
		</fb:fan>
		<?php
	}

	/**
	 * CSS-Datei Update
	 */
	function facebook_fanbox_css_update($var_sCss) {
		@file_put_contents(FANBOX_CSS_FILE, $var_sCss);

		return;
	}
}

add_action('widgets_init', create_function('', 'return register_widget("Facebook_Fanbox_With_CSS");'));

/**
 * JavaScript einbinden
 */
if(!is_admin()) {
	// Footer um JavaScript erweitern
	function facebook_fanbox_with_css_head() {
		echo "\n" . '<!-- JS for Facebook Fanbox (with CSS Support) by H.-Peter Pfeufer [http://ppfeufer.de | http://blog.ppfeufer.de] -->' . "\n" . '<script src="http://connect.facebook.net/' . get_locale() . '/all.js#xfbml=1"></script>' . "\n" . '<!-- END of JS for Facebook Fanbox (with CSS Support) -->' . "\n\n";
	}

	add_action('wp_footer', 'facebook_fanbox_with_css_head');
}
?>