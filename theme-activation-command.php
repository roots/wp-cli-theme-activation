<?php namespace Roots\WP_CLI\Commands;

if (!class_exists('\Theme_Command')) {
  return;
}

class Theme extends \Theme_Command {
  /**
   * Theme activation
   *
   * ## OPTIONS
   *
   * [<theme>]
   * : The theme name to activate. Defaults to 'sage'.
   *
   * [--show-on-front=<page-type>]
   * : What to show on the front page. Options are: 'posts', 'page'. Default is 'page'.
   *
   * [--permalink-structure=<permalink-string>]
   * : Permalink structure. Default is '/%postname%/'.
   *
   * [--skip-navigation]
   * : Skip creating default Primary Navigation.
   *
   * ## EXAMPLES
   *
   *     wp theme activation
   *     wp theme activation --show-on-front=page --permalink-structure='/%year%/%postname%/' --skip-navigation
   *
   * @subcommand roots-activate
   * @alias activation
   */
  public function roots_activate($args = [], $options = []) {
    list($theme) = $args + ['sage'];

    $defaults = [
      'permalink-structure' => '/%postname%/',
      'show-on-front'       => 'page',
      'skip-navigation'     => false
    ];

    $options = wp_parse_args($options, $defaults);
    
    $options['skip-navigation'] = ($options['skip-navigation'] || !!wp_get_nav_menu_object('Primary Navigation'));

    \WP_CLI::log('Activating theme and setting options');

    $home_page_options = [
      'post_content' => 'Lorem Ipsum',
      'post_status'  => 'publish',
      'post_title'   => 'Home',
      'post_type'    => 'page'
    ];
    
    parent::activate([$theme]);

    if ($home_page_id = wp_insert_post($home_page_options, false)) {
      \WP_CLI::run_command(['option', 'update', 'show_on_front', $options['show-on-front']]);
      \WP_CLI::run_command(['option', 'update', 'page_on_front', $home_page_id]);
    }

    if (!$options['skip-navigation'] && $menu_id = wp_create_nav_menu('Primary Navigation')) {
      $home_page_id && wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'     => $home_page_options['post_title'],
        'menu-item-object'    => $home_page_options['post_type'],
        'menu-item-object-id' => $home_page_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish'
      ]);
      set_theme_mod('nav_menu_locations', ['primary_navigation'=>$menu_id]);

      \WP_CLI::success('Primary Navigation created.');
    }
    
    \WP_CLI::run_command(['rewrite', 'structure', $options['permalink-structure']]);
    \WP_CLI::run_command(['rewrite', 'flush']);

    \WP_CLI::success('Theme activated');
  }
}

\WP_CLI::add_command('theme', __NAMESPACE__ . '\\Theme');
