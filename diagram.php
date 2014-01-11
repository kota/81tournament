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
      } elseif($left->is_place_holder()){
        $game[] = "[0,1]";
        $next_round_players[] = $right;
      } elseif($right->is_place_holder()){
        $game[] = "[1,0]";
        $next_round_players[] = $left;
      } elseif($result = $left->get_result($right)){
        switch($result->result_code){
          case 1:
          case 4: //black win
            if($result->black == $left){
              $game[] = "[1,0,'" . $result->kifu_id . "']";
              $next_round_players[] = $left;
            } else {
              $game[] = "[0,1,'" . $result->kifu_id . "']";
              $next_round_players[] = $right;
            }
            break;
          case 2:
          case 5: //white win
            if($result->white == $left){
              $game[] = "[1,0,'" . $result->kifu_id . "']";
              $next_round_players[] = $left;
            } else {
              $game[] = "[0,1,'" . $result->kifu_id . "']";
              $next_round_players[] = $right;
            }
            break;
          case 6:
          case 7: //both loss
            $game[] = "['d','d']";
            $next_round_players[] = Player::create_place_holder();
            break;
          case 3: //draw and rematch
          default: //other
            $next_round_players[] = null;
        }
      } else { #not played yet.
        $next_round_players[] = null;
      }
      $round[] = $game;
    }
    $rounds[] = $round;
  }

  //TODO replace $results with json (if possible)
  $results = "[";
  for($i=0;$i<count($rounds);$i++){
    $results .= "[";
      for($j=0;$j<count($rounds[$i]);$j++){
        if(count($rounds[$i][$j]) > 2){
          $results .= $rounds[$i][$j][2];
        } else {
          $results .= "[null]";
        } 
        if($j != count($rounds[$i])-1) $results .= ",";
      }
    $results .= "]";
    if($i != count($rounds)-1) $results .= ",";
  }
  $results .= "]";

  include('./templates/single_elimination_template.php');
} elseif($config->tournament_type == "group") {
  $player_points = array();
  for($i=0;$i<count($players);$i++){
    $player = $players[$i];
    $player->calculate_point();
    $player_points[$player->name] = $player->tournament_point * 1000 - $i;
  }
  arsort($player_points);
  $player_ranks = array_flip(array_keys($player_points));

  require_once('./models/util.php');
  include('./templates/group_template.php');
}
