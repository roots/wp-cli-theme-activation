<?php

namespace Roots\ThemeActivation;

if (!defined('WP_CLI')) {
  return;
}

class RootsThemeActivationCommand extends \WP_CLI_Command {
  /**
   * Roots Theme Activation options
   *
   * ## OPTIONS
   *
   * <theme>
   * : The theme name to activate. Defaults to 'roots'.
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
   *     wp roots options
   *     wp roots options --show_on_front=page --permalink_structure='/%year%/%postname%/' --skip_navigation
   *
   */
  public function options($args, $options) {
    list($name) = $args;

    $defaults = array(
      'permalink_structure' => '/%postname%/',
      'show_on_front'       => 'page',
      'skip_navigation'     => null,
      'theme'               => 'roots'
    );

    $options = wp_parse_args($options, $defaults);

    WP_CLI::log('Activating theme and setting options');

    $home_page_options = array(
      'post_content' => 'Lorem Ipsum',
      'post_status'  => 'publish',
      'post_title'   => 'Home',
      'post_type'    => 'page',
      'porcelain'    => true
    );

    WP_CLI::run_command(array('theme', 'activate', $option['theme']));

    $home_page_id = WP_CLI::run_command(array('post', 'create'), $home_page_options);

    WP_CLI::run_command(array('option', 'update', 'show_on_front', $option['show_on_front']));
    WP_CLI::run_command(array('option', 'update', 'page_on_front', $home_page_id));

    WP_CLI::run_command(array('rewrite', 'structure', $option['permalink_structure']));
    WP_CLI::run_command(array('rewrite', 'flush'));

    if (!empty($option['skip_navigation'])) {
      WP_CLI::run_command(array('menu', 'create', 'Primary Navigation'));
      WP_CLI::run_command(array('menu', 'location', 'assign', 'primary_navigation'));
      WP_CLI::run_command(array('menu', 'item', 'add-post', 'Primary Navigation', $home_page_id));
    }

    WP_CLI::success('Theme activated');
  }
}

\WP_CLI::add_command('roots', new RootsThemeActivationCommand);
