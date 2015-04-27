Extensions are like mini-themes for Builder in that they can have style.css and functions.php files. They can be applied to a specific Layout or to a specific View.

All functions in Extensions should be unique as all Extensions' functions.php files are loaded on the back-end of WordPress to allow the Extension to offer editors to customize their settings. If two extensions have a function with the same name, then WordPress will crash as PHP fails when a duplicate function definition is encountered. For this reason, it is recommended to always prefix all Extension functions with "builder_extension_NAME_" where "NAME" is the name of the Extension that is composed of only letters, underscores, and numbers. For example, an Extension named "Compact Posts" would have all of its function names start with "builder_extension_compact_posts_" It does make the function names much longer but it also avoids potential issues that could crash a site.

Just as themes have a comment section at the top to list details about the theme, Extensions can have a set of fields in their style.css file to add information about the Extension. Currently, the following fields are supported: "Name", "Description", and "Disable Theme Style".

Name is just the name of the Extension. If one is not provided, it will be generated from the directory name of the Extension. Thus, an Extension in a directory named "no_dates" would have a name of "No Dates".

Description is some descriptive information about the Extension that will show when a user selects the Extension for use.

Disable Theme Style indicates whether or not the Extension's style.css should override the one provided by the active theme / child theme. If it is set to "no", left blank, or is not in the style.css file, the theme's / child theme's style.css file will be used as normal. If it is set to anything else, any Layout or View that uses the extension will have the theme's / child theme's style.css disabled and the bulk of the styling will come directly from the Extension's style.css file. The presence of this field for a specific Extension is only look for when a Layout or View using that Extension is saved. So, if you have an existing Extension that does not have this feature enabled that is in use and you enable this feature, you will have to go to each Layout and View using the Extension and save them again. This is done to improve performance as the file does not have to be inspected on each page load.

Here is an example header you might find in an Extension's style.css file:


<?php
/*
Name: Example Extension
Description: This Extension shows how an Extension can be used.
Disable Theme Style: no
*/

/* CSS */


Builder provides a number of Extensions, but the selection can be expanded by adding extensions to your child theme by creating a directory in your child theme called "extensions" and putting the desired Extensions in that directory. If your active child theme has an extension with the same directory name as one that exists in Builder, the child theme one will replace the default one provided by Builder. This way child themes can provide their own customizations to the default set provided by Builder.

If you are using an Extension provided by the child theme that don't exist in Builder and you switch to a child theme that does not have an Extension with the same directory name, then any Layouts or Views that use that Extension will behave as if no Extension were selected.
