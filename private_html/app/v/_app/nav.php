<nav class="app-nav">
  <a class="brand" href="<?php echo url('/'); ?>">Routinelist</a>
  <?php if (auth_logged_in()) { ?>
  <div class="nav-actions"><span><?php echo htmlspecialchars(auth_username(),ENT_QUOTES,'UTF-8'); ?></span><a href="<?php echo url('/password'); ?>">Jelszó</a><form action="<?php echo url('/logout'); ?>" method="post"><input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(),ENT_QUOTES,'UTF-8'); ?>"><button type="submit" class="btn btn-sm btn-outline-secondary">Kilépés</button></form></div>
  <?php } ?>
</nav>
