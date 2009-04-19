<?php
function file_join() {
  $args = func_get_args();
  return join(DIRECTORY_SEPARATOR,$args);
}