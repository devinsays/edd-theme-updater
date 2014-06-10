<?php
/**
 * The main template file.
 *
 * This theme doesn't output anything.  It just show how EDD licensing works.
 *
 * @package WordPress
 * @subpackage EDD Sample Theme
 */
?>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title>EDD Sample Theme</title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>


	<article>
		<h1>EDD Sample Theme</h1>
		<p>Easy licensing for your WordPress Themes</p>
	</article>

<?php wp_footer(); ?>

</body>
</html>