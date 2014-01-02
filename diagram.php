<?php
require_once('./models/player.php');
require_once('./models/config.php');

$config = Config::load_and_validate();
$players = Player::load(true);

if($config->tournament_type == "single_elimination"){
  $rounds = array();
  $num_rounds = log(count($players),2);
  $next_round_players = $players;
  for($i=0;$i<$num_rounds;$i++){
    $round_players = $next_round_players;
    $next_round_players = array();
    $round = array();
    for($j=0;$j<count($round_players)/2;$j++){
      $left = $round_players[$j*2];
      $right = $round_players[($j*2)+1];
  
      $game = array($left,$right);
      if(!$left || !$right){
        $next_round_players[] = null;
      } elseif($result = $left->get_result($right)){
        if($result == "W"){
          $game[] = "[1,0]";
          $next_round_players[] = $left;
        } elseif($result == "L"){
          $game[] = "[0,1]";
          $next_round_players[] = $right;
        }
      } else { #not played yet.
        $next_round_players[] = null;
      }
      $round[] = $game;
    }
    $rounds[] = $round;
  }
  include('./templates/single_elimination_template.php');
} else {
  include('./templates/group_template.php');
}
