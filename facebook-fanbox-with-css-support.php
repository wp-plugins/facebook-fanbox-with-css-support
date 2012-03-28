<?php
/**
 * Plugin Name: Facebook Fanbox (with CSS Support)
 * Plugin URI: http://blog.ppfeufer.de/wordpress-plugin-facebook-fanbox-with-css-support/
 * Description: Add a sidebarwidget with a fully css-customisable facebook fanbox to your WordPress-Blog.
 * Version: 1.3.1
 * Author: H.-Peter Pfeufer
 * Author URI: http://ppfeufer.de
 */

define('FANBOX_CSS_FILE_DEFAULT', WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__), "", plugin_basename(__FILE__)) . 'css/facebook-fanbox.css');
define('FANBOX_CSS_FILE', WP_CONTENT_DIR . '/uploads/facebook-fanbox.css');
define('FANBOX_CSS_URI', WP_CONTENT_URL . '/uploads/facebook-fanbox.css');

class Facebook_Fanbox_With_CSS extends WP_Widget {
	private $var_sFlattrLink;

	/**
	 * Constructor
	 */
	function Facebook_Fanbox_With_CSS() {
		$this->var_sFlattrLink = 'http://flattr.com/thing/115342/WordPress-Plugin-Facebook-Fanbox-with-CSS-Support';

		if(function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('facebook-fanbox-with-css', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/l10n', dirname(plugin_basename(__FILE__)) . '/l10n');
		}

		if(ini_get('allow_url_fopen') || function_exists('curl_init')) {
			add_action('in_plugin_update_message-' . plugin_basename(__FILE__), array(
				$this,
				'facebook_fanbox_css_update_notice'
			));
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
		echo '<p><a href="' . $this->var_sFlattrLink . '" target="_blank"><img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></p>';
		echo '<p style="clear:both;"></p>';
	}

	/**
	 * Widget erstellen
	 * @param array $args
	 * @param array $instance
	 */
	function widget($args, $instance) {
		add_filter('language_attributes', 'facebook_fanbox_css_schema');
		extract($args);

		echo $before_widget;

		$title = (empty($instance['title'])) ? '' : apply_filters('widget_title', $instance['title']);

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
			$instance['css'] = strip_tags($var_sCssFanboxDefault);
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

	/**
	 * A little notice on pluginupdates ....
	 *
	 * @since 0.1
	 */
	function facebook_fanbox_css_update_notice() {
		$array_FBFBWCSS_Data = get_plugin_data(__FILE__);
		$var_sUserAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:5.0) Gecko/20100101 Firefox/5.0 WorPress Plugin Facebook Fanbox with CSS Support (Version: ' . $array_FBFBWCSS_Data['Version'] . ') running on: ' . get_bloginfo('url');
		$url = 'http://plugins.trac.wordpress.org/browser/facebook-fanbox-with-css-support/trunk/readme.txt?format=txt';
		$data = '';

		if(ini_get('allow_url_fopen')) {
			$data = file_get_contents($url);
		} else {
			if(function_exists('curl_init')) {
				$cUrl_Channel = curl_init();
				curl_setopt($cUrl_Channel, CURLOPT_URL, $url);
				curl_setopt($cUrl_Channel, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($cUrl_Channel, CURLOPT_USERAGENT, $var_sUserAgent);
				$data = curl_exec($cUrl_Channel);
				curl_close($cUrl_Channel);
			} // END if(function_exists('curl_init'))
		} // END if(ini_get('allow_url_fopen'))

		if($data) {
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote($array_FBFBWCSS_Data['Version']) . '\s*=|$)~Uis';

			if(preg_match($regexp, $data, $matches)) {
				$changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));

				echo '</div><div class="update-message" style="font-weight: normal;"><strong>What\'s new:</strong>';
				$ul = false;
				$version = 99;

				foreach($changelog as $index => $line) {
					if(version_compare($version, $array_FBFBWCSS_Data['Version'], ">")) {
						if(preg_match('~^\s*\*\s*~', $line)) {
							if(!$ul) {
								echo '<ul style="list-style: disc; margin-left: 20px;">';
								$ul = true;
							} // END if(!$ul)

							$line = preg_replace('~^\s*\*\s*~', '', $line);
							echo '<li>' . $line . '</li>';
						} else {
							if($ul) {
								echo '</ul>';
								$ul = false;
							} // END if($ul)

							$version = trim($line, " =");
							echo '<p style="margin: 5px 0;">' . htmlspecialchars($line) . '</p>';
						} // END if(preg_match('~^\s*\*\s*~', $line))
					} // END if(version_compare($version, TWOCLICK_SOCIALMEDIA_BUTTONS_VERSION,">"))
				} // END foreach($changelog as $index => $line)

				if($ul) {
					echo '</ul><div style="clear: left;"></div>';
				} // END if($ul)

				echo '</div>';
			} // END if(preg_match($regexp, $data, $matches))
		} // END if($data)
	} // END function update_notice()
}

/**
 * <head>-Tag erweitern.
 * Hierbei wird vorher geprüft, ob die "Erweiterung" schon
 * vorhanden ist, durch eventuelle andere Plugins.
 * Ich kann nur hoffen, dass andere Autoren das ebenfalls machen :-)
 */
if(!function_exists('facebook_fanbox_css_schema')) {
	function facebook_fanbox_css_schema($attr) {
		$var_sFacebookSchema = ' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://ogp.me/ns#" ';

		if(!strstr($attr, trim($var_sFacebookSchema))) {
			$attr .= $var_sFacebookSchema;
		}

		return $attr;
	}

	add_filter('language_attributes', 'facebook_fanbox_css_schema', 99);
}

/**
 * Footer erweitern.
 */
if(!is_admin()) {
	if(!function_exists('facebook_fanbox_with_css_footer')) {
		function facebook_fanbox_with_css_footer() {
			// Footer um Facebook-JavaScript erweitern.
			echo "\n" . '<!-- JS for Facebook Fanbox (with CSS Support) by H.-Peter Pfeufer [http://ppfeufer.de | http://blog.ppfeufer.de] -->' . "\n";
			echo '<script type="text/javascript">
					var fbRoot = document.getElementById(\'fb-root\');

					if (!fbRoot) {
						var body = document.getElementsByTagName(\'body\')[0];
						fbRoot = document.createElement(\'div\');
						fbRoot.id = \'fb-root\';
						body.appendChild(fbRoot);
					}

					var loadNewScript = true;
					var script = fbRoot.getElementsByTagName(\'script\');

					for (var i = 0, iMax = script.length; i < iMax; i++) {
						if (script[i].src === \'http://connect.facebook.net/' . get_locale() . '/all.js#xfbml=1\') {
							loadNewScript = false;
							break;
						}
					}

					if (loadNewScript) {
						var elm = document.createElement(\'script\');
						elm.src = \'http://connect.facebook.net/' . get_locale() . '/all.js#xfbml=1\';
						elm.type = \'text/javascript\';
						fbRoot.appendChild(elm);
					}
				</script>';
			echo "\n" . '<!-- END of JS for Facebook Fanbox (with CSS Support) -->' . "\n\n";
		}

		add_action('wp_footer', 'facebook_fanbox_with_css_footer', 99);
	}
}

/**
 * Widget initialisieren.
 */
add_action('widgets_init', create_function('', 'return register_widget("Facebook_Fanbox_With_CSS");'));
?>