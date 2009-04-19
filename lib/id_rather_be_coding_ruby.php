<?
class File {
  public static function join() {
    return join(DIRECTORY_SEPARATOR,func_get_args());
  }
  
  public static function dirname($path) {
    return dirname($path);
  }
  
  public static function exist($path) {
    return file_exists($path);
  }
}
?>