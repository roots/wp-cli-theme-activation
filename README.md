## Installation

The quick and dirty method is to drop [theme-activation-command.php](https://raw.githubusercontent.com/roots/wp-cli-theme-activation/master/theme-activation-command.php) into your `/wp-content/mu-plugins/` directory.

For other methods, please refer to WP-CLI's [Community Packages](https://github.com/wp-cli/wp-cli/wiki/Community-Packages) wiki.

## Usage

Running the command without any options will activate [Sage](https://github.com/roots/sage) with all of the default options.


### `wp theme activation`

1. Activates Sage
2. Creates page called Home and sets as [static front page](http://codex.wordpress.org/Creating_a_Static_Front_Page)
3. Creates a new menu called Primary Navigation and adds newly created home page to it


### Options

#### `[<theme>]`
The theme name to activate. **Default: 'sage'**

#### `[--show-on-front=<page-type>]`
What to show on the front page. Options are: 'posts', 'page'. **Default: 'page'**

#### `[--permalink-structure=<permalink-string>]`
Permalink structure. **Default: '/%postname%/'**

#### `[--skip-navigation]`
Skips creating default Primary Navigation.
  
### Examples
```
wp theme activation
wp theme activation --show-on-front=page --permalink-structure='/%year%/%postname%/' --skip-navigation
```
