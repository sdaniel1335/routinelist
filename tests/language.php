<?php
// Run separately: php tests/language.php hu, en, or an unsupported code (e.g. de).
$language=isset($argv[1]) ? $argv[1] : 'hu';
define('APP_LANG',$language);
require_once dirname(__DIR__).'/private_html/app/c/schedule.php';
$prefix=$language==='hu' ? 'LANG_HU_' : 'LANG_EN_';
$count=0;
foreach (get_defined_constants() as $name=>$value) {
  if (strpos($name,'LANG_HU_')!==0) { continue; }
  $key=substr($name,8);
  if (!is_string($value) || !defined('LANG_EN_'.$key) || lang($key)!==constant($prefix.$key)) {
    fwrite(STDERR,'FAIL: '.$name."\n"); exit(1);
  }
  $count++;
}
if ($count!==112) { exit(1); }
$days=Schedule::weekdays();
if ($days[1]!==lang('MONDAY') || count(Schedule::rules())!==11) { exit(1); }
$r=array('rule'=>'monthly','day_number'=>15);
if (Schedule::label($r)!==lang('RULE_MONTHLY').' · '.sprintf(lang('NUMBER_DAY'),15)) { exit(1); }
echo 'Language checks passed: '.$language."\n";
