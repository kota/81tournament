<?php
class Config{
  private static $file_path = './data/config.json';

  public static function load_and_validate(){
    $config = json_decode(file_get_contents(self::$file_path));
    //TODO validate config values
    return $config;
  }
}
