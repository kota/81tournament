<?php
class Result{
  public static function load(){
    $file = fopen('./data/result.txt','r');
    $results = array();
    while($line = fgets($file)){
      $results[] = explode(',',trim($line));
    }
    fclose($file);
    return $results;
  }

  public static function update($kifus,$player_names){
    $result_file = fopen('./data/result.txt','w');
    $results = "";
    foreach($kifus->kifu as $kifu){
      if(in_array((string)$kifu->black_name,$player_names) && 
         in_array((string)$kifu->white_name,$player_names)){
        $result_code = (int)$kifu->result;
        if($result_code == 1 || $result_code == 2 || $result_code == 3){
          $result = implode(',',array($kifu->black_name,$kifu->white_name,$kifu->result,$kifu->created_at,"\n"));
          $results .= $result;
          fwrite($result_file,$result);
        }
      }
    }
    
    fclose($result_file);
  }
}
