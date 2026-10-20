<?php
require_once __DIR__ . '/lang.php';
class Schedule
{
  public static function cycleWeek($date) {
    // The Monday of the week containing 2026-01-01 is the fixed A anchor.
    // Calendar days, rather than elapsed seconds, keep DST from shifting weeks.
    $anchor=new DateTime('2025-12-29', $date->getTimezone());
    $day=clone $date; $day->setTime(0,0,0);
    $weeks=(int)floor((int)$anchor->diff($day)->format('%r%a')/7);
    return $weeks%2===0 ? 'A' : 'B';
  }
  public static function weekNumber($date) {
    $january=new DateTime($date->format('Y').'-01-01');
    return 1+(int)floor(((int)$date->format('z')+(int)$january->format('N')-1)/7);
  }
  public static function weekBounds($date) {
    $start=clone $date; $end=clone $date;
    $start->modify('-'.((int)$date->format('N')-1).' days');
    $end->modify('+'.(7-(int)$date->format('N')).' days');
    if ($start->format('Y')!==$date->format('Y')) { $start=new DateTime($date->format('Y').'-01-01'); }
    if ($end->format('Y')!==$date->format('Y')) { $end=new DateTime($date->format('Y').'-12-31'); }
    return array($start,$end);
  }
  public static function rules() {
    return array('workday'=>lang('RULE_WORKDAY'),'restday'=>lang('RULE_RESTDAY'),'daily'=>lang('RULE_DAILY'),'weekly'=>lang('RULE_WEEKLY'),'odd'=>lang('RULE_A'),'even'=>lang('RULE_B'),'monthly'=>lang('RULE_MONTHLY'),'quarterly'=>lang('RULE_QUARTERLY'),'halfyear'=>lang('RULE_HALFYEAR'),'yearly'=>lang('RULE_YEARLY'),'season'=>lang('RULE_SEASON'));
  }
  public static function weekdays() { return array(1=>lang('MONDAY'),2=>lang('TUESDAY'),3=>lang('WEDNESDAY'),4=>lang('THURSDAY'),5=>lang('FRIDAY'),6=>lang('SATURDAY'),7=>lang('SUNDAY')); }
  public static function seasons() { return array(1=>lang('SPRING'),2=>lang('SUMMER'),3=>lang('AUTUMN'),4=>lang('WINTER')); }
  public static function workday($date,$overrides) {
    $key=$date->format('Y-m-d');
    return isset($overrides[$key]) ? $overrides[$key] : (int)$date->format('N')<=5;
  }
  public static function matches($r,$date,$overrides) {
    $day=(int)$date->format('j'); $month=(int)$date->format('n'); $weekday=(int)$date->format('N');
    switch ($r['rule']) {
      case 'daily': return true;
      case 'workday': return self::workday($date,$overrides);
      case 'restday': return !self::workday($date,$overrides);
      case 'weekly': return $weekday===(int)$r['weekday'];
      case 'odd': case 'even': return $weekday===(int)$r['weekday'] && self::cycleWeek($date)===($r['rule']==='odd' ? 'A' : 'B');
      case 'monthly': return $day===min((int)$r['day_number'],(int)$date->format('t'));
      case 'quarterly':
        $start=new DateTime($date->format('Y').'-'.sprintf('%02d',1+3*(int)floor(($month-1)/3)).'-01');
        return (int)floor((int)$start->diff($date)->format('%a')/7)+1===(int)$r['week_number'] && (!(int)$r['weekday'] || $weekday===(int)$r['weekday']);
      case 'halfyear': return ($month-1)%6+1===(int)$r['month_number'] && (!(int)$r['day_number'] || $day===min((int)$r['day_number'],(int)$date->format('t')));
      case 'yearly': return $month===(int)$r['month_number'] && $day===min((int)$r['day_number'],(int)$date->format('t'));
      case 'season': return (int)floor(($month%12)/3)===((int)$r['season']%4);
    }
    return false;
  }
  public static function label($r) {
    $rules=self::rules(); $days=self::weekdays(); $seasons=self::seasons(); $label=$rules[$r['rule']];
    if (in_array($r['rule'],array('weekly','odd','even'))) { $label.=' · '.$days[$r['weekday']]; }
    if ($r['rule']==='monthly') { $label.=' · '.sprintf(lang('NUMBER_DAY'),$r['day_number']); }
    if ($r['rule']==='quarterly') { $label.=' · '.sprintf(lang('WEEK_NUMBER'),$r['week_number']).' · '.($r['weekday'] ? $days[$r['weekday']] : lang('WHOLE_WEEK')); }
    if ($r['rule']==='halfyear') { $label.=' · '.sprintf(lang('NUMBER_MONTH'),$r['month_number']).' · '.($r['day_number'] ? sprintf(lang('NUMBER_DAY'),$r['day_number']) : lang('WHOLE_MONTH')); }
    if ($r['rule']==='yearly') { $label.=' · '.sprintf(lang('NUMBER_MONTH'),$r['month_number']).' · '.sprintf(lang('NUMBER_DAY'),$r['day_number']); }
    if ($r['rule']==='season') { $label.=' · '.$seasons[$r['season']]; }
    return $label;
  }
}
