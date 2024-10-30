<?php
/**
 * @package binglinks
 * @author Jose Miguel Parrella
 * @version 0.1
 */
/*
Plugin Name: Bing Links
Plugin URI: http://www.bureado.com/
Description: This plugin adds a widget with the Bing results for a search of your blog posts tags', using the Bing Search Library for PHP
Author: Jose Miguel Parrella
Version: 0.1
Author URI: http://www.bureado.com/
License: New BSD License
*/
/*
Copyright (c) 2010, Jose Miguel Parrella
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

* Neither the name of Microsoft Corporation nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 
*/

include "Msft/Exception.php";
include "Msft/Bing/Exception.php";
include "Msft/Bing/Search/Exception.php";
include "Msft/Bing/Search.php";
  
class BingLinks extends WP_Widget {
  
  function BingLinks() {
    $widget_ops = array('title' => 'Bing Search Results', 'count' => 3, 'description' => 'Bing search results for your posts' );
	$this->WP_Widget('pages', __('Bing Links'), $widget_ops);
  }
  
  function widget($args, $instance) {		
    extract( $args );
    $title = apply_filters('widget_title', $instance['title']);
    echo $before_widget;
    if ( $title )
      echo $before_title . $title . $after_title;
    $o = new Msft_Bing_Search('E871E73F9FDD7B3CF97D245A2F80C79227B62BD0');
    $t = get_tags();
    if( $t && is_array( $t ) ) {
      foreach( $t as $l ) {
        $q .= $l->name . ' ';
      }
      $o->setQuery($q)->setWebCount($instance['count'])->setSource('Web');
      $r = json_decode($o->search());
      echo "<ul>";
      foreach($r->SearchResponse->Web->Results as $v) {
        printf('<li><a href="%s">%s</a></li>',$v->Url,$v->Title);
      }
      echo "</ul>";
    }
    echo $after_widget;
  } // Widget function

  function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
    $instance['count'] = strip_tags($new_instance['count']);
    return $instance;
  } // Update function
  
  function form($instance) {
    $title = esc_attr($instance['title']); // Widget's title
    $count = esc_attr($instance['count']); // Widget's count of results
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
      <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Search Results:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" /></label>
    </p>
    <?php 
   } // Form function
  
} // BingLinks class

add_action('widgets_init', create_function('', 'return register_widget("BingLinks");'));
?>