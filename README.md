# EDD Theme Updater

This is an example of how to implement one-click theme updates using Easy Digital Downloads and the Software Licensing plugin.

## Set Up Instructions

* Copy the "updater" folder into your theme.
* Copy the code in functions.php into your own theme to load the class
* Modify "updater/theme-updater.php" with your theme and shop information
* Modify the textdomain in "updater/theme-updater.php" to match your theme

## Parameters

These are the items that will need to be configured in "updater/theme-updater.php":

```PHP
'remote_api_url'=> 'https://easydigitaldownloads.com', // Site where EDD is hosted
'item_name' => 'Theme Name', // Name of theme
'theme_slug' => 'theme-slug', // Theme slug
'version' => '1.0.0', // The current version of this theme
'author' => 'Easy Digital Downloads', // The author of this theme
'download_id' => '', // Optional, used for generating a license renewal link
'renew_url' => '' // Optional, allows for a custom license renewal link
```