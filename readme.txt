=== EM Custom Login ===
Contributors: erikdmitchell
Donate link: erikdmitchell@gmail.com
Tags: custom login, login, register, password, admin, customization, error, login error
Requires at least: 4.0
Tested up to: 4.7.1
Stable tag: 0.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables a completely customizable WordPress login, registration and password form.

== Description ==

Provides users with a completely customizable login page, registration page and a password reset/update page.

In addition, an admin section allows users to perform various tasks such as customized emails.

== Installation ==

1. Upload `em-custom-login` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The plugin will generate all the pages with the proper shortcode.

== Frequently Asked Questions ==

= Do I need to use the built in pages?  =

No. The plugin automatically creates pages for you, but in the settings area, you can use custom pages.
Each page needs a specific shortcode, which is provided below the option to change page.

= What shortcodes should I use? =

A detailed list of shortcodes and their functionality are coming soon.

= How do I use my own templates? =

1. Create a "em-custom-login" folder in your active theme.
2. Copy the templates from plugins/em-custom-login/templates into themes/{active-theme}/em-custom-login/.
3. Edit the files in your theme as needed.

= How do I add custom registration fields? =

This functionality is coming in our next release.

== Screenshots ==

1. The (admin) settings panel.

More soon.

== Hooks and Filters ==

Coming soon.

== Changelog ==

= 0.1.5 =

* The plugin activation function has been cleaned up.
* Properly enqueued the admin.js file.
* Added front end styles.

= 0.1.4 =

* Fixed "Retrieve Password Email" label duplication on admin page.
* Fixed issue where leaving the reCaptcha setting unchecked caused an error on the front end.

* Added setting for a custom logout redirect.

* Template system tweaked so you no longer need the templates sub folder. Files can go in {active-theme}/em-custom-login/

= 0.1.3 =

* Added index.php and a security check for the main plugin file.
* Added LICENSE.txt
* Added a template functionality for our shortcodes. See FAQ.
* Cleaned up all files for translation support

= 0.1.2 =

* Added functionality to disable admin bar for non administrators.
* Added ability to give users a specific page after registration (set in admin panel).
* Added reCaptcha integration on registration page.
* Added ability to hide admin bar from users.
* Added tow hooks: emcl_after_user_registration and emcl_registraion_before_recaptcha. More details soon.

= 0.1.1 =

* Created a new workflow that sets login functions to the WordPress default if there's no set page.
* Added ability to give users a specific page after login (set in admin panel).

= 0.1.0 =

* Initial version.

== Upgrade Notice ==

None yet.
