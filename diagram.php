<?php
require('./models/player.php');
require('./models/config.php');
require('./models/result.php');

$config = Config::load_and_validate();

$players_file = fopen('./data/players.txt','r');
$players = Player::load();

$results = Result::load();

$black_result_table = array(1 => "W", 2 => "L", 3 => "D");
$white_result_table = array(1 => "L", 2 => "W", 3 => "D");

$player_results = array();
foreach($results as $result){
  $black_name = $result[0];
  $white_name = $result[1];
  $result_code = $result[2];

  if(array_key_exists($black_name,$player_results)){
    $player_results[$black_name][$white_name] = $black_result_table[$result_code];
  } else {
    $player_results[$black_name] = array($white_name => $black_result_table[$result_code]);
  }

  if(array_key_exists($white_name,$player_results)){
    $player_results[$white_name][$black_name] = $white_result_table[$result_code];
  } else {
    $player_results[$white_name] = array($black_name => $white_result_table[$result_code]);
  }
}

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
      } elseif(array_key_exists($left->name,$player_results) &&
               array_key_exists($right->name,$player_results[$left->name])){
        $result = $player_results[$left->name][$right->name];
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
  include('./templates/diagram_template.php');
}
