<?php
class Result{
  private static $file_path = './data/result.txt';

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

    $file = fopen(self::$file_path,'r');
    $overwrites = array();
    $overwritten_games = array();
    while($line = fgets($file)){
      if(preg_match('/\* /',$line)){
        $overwrites[] = $line;
        $line = substr($line,2);
        $overwritten_games[] = array_slice(explode(',',$line),0,2);
      }
    }
    fclose($file);

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
          $result = implode(',',array($kifu->black_name,$kifu->white_name,$kifu->result,$kifu->created_at,"\n"));
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
