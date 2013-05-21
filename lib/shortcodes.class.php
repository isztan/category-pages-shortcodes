<?php

if ( ! array_key_exists( 'swer-page2cat-shortcodes', $GLOBALS ) )
{ 
 class Page2catShortcodes extends Page2cat_Core {

  function showsingle( $atts ){
    global $post;
    $output = '';

    extract(
     shortcode_atts(
      array(
      'postid' => '',
      'pageid' => '',
      'showheader' => 'true',
      'header' => '2',
      'headerclass' => 'aptools-single-header page2cat-single-header',
      'content' => 'true',
      'contentclass' => 'aptools-content page2cat-content',
      'wrapper' => 'false',
      'wrapperclass' => 'aptools-wrapper page2cat-wrapper',
      ),
      $atts
     )
    );

   if ( $postid == $post->ID ) return;
   if ( $pageid == $post->ID ) return;
    # print_r( $atts); die();
   ob_start();

   if ( !empty( $postid ) && empty( $pageid ) ) :
    $output = self::shortcode_posts( $atts );
   elseif ( !empty( $pageid ) && empty( $postid ) ) :
    $output = self::shortcode_pages( $atts );
   else :
    $output = false;
   endif;

    _e( $output );
    $clean = ob_get_clean();
    return $clean;
  }


  function showlist( $atts ){
    extract(
     shortcode_atts(
      array(
        'catid' => '',
        'lenght' => '10',
        'listclass' => 'aptools-list page2cat-list',
        'header' => '2',
        'headerclass' => 'aptools-list-header page2cat-list-header',
        'excerpt' => 'false',
        'image' => 'false',
        'wrapper' => 'false',
        'wrapperclass' => 'aptools-list-wrapper page2cat-list-wrapper',
      ),
      $atts
     )
    );

   $hopen = '<h'.$header.' class='.$headerclass.'>';
   $hclose = '</h'.$header.'>';

   if ( $catid !== '' ) :
       $args = array( 'category__in' => array($catid), 'posts_per_page' => $lenght );
   endif;

   $page = new WP_Query( $args );
   if ( $page->have_posts() ):
    if ( $wrapper !== 'false' ){
        echo '<div class="'.$wrapperclass.'">';
    }
    echo '<ul class="'.$listclass.'">';
    while ( $page->have_posts() ):
     $page->the_post();
     echo '<li>';
     echo '<a href="'.get_permalink().'">'.get_the_title().'</a>'; 
     if ( $image !== 'false' && has_post_thumbnail() ){
       the_post_thumbnail( $image );
     }
     if ( $excerpt === 'true' ) echo ' <span>'.get_the_excerpt().'</span>';
     echo '</li>';
    endwhile;
    echo '</ul>';
    if ( $wrapper !== 'false' ){
        echo '</div>';
    }
   endif;
   wp_reset_postdata();                
  }

    
  function showauto(){
   global $cat;
   $query_args = array(
       'post_type' => 'page',
       'post_status' => 'publish',
       'meta_key' => 'page2cat_archive_link',
       'meta_value' => $cat,
       'posts_per_page' => 1,
   );

   $defaults = array(
      'showheader' => 'true',
      'header' => '2',
      'headerclass' => 'aptools-single-header page2cat-single-header',
      'content' => 'true',
      'contentclass' => 'aptools-content page2cat-content',
      'wrapper' => 'false',
      'wrapperclass' => 'aptools-wrapper page2cat-wrapper',
    );

   /*
   $pages = new WP_Query( $query_args );
   if ( $pages->have_posts() ):
    while ( $pages->have_posts() ):
        $pages->the_post();
        echo '<h2>'.get_the_title().'</h2>';
        echo '<div class="aptools-category-content page2cat-category-content">'.get_the_content().'</div>';
    endwhile;
   endif;
   wp_reset_postdata();                
   */

   ob_start();
   $output = self::shortcode_pages( $defaults, $query_args );
   _e( $output );
   $clean = ob_get_clean();
   return $clean;
  }
 }

 if ( class_exists( 'Page2catShortcodes' ) ):
  $page2cat_shortcodes = new Page2catShortcodes();
  $GLOBALS['swer-page2cat-shortcodes'] = $page2cat_shortcodes;
 endif;
}


add_shortcode( 'showsingle', array( 'Page2catShortcodes', 'showsingle' ) );
add_shortcode( 'showlist', array( 'Page2catShortcodes', 'showlist' ) );
add_shortcode( 'showauto', array( 'Page2catShortcodes', 'showauto' ) );


