<?php
function routine_escape($value) { return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8'); }
$csrf=routine_escape(auth_csrf_token()); $weekdays=Schedule::weekdays();
$form=array('id'=>0,'title'=>'','description'=>'','rule'=>'workday','weekday'=>1,'day_number'=>15,'month_number'=>1,'week_number'=>1,'season'=>1,'sort_order'=>0);
if ($editing) { $form=array_merge($form,$editing); }
if (isset($_SESSION['routine_draft'])) {
  foreach ($form as $key=>$value) { if (isset($_SESSION['routine_draft'][$key]) && is_scalar($_SESSION['routine_draft'][$key])) { $form[$key]=$_SESSION['routine_draft'][$key]; } }
  unset($_SESSION['routine_draft']);
}
?>
<div class="routine-page" data-today-label="<?php echo routine_escape(lang('TODAY_BADGE')); ?>">
  <div class="page-heading">
    <div><p class="eyebrow"><?php echo htmlspecialchars(lang('OVERVIEW'), ENT_QUOTES, 'UTF-8'); ?></p><h1><?php echo htmlspecialchars(lang('THREE_WEEKS'), ENT_QUOTES, 'UTF-8'); ?></h1><p class="text-secondary mb-0"><?php echo routine_escape(lang('TODAY')); ?>: <?php echo $today->format(lang('DATE_FORMAT')); ?> · <?php echo $weekdays[(int)$today->format('N')]; ?></p></div>
    <a href="#routine-form" class="btn btn-primary">+ <?php echo htmlspecialchars(lang('NEW_ROUTINE'), ENT_QUOTES, 'UTF-8'); ?></a>
  </div>
  <?php foreach ($weeks as $week) { $first=$week['start']; $last=$week['end']; ?>
  <section class="week-section week-<?php echo $week['position']; ?>" aria-label="<?php echo $week['label']; ?>">
    <?php if ($week['position']==='past') { ?><details class="past-week"><summary><?php echo htmlspecialchars(lang('PAST_WEEK'), ENT_QUOTES, 'UTF-8'); ?> <span>· <?php echo $first->format(lang('DATE_FORMAT')).' – '.$last->format(lang('SHORT_DATE_FORMAT')); ?> · <?php echo htmlspecialchars(lang('OPEN'), ENT_QUOTES, 'UTF-8'); ?></span></summary><?php } ?>
    <div class="week-heading"><h2><?php echo $week['label']; ?></h2><span><?php echo $first->format(lang('DATE_FORMAT')).' – '.$last->format(lang('SHORT_DATE_FORMAT')); ?> · <?php echo routine_escape(sprintf(lang('WEEK_NUMBER'),Schedule::weekNumber($first))); ?> · <?php echo routine_escape(sprintf(lang('CYCLE_WEEK'),Schedule::cycleWeek($first))); ?></span></div>
    <div class="week-scroll" tabindex="0" aria-label="<?php echo htmlspecialchars(lang('CALENDAR_ARIA'), ENT_QUOTES, 'UTF-8'); ?>">
      <div class="week-grid">
      <?php foreach ($week['days'] as $index=>$day) { if ($day===null) { ?><div class="day day-outside"><div class="day-heading"><?php echo $weekdays[$index+1]; ?><small class="d-block mt-2"><?php echo htmlspecialchars(lang('OTHER_YEAR'), ENT_QUOTES, 'UTF-8'); ?></small></div></div><?php continue; } $date=$day['date']; $is_today=$date->format('Y-m-d')===$today->format('Y-m-d'); ?>
        <article class="day<?php echo $is_today ? ' is-today' : ''; ?><?php echo !$day['workday'] ? ' rest-day' : ''; ?>"<?php echo $is_today ? ' aria-current="date"' : ''; ?>>
          <div class="day-heading"><span><?php echo $weekdays[(int)$date->format('N')]; ?></span><?php if ($is_today) { ?><strong class="today-badge"><?php echo routine_escape(lang('TODAY_BADGE')); ?></strong><?php } ?><time datetime="<?php echo $date->format('Y-m-d'); ?>"><?php echo $date->format(lang('SHORT_DATE_FORMAT')); ?></time><small><?php echo $day['workday'] ? lang('WORKDAY') : lang('RESTDAY'); ?></small></div>
          <div class="day-items">
          <?php foreach ($day['items'] as $item) { ?>
            <details class="routine-card"><summary><?php echo routine_escape($item['title']); ?></summary><div class="routine-detail"><?php if ($item['description']!=='') { ?><p><?php echo nl2br(routine_escape($item['description'])); ?></p><?php } else { ?><p class="text-secondary"><?php echo htmlspecialchars(lang('NO_DESCRIPTION'), ENT_QUOTES, 'UTF-8'); ?></p><?php } ?><small><?php echo routine_escape(Schedule::label($item)); ?></small></div></details>
          <?php } ?>
          <?php if (!$day['items']) { ?><p class="empty-day"><?php echo htmlspecialchars(lang('NO_ROUTINE'), ENT_QUOTES, 'UTF-8'); ?></p><?php } ?>
          </div>
        </article>
      <?php } ?>
      </div>
    </div>
    <?php if ($week['position']==='past') { ?></details><?php } ?>
  </section>
  <?php } ?>
  <section id="routines" class="management-section">
    <div class="section-heading"><h2><?php echo htmlspecialchars(lang('SAVED_ROUTINES'), ENT_QUOTES, 'UTF-8'); ?> <span class="count"><?php echo count($routines); ?></span></h2><a href="#routine-form"><?php echo htmlspecialchars(lang('ADD_ROUTINE'), ENT_QUOTES, 'UTF-8'); ?></a></div>
    <?php if (!$routines) { ?><div class="empty-list"><?php echo htmlspecialchars(lang('EMPTY_LIST'), ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
    <div class="routine-list">
    <?php foreach ($routines as $item) { ?>
      <div class="routine-row"><div class="routine-row-content"><h3><?php echo routine_escape($item['title']); ?></h3><p class="rule-label"><?php echo routine_escape(Schedule::label($item)); ?> · <?php echo htmlspecialchars(lang('ORDER'), ENT_QUOTES, 'UTF-8'); ?>: <?php echo (int)$item['sort_order']; ?></p><?php if ($item['description']!=='') { ?><p class="description"><?php echo nl2br(routine_escape($item['description'])); ?></p><?php } ?></div><div class="row-actions"><a class="btn btn-sm btn-outline-secondary" href="<?php echo url('/?edit='.(int)$item['id'].'#routine-form'); ?>"><?php echo htmlspecialchars(lang('EDIT'), ENT_QUOTES, 'UTF-8'); ?></a><form action="<?php echo url('/routine/delete'); ?>" method="post" data-confirm="<?php echo htmlspecialchars(lang('DELETE_CONFIRM'), ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>"><button class="btn btn-sm btn-outline-danger" type="submit"><?php echo htmlspecialchars(lang('DELETE'), ENT_QUOTES, 'UTF-8'); ?></button></form></div></div>
    <?php } ?>
    </div>
  </section>
  <section id="routine-form" class="management-section form-section">
    <h2><?php echo $form['id'] ? lang('EDIT_ROUTINE') : lang('NEW_ROUTINE'); ?></h2>
    <form action="<?php echo url('/routine/save'); ?>" method="post" id="routine-editor">
      <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><input type="hidden" name="id" value="<?php echo (int)$form['id']; ?>">
      <div class="row g-3">
        <div class="col-md-8"><label for="routine-title" class="form-label"><?php echo htmlspecialchars(lang('NAME'), ENT_QUOTES, 'UTF-8'); ?></label><input id="routine-title" class="form-control" name="title" value="<?php echo routine_escape($form['title']); ?>" maxlength="200" placeholder="<?php echo htmlspecialchars(lang('NAME_EXAMPLE'), ENT_QUOTES, 'UTF-8'); ?>" required></div>
        <div class="col-md-4"><label for="sort-order" class="form-label"><?php echo htmlspecialchars(lang('DISPLAY_ORDER'), ENT_QUOTES, 'UTF-8'); ?></label><input id="sort-order" class="form-control" type="number" name="sort_order" min="0" max="9999" value="<?php echo (int)$form['sort_order']; ?>" required><div class="form-text"><?php echo htmlspecialchars(lang('ORDER_HELP'), ENT_QUOTES, 'UTF-8'); ?></div></div>
        <div class="col-12"><label for="routine-description" class="form-label"><?php echo htmlspecialchars(lang('DESCRIPTION'), ENT_QUOTES, 'UTF-8'); ?></label><textarea id="routine-description" class="form-control" name="description" rows="3" maxlength="15000" placeholder="<?php echo htmlspecialchars(lang('DESCRIPTION_EXAMPLE'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo routine_escape($form['description']); ?></textarea></div>
        <div class="col-md-6"><label for="routine-rule" class="form-label"><?php echo htmlspecialchars(lang('RECURRENCE'), ENT_QUOTES, 'UTF-8'); ?></label><select id="routine-rule" class="form-select" name="rule"><?php foreach (Schedule::rules() as $key=>$label) { ?><option value="<?php echo $key; ?>"<?php echo $form['rule']===$key ? ' selected' : ''; ?>><?php echo $label; ?></option><?php } ?></select></div>
        <div class="col-md-6" data-rules="weekly odd even quarterly"><label for="routine-weekday" class="form-label"><?php echo htmlspecialchars(lang('WEEKDAY'), ENT_QUOTES, 'UTF-8'); ?></label><select id="routine-weekday" name="weekday" class="form-select"><option value="0" data-quarter-only><?php echo htmlspecialchars(lang('WEEK_WINDOW'), ENT_QUOTES, 'UTF-8'); ?></option><?php foreach ($weekdays as $key=>$label) { ?><option value="<?php echo $key; ?>"<?php echo (int)$form['weekday']===$key ? ' selected' : ''; ?>><?php echo $label; ?></option><?php } ?></select></div>
        <div class="col-md-6" data-rules="monthly halfyear yearly"><label for="routine-day" class="form-label"><?php echo htmlspecialchars(lang('MONTH_DAY'), ENT_QUOTES, 'UTF-8'); ?></label><input id="routine-day" class="form-control" type="number" name="day_number" min="0" max="31" value="<?php echo (int)$form['day_number']; ?>"><div class="form-text"><?php echo htmlspecialchars(lang('DAY_HELP'), ENT_QUOTES, 'UTF-8'); ?></div></div>
        <div class="col-md-6" data-rules="halfyear yearly"><label for="routine-month" class="form-label"><?php echo htmlspecialchars(lang('WHICH_MONTH'), ENT_QUOTES, 'UTF-8'); ?></label><input id="routine-month" class="form-control" type="number" name="month_number" min="1" max="12" value="<?php echo max(1,(int)$form['month_number']); ?>"><div class="form-text"><?php echo htmlspecialchars(lang('MONTH_HELP'), ENT_QUOTES, 'UTF-8'); ?></div></div>
        <div class="col-md-6" data-rules="quarterly"><label for="routine-week" class="form-label"><?php echo htmlspecialchars(lang('QUARTER_WEEK'), ENT_QUOTES, 'UTF-8'); ?></label><input id="routine-week" class="form-control" type="number" name="week_number" min="1" max="14" value="<?php echo max(1,(int)$form['week_number']); ?>"><div class="form-text"><?php echo htmlspecialchars(lang('QUARTER_HELP'), ENT_QUOTES, 'UTF-8'); ?></div></div>
        <div class="col-md-6" data-rules="season"><label for="routine-season" class="form-label"><?php echo htmlspecialchars(lang('SEASON'), ENT_QUOTES, 'UTF-8'); ?></label><select id="routine-season" name="season" class="form-select"><?php foreach (Schedule::seasons() as $key=>$label) { ?><option value="<?php echo $key; ?>"<?php echo (int)$form['season']===$key ? ' selected' : ''; ?>><?php echo $label; ?></option><?php } ?></select></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><?php echo htmlspecialchars(lang('SAVE_ROUTINE'), ENT_QUOTES, 'UTF-8'); ?></button><?php if ($form['id']) { ?> <a class="btn btn-outline-secondary" href="<?php echo url('/#routine-form'); ?>"><?php echo htmlspecialchars(lang('CANCEL'), ENT_QUOTES, 'UTF-8'); ?></a><?php } ?></div>
      </div>
    </form>
  </section>
  <section id="calendar" class="management-section">
    <details><summary class="calendar-summary"><?php echo htmlspecialchars(lang('CALENDAR_EXCEPTIONS'), ENT_QUOTES, 'UTF-8'); ?></summary>
      <p class="text-secondary mt-3"><?php echo htmlspecialchars(lang('CALENDAR_HELP'), ENT_QUOTES, 'UTF-8'); ?></p>
      <form class="row g-2 align-items-end mb-3" method="post" action="<?php echo url('/calendar'); ?>"><input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><div class="col-sm-4"><label for="override-date" class="form-label"><?php echo htmlspecialchars(lang('DATE'), ENT_QUOTES, 'UTF-8'); ?></label><input class="form-control" type="date" name="date" id="override-date" required></div><div class="col-sm-4"><label for="override-type" class="form-label"><?php echo htmlspecialchars(lang('DAY_TYPE'), ENT_QUOTES, 'UTF-8'); ?></label><select class="form-select" id="override-type" name="is_workday"><option value="0"><?php echo htmlspecialchars(lang('HOLIDAY'), ENT_QUOTES, 'UTF-8'); ?></option><option value="1"><?php echo htmlspecialchars(lang('WORKDAY'), ENT_QUOTES, 'UTF-8'); ?></option></select></div><div class="col-sm-4"><button class="btn btn-outline-primary"><?php echo htmlspecialchars(lang('SAVE_EXCEPTION'), ENT_QUOTES, 'UTF-8'); ?></button></div></form>
      <?php foreach ($overrides as $date=>$workday) { ?><div class="override-row"><span><?php echo routine_escape($date); ?> · <?php echo $workday ? lang('WORKDAY') : lang('RESTDAY'); ?></span><form method="post" action="<?php echo url('/calendar'); ?>" data-confirm="<?php echo htmlspecialchars(lang('DELETE_EXCEPTION_CONFIRM'), ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><input type="hidden" name="date" value="<?php echo routine_escape($date); ?>"><button name="remove" value="1" class="btn btn-sm btn-outline-danger"><?php echo htmlspecialchars(lang('DELETE'), ENT_QUOTES, 'UTF-8'); ?></button></form></div><?php } ?>
    </details>
  </section>
  <section class="management-section" aria-labelledby="year-weeks-heading">
    <h2 id="year-weeks-heading" class="mb-3"><?php echo htmlspecialchars(lang('YEAR_WEEKS'), ENT_QUOTES, 'UTF-8'); ?></h2>
    <p class="text-secondary"><?php echo htmlspecialchars(lang('YEAR_HELP'), ENT_QUOTES, 'UTF-8'); ?></p>
    <?php for ($year=(int)$today->format('Y'); $year<=(int)$today->format('Y')+1; $year++) {
      $first=new DateTime($year.'-01-01'); $bounds=Schedule::weekBounds($first);
      $last=new DateTime(($year-1).'-12-31'); $last_bounds=Schedule::weekBounds($last);
      $last_number=Schedule::weekNumber($last);
    ?>
      <div class="mb-3">
        <h3 class="h6"><?php echo $year; ?> · <?php echo $year===(int)$today->format('Y') ? lang('THIS_YEAR') : lang('NEXT_YEAR'); ?></h3>
        <p class="mb-1"><?php echo htmlspecialchars(lang('JANUARY_WEEK'), ENT_QUOTES, 'UTF-8'); ?>: <strong><?php echo routine_escape(sprintf(lang('CYCLE_WEEK'),Schedule::cycleWeek($first))); ?></strong>: <strong><?php echo $first->format(lang('DATE_FORMAT')); ?> (<?php echo $weekdays[(int)$first->format('N')]; ?>)</strong> – <?php echo $bounds[1]->format(lang('DATE_FORMAT')); ?>.</p>
        <p class="mb-0 text-secondary"><?php echo htmlspecialchars(lang('PREVIOUS_LAST_WEEK'), ENT_QUOTES, 'UTF-8'); ?>: <?php echo routine_escape(sprintf(lang('WEEK_NUMBER'),$last_number)); ?> (<?php echo routine_escape(sprintf(lang('CYCLE_WEEK'),Schedule::cycleWeek($last))); ?>), <?php echo $last_bounds[0]->format(lang('DATE_FORMAT')).' – '.$last->format(lang('DATE_FORMAT')); ?>.<?php if (Schedule::cycleWeek($last)===Schedule::cycleWeek($first)) { ?> <?php echo routine_escape(sprintf(lang('YEAR_SPLIT'),Schedule::cycleWeek($first))); ?><?php } ?></p>
      </div>
    <?php } ?>
  </section>
  <p class="schedule-note"><?php echo htmlspecialchars(lang('SCHEDULE_NOTE'), ENT_QUOTES, 'UTF-8'); ?></p>
</div>
