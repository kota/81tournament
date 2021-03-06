<?php 
require_once('./models/result.php');
require_once('./models/config.php');

class Player{
  private static $file_path = './data/players.txt';
  private static $config;
 
  public $name;
  public $display_name;
  public $rate;
  public $country_code;
  public $comment;
  public $results;

  private $place_holder_flag = false;

  public $tournament_point;

  public function __construct($params,$place_holder_flag=false) {
    $this->display_name = trim($params[0]);
    $this->name = strtolower($this->display_name);
    if(count($params) > 1){
      $this->rate = (int)trim($params[1]);
    }
    if(count($params) > 2){
      $this->country_code = (int)trim($params[2]);
    }
    if(count($params) > 3){
      $this->comment = trim($params[3]);
    }
    $this->place_holder_flag = $place_holder_flag;
  }

  public function is_place_holder(){
    return $this->place_holder_flag;
  }

  public function to_json_string(){
    return "{name:'" . $this->display_name . "',rate:'" . $this->rate . "',country_code:'" . $this->country_code . "'}";
  }

  public function country_flag_s_tag(){
    if(!isset($this->country_code)){
      return '&nbsp;';
    }
    $zero_filled = sprintf('%03d', $this->country_code);
    return '<img src="http://81dojo.com/dojo/images/flags_s/' . $zero_filled . '.gif" align="absbottom" />';
  }

  public function calculate_point(){
    if (!$this->results) return 0;
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
      $latest_results = array();
      if($results){
        foreach($results as $raw_result){
          $black = self::find_player_by_name($raw_result[0],$players);
          $white = self::find_player_by_name($raw_result[1],$players);
          $result_code = $raw_result[2];
          $kifu_id = $raw_result[3];
          $created_at = new DateTime($raw_result[4]);
          $result = new Result($result_code,$black,$white,$kifu_id,$created_at);
          $found = false;
          for($i=0;$i<count($latest_results);$i++){
            $latest = $latest_results[$i];
            if($latest->black->name == $result->black->name && $latest->white->name == $result->white->name ||
               $latest->white->name == $result->black->name && $latest->black->name == $result->white->name){
              $found =true;
              if($latest->created_at < $result->created_at){
                $latest_results[$i] = $result;
              }
              break;
            }
          }
          if(!$found){
            $latest_results[] = $result;
          }
        }
        foreach($latest_results as $latest){
          $latest->black->add_result($latest->white,$latest);
          $latest->white->add_result($latest->black,$latest);
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

  public static function create_place_holder(){
    return new Player("place holder",true);
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
