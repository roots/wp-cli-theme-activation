<?php

namespace Roots\ThemeActivation;

if (!defined('\WP_CLI')) {
  return;
}

class RootsThemeActivationCommand extends \WP_CLI_Command {
  /**
   * Theme activation options
   *
   * ## OPTIONS
   *
   * <theme>
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
   *     wp theme-activation options
   *     wp theme-activation options --show_on_front=page --permalink_structure='/%year%/%postname%/' --skip_navigation
   *
   */
  public function options($args, $options) {
    list($theme) = $args;

    $defaults = [
      'permalink_structure' => '/%postname%/',
      'show_on_front'       => 'page',
      'skip_navigation'     => null,
      'theme'               => 'sage'
    ];

    $options = wp_parse_args($options, $defaults);

    \WP_CLI::log('Activating theme and setting options');

    $home_page_options = [
      'post_content' => 'Lorem Ipsum',
      'post_status'  => 'publish',
      'post_title'   => 'Home',
      'post_type'    => 'page',
      'porcelain'    => true
    ];

    \WP_CLI::run_command(['theme', 'activate', $options['theme']]);

    if ($home_page_id = wp_insert_post($home_page_options, false)) {
      \WP_CLI::run_command(['option', 'update', 'show_on_front', $options['show_on_front']]);
      \WP_CLI::run_command(['option', 'update', 'page_on_front', $home_page_id]);
    }

    \WP_CLI::run_command(['rewrite', 'structure', $options['permalink_structure']]);
    \WP_CLI::run_command(['rewrite', 'flush']);

    if (!empty($options['skip_navigation'])) {
      \WP_CLI::run_command(['menu', 'create', 'Primary Navigation']);
      \WP_CLI::run_command(['menu', 'location', 'assign', 'primary_navigation']);
      \WP_CLI::run_command(['menu', 'item', 'add-post', 'Primary Navigation', $home_page_id]);
    }

    \WP_CLI::success('Theme activated');
  }
}

\WP_CLI::add_command('theme-activation', new RootsThemeActivationCommand);
