content-2012.mozillafestival.org
================================

The wp-content directory for an update to the Mozilla Festival website (http://mozillafestival.org) so that everything looks lovely, fresh and clean for 2012.

Using locally
--------------

This is just the theme directory for the Mozilla Festival website (minus the uploads) so if you want to help us out by fixing bugs or adding features you first want to set up a [local wordpress install](http://codex.wordpress.org/WordPress_Installation_Techniques).

Once you've got that up and running check out the theme in a separate location, alongside your local wordpress and set up a local VirtualHost pointing to the `content-2012.mozillafestival.org/wp-content`. Below is a stripped down version of what I use on my mac (running a local apache webserver).

```
<VirtualHost *:80>
    DocumentRoot "/Users/rossilla/Sites/dev-content-2012.mozillafestival.org/wp-content"
    ServerName dev-content-2012.mozillafestival.org
</VirtualHost>
```

The final thing to do is add the following two lines to your local wp-config.php, this tells your local WordPress install where to look for the wp-content directory. 

```
define('WP_CONTENT_DIR', '/Users/rossilla/Sites/dev-content-2012.mozillafestival.org/wp-content');
define('WP_CONTENT_URL', 'http://dev-content-2012.mozillafestival.org');
```