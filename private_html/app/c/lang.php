<?php
require_once dirname(__DIR__) . '/config/lang_hu.php';
require_once dirname(__DIR__) . '/config/lang_en.php';

/** Resolve a string constant using APP_LANG; unknown languages fall back to English. */
function lang($key)
{
  $language = defined('APP_LANG') ? APP_LANG : 'hu';
  $prefix = $language === 'hu' ? 'LANG_HU_' : 'LANG_EN_';
  $name = $prefix . $key;
  if (!defined($name)) {
    throw new InvalidArgumentException('Unknown language key: ' . $key);
  }
  return constant($name);
}
