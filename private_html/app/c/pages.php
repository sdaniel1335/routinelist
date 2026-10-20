<?php
require_once __DIR__ . '/schedule.php';
class Pages extends App
{
  public function index() {
    $user=auth_user_id(); $routines=array();
    $stmt=$this->db->prepare('SELECT id,title,description,rule,weekday,day_number,month_number,week_number,season,sort_order FROM '.$this->table('routines').' WHERE user_id=? ORDER BY sort_order,id');
    $stmt->bind_param('i',$user); $stmt->execute();
    $stmt->bind_result($id,$name,$description,$rule,$weekday,$day,$month,$week,$season,$order);
    while ($stmt->fetch()) {
      $routines[]=array('id'=>$id,'title'=>$name,'description'=>$description,'rule'=>$rule,'weekday'=>$weekday,'day_number'=>$day,'month_number'=>$month,'week_number'=>$week,'season'=>$season,'sort_order'=>$order);
    }
    $stmt->close(); $overrides=array();
    $stmt=$this->db->prepare('SELECT date,is_workday FROM '.$this->table('routine_calendar').' WHERE user_id=? ORDER BY date');
    $stmt->bind_param('i',$user); $stmt->execute(); $stmt->bind_result($date,$workday);
    while ($stmt->fetch()) { $overrides[$date]=(bool)$workday; }
    $stmt->close();
    $today=new DateTime('today'); $current=Schedule::weekBounds($today);
    $previous=clone $current[0]; $previous->modify('-1 day');
    $next=clone $current[1]; $next->modify('+1 day'); $weeks=array();
    foreach (array('past'=>$previous,'current'=>$today,'future'=>$next) as $position=>$reference) {
      $bounds=Schedule::weekBounds($reference); $cursor=clone $bounds[0]; $days=array_fill(0,7,null);
      while ($cursor<=$bounds[1]) {
        $items=array();
        foreach ($routines as $routine) { if (Schedule::matches($routine,$cursor,$overrides)) { $items[]=$routine; } }
        $days[(int)$cursor->format('N')-1]=array('date'=>clone $cursor,'items'=>$items,'workday'=>Schedule::workday($cursor,$overrides));
        $cursor->modify('+1 day');
      }
      $labels=array('past'=>'Múlt hét','current'=>'Aktuális hét','future'=>'Jövő hét');
      $weeks[]=array('label'=>$labels[$position],'position'=>$position,'start'=>$bounds[0],'end'=>$bounds[1],'days'=>$days);
    }
    $editing=null; $edit=isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
    foreach ($routines as $routine) { if ((int)$routine['id']===$edit) { $editing=$routine; } }
    $this->set(array('title'=>APP_TITLE,'routines'=>$routines,'weeks'=>$weeks,'today'=>$today,'editing'=>$editing,'overrides'=>$overrides));
    $this->render('pages/index');
  }
}
