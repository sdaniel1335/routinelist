<?php
require_once dirname(__DIR__).'/private_html/app/c/schedule.php';
date_default_timezone_set('Europe/Budapest');
$count=0;
foreach (array(
  array('2026-12-31',53,'2026-12-28','2026-12-31'),
  array('2027-01-01',1,'2027-01-01','2027-01-03'),
  array('2027-01-03',1,'2027-01-01','2027-01-03'),
  array('2027-01-04',2,'2027-01-04','2027-01-10'),
  array('2024-01-01',1,'2024-01-01','2024-01-07'),
  array('2023-01-01',1,'2023-01-01','2023-01-01')
) as $case) {
  $date=new DateTime($case[0]); $bounds=Schedule::weekBounds($date);
  if (Schedule::weekNumber($date)!==$case[1] || $bounds[0]->format('Y-m-d')!==$case[2] || $bounds[1]->format('Y-m-d')!==$case[3]) {
    fwrite(STDERR,'FAIL: week boundaries '.$case[0]."\n"); exit(1);
  }
  $count++;
}
function check($rule,$date,$expected,$fields=array(),$overrides=array()) {
  global $count;
  $r=array_merge(array('rule'=>$rule,'weekday'=>0,'day_number'=>0,'month_number'=>0,'week_number'=>0,'season'=>0),$fields);
  $actual=Schedule::matches($r,new DateTime($date),$overrides);
  if ($actual!==$expected) { fwrite(STDERR,'FAIL: '.$rule.' '.$date."\n"); exit(1); }
  $count++;
}
check('daily','2026-10-20',true);
check('workday','2026-10-19',true);
check('workday','2026-10-18',false);
check('restday','2026-10-18',true);
check('workday','2026-10-23',false,array(),array('2026-10-23'=>false));
check('restday','2026-10-23',true,array(),array('2026-10-23'=>false));
check('workday','2026-10-24',true,array(),array('2026-10-24'=>true));
check('weekly','2026-10-21',true,array('weekday'=>3));
check('weekly','2026-10-22',false,array('weekday'=>3));
check('odd','2026-10-24',true,array('weekday'=>6));
check('even','2026-10-24',false,array('weekday'=>6));
check('even','2026-10-31',true,array('weekday'=>6));
check('odd','2027-01-03',true,array('weekday'=>7));
check('odd','2027-01-04',false,array('weekday'=>1));
check('even','2027-01-04',true,array('weekday'=>1));
check('odd','2026-12-31',true,array('weekday'=>4));
check('odd','2027-01-01',true,array('weekday'=>5));
check('monthly','2026-02-28',true,array('day_number'=>31));
check('monthly','2028-02-29',true,array('day_number'=>31));
check('monthly','2026-10-15',true,array('day_number'=>15));
check('monthly','2026-10-16',false,array('day_number'=>15));
check('quarterly','2026-04-01',true,array('week_number'=>1));
check('quarterly','2026-04-07',true,array('week_number'=>1));
check('quarterly','2026-04-08',false,array('week_number'=>1));
check('quarterly','2026-04-08',true,array('week_number'=>2,'weekday'=>3));
check('quarterly','2026-04-09',false,array('week_number'=>2,'weekday'=>3));
check('quarterly','2026-12-31',true,array('week_number'=>14));
check('quarterly','2027-01-01',false,array('week_number'=>14));
check('halfyear','2026-02-01',true,array('month_number'=>2));
check('halfyear','2026-08-31',true,array('month_number'=>2));
check('halfyear','2026-09-01',false,array('month_number'=>2));
check('halfyear','2026-08-15',true,array('month_number'=>2,'day_number'=>15));
check('halfyear','2026-08-16',false,array('month_number'=>2,'day_number'=>15));
check('yearly','2026-02-28',true,array('month_number'=>2,'day_number'=>29));
check('yearly','2028-02-29',true,array('month_number'=>2,'day_number'=>29));
check('season','2026-03-01',true,array('season'=>1));
check('season','2026-05-31',true,array('season'=>1));
check('season','2026-06-01',false,array('season'=>1));
check('season','2026-12-31',true,array('season'=>4));
check('season','2027-01-01',true,array('season'=>4));
check('season','2027-02-28',true,array('season'=>4));
check('season','2027-03-01',false,array('season'=>4));
echo $count." schedule checks passed.\n";
