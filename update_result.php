<?php
require('./models/player.php');
require('./models/config.php');
require('./models/result.php');

$config = Config::load_and_validate();

$players = Player::load();

$player_names = array();
foreach($players as $player){
  $player_names[] = $player->name;
}

$url = "http://" . $config->host . "/api/kifus/search_tournament";
$data = array(
    "tournament_name" => $config->tournament_name,
    "begin_date" => $config->begin_date,
    "end_date" => $config->end_date
);
$options = array('http' => array(
    'method' => 'POST',
    'header'  => 'Content-type: application/x-www-form-urlencoded',
    'content' => http_build_query($data),
));

$kifus = new SimpleXMLElement(file_get_contents($url, false, stream_context_create($options)));
Result::update($kifus,$player_names);

echo "successfully update data/result.txt\n";
