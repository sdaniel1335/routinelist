# Routinelist

Egyszerű ismétlődőrutinnaptár a VC Framework alapján, PHP 5.3+ és MySQL/MariaDB környezethez.

## Telepítés

1. Másold a `private_html/app/config/db-sample.php` fájlt `db.php` néven ugyanoda, és add meg az adatbázis adatait.
2. Importáld az `install.sql` fájlt egy új adatbázisba. Közös adatbázis esetén a meglévő `users` táblát megtartja. Ha `DB_PREFIX` értéket használsz, importálás előtt az SQL táblaneveit és hivatkozásait is egészítsd ki a prefixszel.
3. Apache és mod_rewrite mellett nyisd meg a modult. Belépés: `admin` / `admin`; a Jelszó oldalon változtasd meg.
4. Éles környezetben állítsd `APP_ENV` értékét `prod`-ra.

## Használat

A kezdőlap az előző, aktuális és következő hét napjait mutatja, hétfő–vasárnap oszlopokban. Az évhatáron csonka hetek jelennek meg, a másik év napjai üres helyek. A Múlt hét alapból csukva van, a Jövő hét halványabb, rámutatáskor vagy billentyűzetes fókusznál teljesen látható. A mai nap Budapest időzónája szerint kiemelt. Mobilon a hetek vízszintesen görgethetők. A rutin neve lenyitja a leírást. Nincs pipálás, teljesítéskövetés vagy csoportosítás.

A hetek alatt lehet rutint felvenni, szerkeszteni és megerősítés után törölni. A sorrendszám szabályozza a naptár és a lista sorrendjét, azonos számnál a felvétel sorrendje dönt. A rutinok és naptári kivételek felhasználónként külön tárolódnak. A MODULE, layout.php és topbar.php változatlan.

## Ismétlődés

- Naponta: mindennap, munkanapokon vagy pihenőnapokon.
- Hetente vagy A/B hetente, a hét kiválasztott napján. Az A/B ciklus fix kiindulópontja a 2026. január 1-jét tartalmazó hét (2025. december 29. – 2026. január 4.): ez A hét. Utána minden hétfőn vált, évváltáskor sincs újraindítás. A korábbi páratlan rutinok A, a páros rutinok B hetesként folytatódnak; az adatbázis belső odd/even kulcsai megmaradnak, nincs szükség migrációra. Az éves hétszámozás külön működik: az 1. hét január 1-jén kezdődik, az utolsó december 31-én zárul. Az idei és a következő január 1-jei hét A/B jelölése a lap alján látszik.
- Havonta, adott napon. Ha a nap nem létezik, a hónap utolsó napja érvényes.
- Negyedévente, az adott negyedév 1–14. hétblokkjában, opcionális hétköznappal. Az 1. hét a negyedév első 7 napja, nem az első naptári hét. A 14. blokk rövid, nem minden negyedévben létezik. Nap nélkül az egész időablak minden napján látszik.
- Félévente, a januárban/júliusban induló félév adott hónapjában. A nap 0 értéke egész hónapot jelent.
- Évente, adott hónap és nap szerint; február 29. nem szökőévben február 28.
- Évente egy évszak teljes időszakában: tavasz március–május, nyár június–augusztus, ősz szeptember–november, tél december–február.

A munkanap alapból hétfő–péntek. Az ünnepnapokat és munkaszombatokat a kezdőlap alján a naptári kivételeknél kell beállítani; nincs automatikusan letöltött ünnepnaptár.

A múlt és jövő a jelenlegi szabályok vetítése: szerkesztésük a múltbeli nézetet is módosítja, törlésük minden megjelenést eltávolít. Nem történeti napló.

## Ellenőrzés

PHP-val: `php tests/schedule.php`. Szintaxis: a projekt PHP fájljain `php -l`, JavaScript: `node --check public_html/js/app.js`.

## Nyelv

Az `APP_LANG` értéke a `private_html/app/config/app.php` fájlban `hu` vagy `en` lehet. A szótárak a `config/lang_hu.php` és `config/lang_en.php` fájlokban vannak, egyszerű szövegkonstansokkal: `define('LANG_HU_WEEK', 'hét');` és `define('LANG_EN_WEEK', 'week');`. Használat a kódban: `lang('WEEK')`. A `c/lang.php` csak a betöltést és az APP_LANG szerinti kiválasztást végzi. Ismeretlen nyelvkódnál angol a tartalék. A megoldás PHP 5.3-kompatibilis. A MODULE, a layout.php és a topbar.php továbbra is változatlan, az ezekbe rögzített feliratok is megmaradnak. A rutinok felhasználó által megadott neve és leírása nem fordítódik automatikusan.
