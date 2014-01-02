<?php 
require_once('./models/result.php');
require_once('./models/config.php');

class Player{
  private static $file_path = './data/players.txt';
  private static $config;
 
  public $name;
  public $rate;
  public $country_code;
  public $results;

  public function __construct($params) {
    $this->name = trim($params[0]);
    if(count($params) > 1){
      $this->rate = (int)trim($params[1]);
    }
    if(count($params) > 2){
      $this->country_code = (int)trim($params[2]);
    }
  }

  public function to_json_string(){
    return "{name:'" . $this->name . "',rate:'" . $this->rate . "',country_code:'" . $this->country_code . "'}";
  }

  public static function load($load_results=false){

    $file = fopen(self::$file_path,'r');
    $players = array();
    while($line = fgets($file)){
      if(strpos($line,"#") === 0){ #ignore lines starting with #.
        continue;
      }
      $players[] = new Player(explode(',',$line));
    }
    fclose($file);
    if($load_results){
      $results = Result::load();
      if($results){
        $black_result_table = array(1 => "W", 2 => "L", 3 => "D");
        $white_result_table = array(1 => "L", 2 => "W", 3 => "D");
        foreach($results as $result){
          $black = self::find_player_by_name($result[0],$players);
          $white = self::find_player_by_name($result[1],$players);
          $result_code = $result[2];

          $black->add_result($white,$black_result_table[$result_code]);
          $white->add_result($black,$white_result_table[$result_code]);
        }
      }
    }

    return $players;
  }

  public function get_result($opponent){
    if(array_key_exists($opponent->name,$this->results)){
      return $this->results[$opponent->name];
    }
    return null;
  }

  public function add_result($player,$result){
    if(!$this->results){
      $this->results = array();
    }
    $this->results[$player->name] = $result;
  }

  public static function update_source_file(){
    self::$config = Config::load_and_validate();
    $tmp_file_path = './data/players.tmp';

    $file = fopen(self::$file_path,'r');
    $tmp_file = fopen($tmp_file_path,'w');
    while($line = fgets($file)){
      if(strpos($line,"#") === 0){ #ignore lines starting with #.
        fwrite($tmp_file,$line);
        continue;
      }
      $elements = explode(',',$line);
      $login = trim($elements[0]);
      if($detail = self::fetch_player_detail($login)){
        $country_code = (int)$detail->country->code;
        $rate = (int)$detail->rate;
        fwrite($tmp_file,$login . "," . $rate . "," . $country_code . "\n");
      } else {
        fwrite($tmp_file,$login . "\n");
      }
    }
    fclose($file);
    fclose($tmp_file);

    rename($tmp_file_path, self::$file_path);
  }

  private static function fetch_player_detail($login){
    $url = "http://" . self::$config->host . "/api/players/with_login.xml";
    $data = array(
        "login" => $login,
    );
    $options = array('http' => array(
        'method' => 'GET',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($data),
    ));
    $response = @file_get_contents($url, false, stream_context_create($options));
    if($response === false){
      echo "Player not found. login = ".$login . "\n";
      return null;
    }
    $player = new SimpleXMLElement($response);
    return $player;
  }

  private static function find_player_by_name($name,$players){
    foreach($players as $player){
      if($player->name == $name){
        return $player;
      }
    }
  }
}
