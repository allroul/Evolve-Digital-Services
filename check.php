<?php
  $inodes = 0; $total_size = 0; $media_size = 0; $arch_size = 0; $app_size = 0;
  $m_array = array("png", "jpg", "gif", "jpeg", "tiff", "mp3", "mp4", "avi", "svg");
  $a_array = array("tar", "gz", "rar", "zip", "bz2", "tgz", "wpress");
  $p_array = array("exe", "apk");
  $si_prefix = array( 'B', 'K', 'M', 'G', 'T', 'P');
  $base = 1024;
function is_function_enabled($func) {
   $func=strtolower(trim($func));
   if ($func=='') return false;
   // Получить список функций, отключенных в php.ini
   $disabled=explode(",",@ini_get("disable_functions"));
   if (empty($disabled)) {
       $disabled=array();
   }
   else {
       // Убрать пробелы и привести названия к нижнему регистру
       $disabled=array_map('trim',array_map('strtolower',$disabled));
   }
   // Проверить доступность функции разными способами
   return (function_exists($func) && is_callable($func) && !in_array($func,$disabled));
}
function glob_calc($dir) {
  $filelist = glob($dir . '{,.}[!.,!..]*',  GLOB_NOSORT |  GLOB_MARK | GLOB_BRACE);
  foreach($filelist as $file) {
    $GLOBALS["inodes"] += 1;
    if (substr($file, -1) == '/') {
      glob_calc($file);
      $GLOBALS["total_size"] += 4096;
    } else {
      $size = filesize($file);
      $ext = pathinfo($file, PATHINFO_EXTENSION);
      $GLOBALS["total_size"] += $size;
      if (in_array($ext, $GLOBALS["m_array"])) {
        $GLOBALS["media_size"] += $size;
      } elseif (in_array($ext, $GLOBALS["a_array"])) {
        $GLOBALS["arch_size"] += $size;
      } elseif (in_array($ext, $GLOBALS["p_array"])) {
        $GLOBALS["app_size"] += $size;
      }
    }
  }
}
function scan_calc($dir) {
  $filelist = scandir($dir, SCANDIR_SORT_NONE);
  foreach($filelist as $file) {
    if (($file == '.') or ($file == '..')) {
      continue;
    }
    $GLOBALS["inodes"] += 1;
    if (is_dir($dir . '/' . $file)) {
      scan_calc($dir . '/' . $file);
      $GLOBALS["total_size"] += 4096;
    } else {
      $size = filesize($dir . '/' . $file);
      $ext = pathinfo($dir . '/' . $file, PATHINFO_EXTENSION);
      $GLOBALS["total_size"] += $size;
      if (in_array($ext, $GLOBALS["m_array"])) {
        $GLOBALS["media_size"] += $size;
      } elseif (in_array($ext, $GLOBALS["a_array"])) {
        $GLOBALS["arch_size"] += $size;
      } elseif (in_array($ext, $GLOBALS["p_array"])) {
        $GLOBALS["app_size"] += $size;
      }
    }
  }
}
function readdir_calc($dir) {
  if ($h_dir = opendir($dir)) {
    while (false !== ($file = readdir($h_dir))) {
      if (($file == '.') or ($file == '..')) {
        continue;
      }
      $GLOBALS["inodes"] += 1;
      if (is_dir($dir . '/' . $file)) {
        readdir_calc($dir . '/' . $file);
        $GLOBALS["total_size"] += 4096;
      } else {
        $size = filesize($dir . '/' . $file);
        $ext = pathinfo($dir . '/' . $file, PATHINFO_EXTENSION);
        $GLOBALS["total_size"] += $size;
        if (in_array($ext, $GLOBALS["m_array"])) {
          $GLOBALS["media_size"] += $size;
        } elseif (in_array($ext, $GLOBALS["a_array"])) {
          $GLOBALS["arch_size"] += $size;
        } elseif (in_array($ext, $GLOBALS["p_array"])) {
          $GLOBALS["app_size"] += $size;
        }
      }
    }
    closedir($h_dir);
  }
}
function out_size($digit) {
  $class = min((int)log($digit , $GLOBALS["base"]) , count($GLOBALS["si_prefix"]) - 1);
  return sprintf('%1.2f' , $digit / pow($GLOBALS["base"], $class)) . ' ' . $GLOBALS["si_prefix"][$class];
}
set_time_limit(1200);
echo 'PHP version: ' . phpversion();
echo '<pre>';
if (is_function_enabled('shell_exec')) {
  echo "Method - shell_exec\n\n";
  echo "Total size   " . shell_exec('du -sh ./');
  echo "Total inodes " . shell_exec('find . | wc -l');
  echo "\nMediafiles   " . shell_exec('find ./ -regextype posix-egrep -regex ".*\.(png|jpg|gif|jpeg|tiff|mp3|mp4|avi|svg)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "Archives     " . shell_exec('find ./ -regextype posix-egrep -regex ".*\.(tar|gz|rar|zip|bz2|tgz|wpress)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "Programms    " . shell_exec('find ./ -regextype posix-egrep -regex ".*\.(exe|apk)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
} elseif (is_function_enabled('exec')) {
  echo "Method - exec\n\n";
  echo "Total size   " . exec('du -sh ./');
  echo "\nTotal inodes " . exec('find . | wc -l');
  echo "\n\nMediafiles   " . exec('find ./ -regextype posix-egrep -regex ".*\.(png|jpg|gif|jpeg|tiff|mp3|mp4|avi|svg)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "\nArchives     " . exec('find ./ -regextype posix-egrep -regex ".*\.(tar|gz|rar|zip|bz2|tgz|wpress)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "\nProgramms    " . exec('find ./ -regextype posix-egrep -regex ".*\.(exe|apk)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
} elseif (is_function_enabled('passthru')) {
  echo "Method - passrhru\n\n";
  echo "Total size   "; passthru('du -sh ./');
  echo "Total inodes "; passthru('find . | wc -l');
  echo "\nMediafiles   "; passthru('find ./ -regextype posix-egrep -regex ".*\.(png|jpg|gif|jpeg|tiff|mp3|mp4|avi|svg)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "Archives     "; passthru('find ./ -regextype posix-egrep -regex ".*\.(tar|gz|rar|zip|bz2|tgz|wpress)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "Programms    "; passthru('find ./ -regextype posix-egrep -regex ".*\.(exe|apk)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
} elseif (is_function_enabled('system')) {
  echo "Method - system\n\n";
  echo "Total size   "; system('du -sh ./');
  echo "Total inodes "; system('find . | wc -l');
  echo "\nMediafiles   "; system('find ./ -regextype posix-egrep -regex ".*\.(png|jpg|gif|jpeg|tiff|mp3|mp4|avi|svg)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "Archives     "; system('find ./ -regextype posix-egrep -regex ".*\.(tar|gz|rar|zip|bz2|tgz|wpress)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
  echo "Programms    "; system('find ./ -regextype posix-egrep -regex ".*\.(exe|apk)$" -ls  |awk \'BEGIN {sum=0} {sum+=$7/(1024*1024*1024)} END{printf("%.3f Gb", sum)}\'');
} elseif (is_function_enabled('glob')) {
  echo "Method - glob\n\n";
  $basedir = getcwd();
  glob_calc($basedir . '/');
} elseif (is_function_enabled('scandir')) {
  echo "Method - scandir\n\n";
  $basedir = getcwd();
  scan_calc($basedir);
} elseif (is_function_enabled('readdir')) {
  echo "Method - readdir\n\n";
  $basedir = getcwd();
  readdir_calc($basedir);
}
if ($inodes > 0) {
  echo "Total size   " . out_size($total_size);
  echo "\nTotal inodes " . $inodes;
  echo "\n\nMediafiles   " . out_size($media_size);
  echo "\nArchives     " . out_size($arch_size);
  echo "\nProgramms    " . out_size($app_size);
}
echo '</pre>';
