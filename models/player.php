<?php 
class Player{
  public $name;

  public function __construct($params) {
    $this->name = trim($params[0]);
  }

  public static function load(){
    $file = fopen('./data/players.txt','r');
    $players = array();
    
    while($line = fgets($file)){
      $player = new Player(explode(',',$line));
      $players[] = $player;
    }
    fclose($file);
    return $players;
  }
}
