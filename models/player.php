<?php 
require_once('./models/result.php');
require_once('./models/config.php');

class Player{
  private static $file_path = './data/players.txt';
  private static $config;
 
  public $name;
  public $rate;
  public $country_code;
  public $comment;
  public $results;

  public $tournament_point;

  public function __construct($params) {
    $this->name = trim($params[0]);
    if(count($params) > 1){
      $this->rate = (int)trim($params[1]);
    }
    if(count($params) > 2){
      $this->country_code = (int)trim($params[2]);
    }
    if(count($params) > 3){
      $this->comment = trim($params[3]);
    }
  }

  public function to_json_string(){
    return "{name:'" . $this->name . "',rate:'" . $this->rate . "',country_code:'" . $this->country_code . "'}";
  }

  public function country_flag_s_tag(){
    if(!isset($this->country_code)){
      return '&nbsp;';
    }
    $zero_filled = sprintf('%03d', $this->country_code);
    return '<img src="http://81dojo.com/dojo/images/flags_s/' . $zero_filled . '.gif" />';
  }

  public function calculate_point(){
    $this->tournament_point = 0;
    foreach($this->results as $key => $result){
      $this->tournament_point += $result->point_for($this);
    }
    return $this->tournament_point;
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
        foreach($results as $raw_result){
          $black = self::find_player_by_name($raw_result[0],$players);
          $white = self::find_player_by_name($raw_result[1],$players);
          $result_code = $raw_result[2];
          $kifu_id = $raw_result[3];
          $result = new Result($result_code,$black,$white,$kifu_id);
          
          $black->add_result($white,$result);
          $white->add_result($black,$result);
        }
      }
    }

    return $players;
  }

  public function get_result($opponent){
    if($this->results && array_key_exists($opponent->name,$this->results)){
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
        fwrite($tmp_file,$login . "," . $rate . "," . $country_code . ",\n");
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
