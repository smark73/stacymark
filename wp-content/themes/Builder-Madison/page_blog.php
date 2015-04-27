<?php

/*
Template Name: Blog Template

Runs a standard blog query and then loads the index.php template file
to render the blog view.
*/

global $more;
$more = 0;

$page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
query_posts( "paged=$page" );

locate_template( array( 'index.php' ), true );
