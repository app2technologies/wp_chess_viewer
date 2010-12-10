<?php
/*
Plugin Name: WP Chess Viewer
Plugin URI: https://github.com/ashwinphatak/wp_chess_viewer
Description: WordPress plugin for embedding chess games in posts using PGN notation
Version: 1.0
Author: Ashwin Phatak
Author URI: http://app2technologies.com/
License: GPL2
*/

function wpcv_include_media()
{
?>

<script type="text/javascript" src="http://chesstempo.com/js/pgnyui.js"></script>
<script type="text/javascript" src="http://chesstempo.com/js/pgnviewer.js"></script>
<link type="text/css" rel="stylesheet" href="http://chesstempo.com/css/board-min.css"></link>

<?php
}

add_action('wp_head', 'wpcv_include_media');


function wpcv_handle_pgn($content)
{
  $start = strpos($content, '[begin-pgn]');
  if($start) {
    $end = strpos($content, '[end-pgn]');
    if($end) {
      $length = $end - $start;
      $pgn = substr($content, $start + strlen('[begin-pgn]'), $length - strlen('[end-pgn]') - 2);
      $pgn = trim($pgn);
      $pgn = str_replace("<br />", " ", $pgn);
      $pgn = str_replace("<p>", " ", $pgn);
      $pgn = str_replace("</p>", " ", $pgn);
      $pgn = str_replace("\n", " ", $pgn);
      $pgn = str_replace("\r", " ", $pgn);
      $pgn = str_replace("\t", " ", $pgn);
      $pgn = str_replace("'", "\\'", $pgn);
      
      $script = "<script>
        new PgnViewer(
          { boardName: 'game',
            pieceSet: 'merida',
            pieceSize: 35,
            movesFormat: 'default',
            pgnString: '$pgn'
          }
        );
        </script>
        <div id='game-container'></div>
        <div id='game-moves'></div>
        ";

      $content = substr_replace($content, $script, $start, $length + strlen('[end-pgn]'));
    }
  }
  
  return $content;
}

add_filter('the_content', 'wpcv_handle_pgn');

?>