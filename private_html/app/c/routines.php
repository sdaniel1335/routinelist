<?php
require_once __DIR__.'/schedule.php';
class Routines extends App
{
  private function number($key,$min,$max) {
    $value=isset($_POST[$key]) && is_string($_POST[$key]) ? filter_var($_POST[$key],FILTER_VALIDATE_INT) : false;
    if ($value===false || $value<$min || $value>$max) { throw new Exception(lang('INVALID_RECURRENCE')); }
    return (int)$value;
  }
  public function save() {
    $this->requireCsrf();
    try {
      $id=$this->number('id',0,PHP_INT_MAX);
      $title=isset($_POST['title']) && is_string($_POST['title']) ? trim($_POST['title']) : '';
      $description=isset($_POST['description']) && is_string($_POST['description']) ? trim($_POST['description']) : '';
      $rule=isset($_POST['rule']) && is_string($_POST['rule']) ? $_POST['rule'] : '';
      $rules=Schedule::rules();
      if ($title==='' || strlen($title)>600 || strlen($description)>60000 || !isset($rules[$rule])) { throw new Exception(lang('INVALID_ROUTINE')); }
      $weekday=0; $day=0; $month=0; $week=0; $season=0;
      if (in_array($rule,array('weekly','odd','even','quarterly'))) { $weekday=$this->number('weekday',$rule==='quarterly' ? 0 : 1,7); }
      if (in_array($rule,array('monthly','halfyear','yearly'))) { $day=$this->number('day_number',$rule==='halfyear' ? 0 : 1,31); }
      if (in_array($rule,array('halfyear','yearly'))) { $month=$this->number('month_number',1,$rule==='halfyear' ? 6 : 12); }
      if ($rule==='quarterly') { $week=$this->number('week_number',1,14); }
      if ($rule==='season') { $season=$this->number('season',1,4); }
      $order=$this->number('sort_order',0,9999); $user=auth_user_id();
      if ($id) {
        $stmt=$this->db->prepare('UPDATE '.$this->table('routines').' SET title=?,description=?,rule=?,weekday=?,day_number=?,month_number=?,week_number=?,season=?,sort_order=? WHERE id=? AND user_id=?');
        $stmt->bind_param('sssiiiiiiii',$title,$description,$rule,$weekday,$day,$month,$week,$season,$order,$id,$user);
      } else {
        $stmt=$this->db->prepare('INSERT INTO '.$this->table('routines').' (title,description,rule,weekday,day_number,month_number,week_number,season,sort_order,user_id) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('sssiiiiiii',$title,$description,$rule,$weekday,$day,$month,$week,$season,$order,$user);
      }
      $stmt->execute(); $stmt->close(); $this->flash('success',lang('ROUTINE_SAVED')); $this->redirect('/#routines');
    } catch (Exception $e) {
      $this->flash('error',$e instanceof mysqli_sql_exception ? lang('SAVE_FAILED') : $e->getMessage());
      $_SESSION['routine_draft']=$_POST; $this->redirect('/#routine-form');
    }
  }
  public function delete() {
    $this->requireCsrf(); $id=isset($_POST['id']) ? (int)$_POST['id'] : 0; $user=auth_user_id();
    $stmt=$this->db->prepare('DELETE FROM '.$this->table('routines').' WHERE id=? AND user_id=?');
    $stmt->bind_param('ii',$id,$user); $stmt->execute(); $stmt->close();
    $this->flash('success',lang('ROUTINE_DELETED')); $this->redirect('/#routines');
  }
  public function calendar() {
    $this->requireCsrf(); $date=isset($_POST['date']) && is_string($_POST['date']) ? $_POST['date'] : '';
    $parsed=DateTime::createFromFormat('!Y-m-d',$date);
    if (!$parsed || $parsed->format('Y-m-d')!==$date) { $this->flash('error',lang('INVALID_DATE')); $this->redirect('/#calendar'); }
    $user=auth_user_id();
    if (isset($_POST['remove'])) {
      $stmt=$this->db->prepare('DELETE FROM '.$this->table('routine_calendar').' WHERE user_id=? AND date=?'); $stmt->bind_param('is',$user,$date);
    } else {
      $workday=isset($_POST['is_workday']) && $_POST['is_workday']==='1' ? 1 : 0;
      $stmt=$this->db->prepare('INSERT INTO '.$this->table('routine_calendar').' (user_id,date,is_workday) VALUES (?,?,?) ON DUPLICATE KEY UPDATE is_workday=VALUES(is_workday)');
      $stmt->bind_param('isi',$user,$date,$workday);
    }
    $stmt->execute(); $stmt->close(); $this->flash('success',lang('EXCEPTION_SAVED')); $this->redirect('/#calendar');
  }
}

