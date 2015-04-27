To make use of this plugin feature, create a plugin-features/pods directory in the child theme. The features in the directory will only run/load when the Pods plugin is active.

There are three files that are automatically recognized by Builder's Plugin Features (all of them are optional):

* init.php - If this file is in the directory, it is loaded on the dashboard and on the front-end of the site. You can use a check for is_admin to conditionally run code on just the dashboard or just the front-end.
* style.css - If this CSS file is in the directory, the style is enqueued on the front-end of the site.
* script.js - If this Javascript file is in the directory, the script is enqueued on the front-end of the site.

If you wish to add CSS or Javascript files on the dashboard, you will need to use the init.php file to properly enqueue these (ensure that you don't enqueue them globally, use conditionals smartly to ensure that they are enqueued only where needed).

If any of these files are in both Builder core and the active child theme, then the child theme's version will override Builder core's version. So, if plugin-features/pods/init.php is present in both Builder core and the child theme, only the one from the child theme will run.
