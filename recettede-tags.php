<?php
/*
Plugin Name: Tags recettes.de
Description: A plugin to create tags for http://recettes.de/cuisine
Version: 0.1
License: GPL2
*/
/*
Copyright 2011 Isabelle Hurbain-Palatin  (email : isabelle@palatin.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action('add_meta_boxes', 'add_recettes_box');
add_action('save_post', 'save_recettes_tags');
add_action('rss2_item', 'add_recettes_tags_rss');

function add_recettes_tags_rss(){
  $recettetags = get_post_meta(get_the_ID(), 'recettes-de-tags', true );
  if (!empty($recettetags)){
    $tagsarr = explode(",", $recettetags);
    foreach($tagsarr as $tagrec){
      $tagrecclean = trim($tagrec);
      $reclink = str_replace(" ", "-", $tagrecclean);
      echo "<category domain='recettede'><![CDATA[<a href='http://recettes.de/" . $reclink . "'>" . $tagrecclean. "</a>]]></category>";
    }
    echo $after;
  }
}

function add_recettes_box (){
  add_meta_box('recettemeta', "Tags 'recette.de/cuisine'", 'recettes_box_display', 'post');
}

function recettes_box_display($post) {
  wp_nonce_field(plugin_basename(__FILE__), 'recettes_de_noncename');
  echo '<label for="tags_recette_field">Tags Recettes.de/cuisine, s&eacute;par&eacute;s par des virgules</label>';
  $mytags = get_post_meta($post->ID, 'recettes-de-tags', true);
  echo '<input type="text" id="tags_recette_field" name="tags_recette_field" size="70" value="' . $mytags . '">';
}

function save_recettes_tags($post_id){
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;

  if(!wp_verify_nonce($_POST['recettes_de_noncename'], plugin_basename(__FILE__)))
    return;

  if ( !current_user_can( 'edit_post', $post_id ) )
    return;

  $mytags = $_POST['tags_recette_field'];
  $mytags = esc_html(wp_kses($mytags));

  add_post_meta($post_id, 'recettes-de-tags', $mytags, true) or update_post_meta($post_id, 'recettes-de-tags', $mytags);
}

function the_recettes_tags($before, $after) {
  $recettetags = get_post_meta(get_the_ID(), 'recettes-de-tags', true );
  if (!empty($recettetags)){
    echo $before;
    $tagsarr = explode(",", $recettetags);
    foreach($tagsarr as $tagrec){
      $tagrecclean = trim($tagrec);
      $reclink = str_replace(" ", "-", $tagrecclean);
      echo "<a href='http://recettes.de/" . $reclink . "'>" . $tagrecclean. "</a> ";
    }
    echo $after;
  }

}
?>
