<?php
class Result{
  private static $file_path = './data/result.txt';
  public $result_code;
  public $black;
  public $white;
  public $kifu_id;

  public function __construct($result_code,$black,$white,$kifu_id) {
    $this->result_code = (int)$result_code;
    $this->black = $black;
    $this->white = $white;
    $this->kifu_id = $kifu_id;
  }

  public function result_label_for($player){
    if($player->name == $this->black->name){
      $black_result_table = array(1 => "W", 2 => "L", 3 => "D", 4 => "W", 5 => "L", 6 => "D");
      return $black_result_table[$this->result_code];
    } elseif($player->name == $this->white->name) {
      $white_result_table = array(1 => "L", 2 => "W", 3 => "D", 4 => "L", 5 => "W", 6 => "D");
      return $white_result_table[$this->result_code];
    } else {
      return "";
    }
  }

  public function point_for($player){
    switch($this->result_code){
      case 1: //black win
        $point = $player->name == $this->black->name ? 3 : 1;
        break;
      case 2: //white win
        $point = $player->name == $this->black->name ? 1 : 3;
        break;
      case 3: //draw
        $point = 2;
        break;
      case 4: //black win by default
        $point = $player->name == $this->black->name ? 3 : 0;
        break;
      case 5: //black lose by default
        $point = $player->name == $this->black->name ? 0 : 3;
        break;
      case 6: //no game
        $point = 1;
        break;
      default: //other
        $point = 0;
        break;
    }
    return $point;
  }

  public static function load(){
    if(!file_exists(self::$file_path)){
      return null;
    }
    $file = fopen(self::$file_path,'r');
    $results = array();
    while($line = fgets($file)){
      if(preg_match('/\* /',$line)){
        $line = substr($line,2);
      }
      $results[] = explode(',',trim($line));
    }
    fclose($file);

    return $results;
  }

  public static function update($kifus,$players){
    $player_names = array();
    foreach($players as $player){
      $player_names[] = $player->name;
    }
    
    $overwrites = array();
    $overwritten_games = array();
    if(file_exists(self::$file_path)){
      $file = fopen(self::$file_path,'r');
      while($line = fgets($file)){
        if(preg_match('/\* /',$line)){
          $overwrites[] = $line;
          $line = substr($line,2);
          $overwritten_games[] = array_slice(explode(',',$line),0,2);
        }
      }
      fclose($file);
    }

    $result_file = fopen(self::$file_path,'w');

    $results = "";
    foreach($kifus->kifu as $kifu){
      if(in_array((string)$kifu->black_name,$player_names) && 
        in_array((string)$kifu->white_name,$player_names)){
        $skip = false;
        foreach($overwritten_games as $game){
          if(($game[0] == $kifu->black_name && $game[1] == $kifu->white_name) ||
             ($game[0] == $kifu->white_name && $game[1] == $kifu->black_name)){
            $skip = true;
            break;
          }
        }
        if($skip){
          continue;
        }
        $result_code = (int)$kifu->result;
        if($result_code == 1 || $result_code == 2 || $result_code == 3){
          $result = implode(',',array($kifu->black_name,$kifu->white_name,$kifu->result,$kifu->id,$kifu->created_at,"\n"));
          $results .= $result;
          fwrite($result_file,$result);
        }
      }
    }
    if(count($overwrites) > 0){
      foreach($overwrites as $overwrite){
        fwrite($result_file,$overwrite);
      }
    }
    
    fclose($result_file);
  }
}
