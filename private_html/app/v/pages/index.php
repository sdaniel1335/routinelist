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
<div class="routine-page">
  <div class="page-heading">
    <div><p class="eyebrow">RUTINOK · HETI ÁTTEKINTÉS</p><h1>Három hét, egy helyen</h1><p class="text-secondary mb-0">Ma: <?php echo $today->format('Y. m. d.'); ?> · <?php echo $weekdays[(int)$today->format('N')]; ?></p></div>
    <a href="#routine-form" class="btn btn-primary">+ Új rutin</a>
  </div>
  <?php foreach ($weeks as $week) { $first=$week['start']; $last=$week['end']; ?>
  <section class="week-section week-<?php echo $week['position']; ?>" aria-label="<?php echo $week['label']; ?>">
    <?php if ($week['position']==='past') { ?><details class="past-week"><summary>Múlt hét <span>· <?php echo $first->format('Y. m. d.').' – '.$last->format('m. d.'); ?> · Megnyitás</span></summary><?php } ?>
    <div class="week-heading"><h2><?php echo $week['label']; ?></h2><span><?php echo $first->format('Y. m. d.').' – '.$last->format('m. d.'); ?> · <?php echo Schedule::weekNumber($first); ?>. hét (<?php echo Schedule::weekNumber($first)%2 ? 'páratlan' : 'páros'; ?>)</span></div>
    <div class="week-scroll" tabindex="0" aria-label="Heti naptár, kis képernyőn oldalra görgethető">
      <div class="week-grid">
      <?php foreach ($week['days'] as $index=>$day) { if ($day===null) { ?><div class="day day-outside"><div class="day-heading"><?php echo $weekdays[$index+1]; ?><small class="d-block mt-2">Másik év hete</small></div></div><?php continue; } $date=$day['date']; $is_today=$date->format('Y-m-d')===$today->format('Y-m-d'); ?>
        <article class="day<?php echo $is_today ? ' is-today' : ''; ?><?php echo !$day['workday'] ? ' rest-day' : ''; ?>"<?php echo $is_today ? ' aria-current="date"' : ''; ?>>
          <div class="day-heading"><span><?php echo $weekdays[(int)$date->format('N')]; ?></span><?php if ($is_today) { ?><strong class="today-badge">MA</strong><?php } ?><time datetime="<?php echo $date->format('Y-m-d'); ?>"><?php echo $date->format('m. d.'); ?></time><small><?php echo $day['workday'] ? 'Munkanap' : 'Pihenőnap'; ?></small></div>
          <div class="day-items">
          <?php foreach ($day['items'] as $item) { ?>
            <details class="routine-card"><summary><?php echo routine_escape($item['title']); ?></summary><div class="routine-detail"><?php if ($item['description']!=='') { ?><p><?php echo nl2br(routine_escape($item['description'])); ?></p><?php } else { ?><p class="text-secondary">Nincs részletes leírás.</p><?php } ?><small><?php echo routine_escape(Schedule::label($item)); ?></small></div></details>
          <?php } ?>
          <?php if (!$day['items']) { ?><p class="empty-day">Nincs tervezett rutin</p><?php } ?>
          </div>
        </article>
      <?php } ?>
      </div>
    </div>
    <?php if ($week['position']==='past') { ?></details><?php } ?>
  </section>
  <?php } ?>
  <section id="routines" class="management-section">
    <div class="section-heading"><h2>Felvett rutinok <span class="count"><?php echo count($routines); ?></span></h2><a href="#routine-form">Új rutin hozzáadása</a></div>
    <?php if (!$routines) { ?><div class="empty-list">Még nincs felvett rutin. Add hozzá az elsőt az alábbi űrlapon.</div><?php } ?>
    <div class="routine-list">
    <?php foreach ($routines as $item) { ?>
      <div class="routine-row"><div class="routine-row-content"><h3><?php echo routine_escape($item['title']); ?></h3><p class="rule-label"><?php echo routine_escape(Schedule::label($item)); ?> · Sorrend: <?php echo (int)$item['sort_order']; ?></p><?php if ($item['description']!=='') { ?><p class="description"><?php echo nl2br(routine_escape($item['description'])); ?></p><?php } ?></div><div class="row-actions"><a class="btn btn-sm btn-outline-secondary" href="<?php echo url('/?edit='.(int)$item['id'].'#routine-form'); ?>">Szerkesztés</a><form action="<?php echo url('/routine/delete'); ?>" method="post" data-confirm="Biztosan törlöd ezt a rutint? A naptárból is eltűnik."><input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Törlés</button></form></div></div>
    <?php } ?>
    </div>
  </section>
  <section id="routine-form" class="management-section form-section">
    <h2><?php echo $form['id'] ? 'Rutin szerkesztése' : 'Új rutin'; ?></h2>
    <form action="<?php echo url('/routine/save'); ?>" method="post" id="routine-editor">
      <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><input type="hidden" name="id" value="<?php echo (int)$form['id']; ?>">
      <div class="row g-3">
        <div class="col-md-8"><label for="routine-title" class="form-label">Név</label><input id="routine-title" class="form-control" name="title" value="<?php echo routine_escape($form['title']); ?>" maxlength="200" placeholder="Pl. Reggeli rutin" required></div>
        <div class="col-md-4"><label for="sort-order" class="form-label">Megjelenési sorrend</label><input id="sort-order" class="form-control" type="number" name="sort_order" min="0" max="9999" value="<?php echo (int)$form['sort_order']; ?>" required><div class="form-text">A kisebb szám kerül előre.</div></div>
        <div class="col-12"><label for="routine-description" class="form-label">Leírás</label><textarea id="routine-description" class="form-control" name="description" rows="3" maxlength="15000" placeholder="Mit jelent ez a rutin? Pl. fogmosás, reggeli, készülődés."><?php echo routine_escape($form['description']); ?></textarea></div>
        <div class="col-md-6"><label for="routine-rule" class="form-label">Ismétlődés</label><select id="routine-rule" class="form-select" name="rule"><?php foreach (Schedule::rules() as $key=>$label) { ?><option value="<?php echo $key; ?>"<?php echo $form['rule']===$key ? ' selected' : ''; ?>><?php echo $label; ?></option><?php } ?></select></div>
        <div class="col-md-6" data-rules="weekly odd even quarterly"><label for="routine-weekday" class="form-label">A hét napja</label><select id="routine-weekday" name="weekday" class="form-select"><option value="0" data-quarter-only>Egész héten (időablak)</option><?php foreach ($weekdays as $key=>$label) { ?><option value="<?php echo $key; ?>"<?php echo (int)$form['weekday']===$key ? ' selected' : ''; ?>><?php echo $label; ?></option><?php } ?></select></div>
        <div class="col-md-6" data-rules="monthly halfyear yearly"><label for="routine-day" class="form-label">A hónap napja</label><input id="routine-day" class="form-control" type="number" name="day_number" min="0" max="31" value="<?php echo (int)$form['day_number']; ?>"><div class="form-text">Féléves rutinnál 0 = egész hónap. Rövidebb hónapban az utolsó nap.</div></div>
        <div class="col-md-6" data-rules="halfyear yearly"><label for="routine-month" class="form-label">Hányadik hónap?</label><input id="routine-month" class="form-control" type="number" name="month_number" min="1" max="12" value="<?php echo max(1,(int)$form['month_number']); ?>"><div class="form-text">Félévnél 1–6 (januártól és júliustól), évnél 1–12.</div></div>
        <div class="col-md-6" data-rules="quarterly"><label for="routine-week" class="form-label">A negyedév hányadik hete?</label><input id="routine-week" class="form-control" type="number" name="week_number" min="1" max="14" value="<?php echo max(1,(int)$form['week_number']); ?>"><div class="form-text">1. hét = a negyedév első 7 napja, 2. hét = a következő 7 nap stb. A 14. hét rövid, nem mindig létezik.</div></div>
        <div class="col-md-6" data-rules="season"><label for="routine-season" class="form-label">Évszak</label><select id="routine-season" name="season" class="form-select"><?php foreach (Schedule::seasons() as $key=>$label) { ?><option value="<?php echo $key; ?>"<?php echo (int)$form['season']===$key ? ' selected' : ''; ?>><?php echo $label; ?></option><?php } ?></select></div>
        <div class="col-12"><button class="btn btn-primary" type="submit">Rutin mentése</button><?php if ($form['id']) { ?> <a class="btn btn-outline-secondary" href="<?php echo url('/#routine-form'); ?>">Mégse</a><?php } ?></div>
      </div>
    </form>
  </section>
  <section id="calendar" class="management-section">
    <details><summary class="calendar-summary">Munkanapok és pihenőnapok kivételei</summary>
      <p class="text-secondary mt-3">Alapból hétfő–péntek munkanap, szombat–vasárnap pihenőnap. Az ünnepnapokat és az áthelyezett munkanapokat itt tudod felülírni.</p>
      <form class="row g-2 align-items-end mb-3" method="post" action="<?php echo url('/calendar'); ?>"><input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><div class="col-sm-4"><label for="override-date" class="form-label">Dátum</label><input class="form-control" type="date" name="date" id="override-date" required></div><div class="col-sm-4"><label for="override-type" class="form-label">Nap típusa</label><select class="form-select" id="override-type" name="is_workday"><option value="0">Pihenőnap / ünnepnap</option><option value="1">Munkanap</option></select></div><div class="col-sm-4"><button class="btn btn-outline-primary">Kivétel mentése</button></div></form>
      <?php foreach ($overrides as $date=>$workday) { ?><div class="override-row"><span><?php echo routine_escape($date); ?> · <?php echo $workday ? 'Munkanap' : 'Pihenőnap'; ?></span><form method="post" action="<?php echo url('/calendar'); ?>" data-confirm="Törlöd ezt a naptári kivételt?"><input type="hidden" name="_csrf" value="<?php echo $csrf; ?>"><input type="hidden" name="date" value="<?php echo routine_escape($date); ?>"><button name="remove" value="1" class="btn btn-sm btn-outline-danger">Törlés</button></form></div><?php } ?>
    </details>
  </section>
  <section class="management-section" aria-labelledby="year-weeks-heading">
    <h2 id="year-weeks-heading" class="mb-3">Hétszámozás az év elején</h2>
    <p class="text-secondary">Az 1. hét január 1-jén kezdődik és vasárnapig tart. Utána hétfőn indulnak a hetek. Az év utolsó hete december 31-én lezárul.</p>
    <?php for ($year=(int)$today->format('Y'); $year<=(int)$today->format('Y')+1; $year++) {
      $first=new DateTime($year.'-01-01'); $bounds=Schedule::weekBounds($first);
      $last=new DateTime(($year-1).'-12-31'); $last_bounds=Schedule::weekBounds($last);
      $last_number=Schedule::weekNumber($last);
    ?>
      <div class="mb-3">
        <h3 class="h6"><?php echo $year; ?> · <?php echo $year===(int)$today->format('Y') ? 'Idén' : 'Következő év'; ?></h3>
        <p class="mb-1">Az 1. hét <strong>páratlan</strong>: <strong><?php echo $first->format('Y. m. d.'); ?> (<?php echo $weekdays[(int)$first->format('N')]; ?>)</strong> – <?php echo $bounds[1]->format('Y. m. d.'); ?>.</p>
        <p class="mb-0 text-secondary">Az előző év utolsó hete: <?php echo $last_number; ?>. hét (<?php echo $last_number%2 ? 'páratlan' : 'páros'; ?>), <?php echo $last_bounds[0]->format('Y. m. d.').' – '.$last->format('Y. m. d.'); ?>.<?php if ($last_number%2) { ?> <strong>Az évváltásnál két páratlan hét követi egymást.</strong><?php } ?></p>
      </div>
    <?php } ?>
  </section>
  <p class="schedule-note">A naptár az aktuális szabályokat vetíti a múltba és a jövőbe. Az időablakos rutin az érintett időszak minden napján látszik. Nincs teljesítéskövetés.</p>
</div>
