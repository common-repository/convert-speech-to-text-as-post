<?php 
/*
* Plugin Name: Easy Speech 2 Text
* Plugin URI: http://mdsplugin.netau.net/wordpress/
* Description: The most complete speech to text converter as a post. Convert audio files to text with upload option and make it as a post.
* Author: Muneeb Ali
* Author URI: http://mdsplugin.netau.net/wordpress/
* Version: 1.0.0
* License: GPL2

Easy Speech 2 Text is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Easy Speech 2 Text is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Easy Speech 2 Text If not, see <http://www.gnu.org/licenses/>.
*/

if (defined('EASYSPEECH2TEXT_PLUGIN_ACTIVE')) return;
define('EASYSPEECH2TEXT_PLUGIN_ACTIVE', true);
define('EASYSPEECH2TEXT_PLUGIN_DIR', dirname(__FILE__));
define('EASYSPEECH2TEXT_PLUGIN_URL', plugins_url( null, __FILE__ )); 
include( plugin_dir_path( __FILE__ ) . 'speech2text.php');
add_action("plugins_loaded", "easyspeech2text_addmetabox"); 
function easyspeech2text_addmetabox()
{
    add_action( 'add_meta_boxes', 'easyspeech2text_editmetabox' );
}
function easyspeech2text_editmetabox()
{
        add_meta_box( 'Easy-Speech-to-text', __( 'Easy Speech to Text','easy-speech-to-text'), 'easyspeech2text_convertpost', 'post', 'normal', 'high' );
}
?>