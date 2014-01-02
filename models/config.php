<?php
class Config{
  public static function load_and_validate(){
    $config = json_decode(file_get_contents('./data/config.json'));
    //TODO validate config values
    return $config;
  }
}
