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
- Hetente vagy páros/páratlan naptári hétszám szerint, a hét kiválasztott napján. Az 1. hét január 1-jén kezdődik, vasárnapig tart; utána hétfőn indulnak a hetek. Az utolsó hét december 31-én lezárul. Évváltáskor két páratlan hét is követheti egymást.
- Havonta, adott napon. Ha a nap nem létezik, a hónap utolsó napja érvényes.
- Negyedévente, az adott negyedév 1–14. hétblokkjában, opcionális hétköznappal. Az 1. hét a negyedév első 7 napja, nem az első naptári hét. A 14. blokk rövid, nem minden negyedévben létezik. Nap nélkül az egész időablak minden napján látszik.
- Félévente, a januárban/júliusban induló félév adott hónapjában. A nap 0 értéke egész hónapot jelent.
- Évente, adott hónap és nap szerint; február 29. nem szökőévben február 28.
- Évente egy évszak teljes időszakában: tavasz március–május, nyár június–augusztus, ősz szeptember–november, tél december–február.

A munkanap alapból hétfő–péntek. Az ünnepnapokat és munkaszombatokat a kezdőlap alján a naptári kivételeknél kell beállítani; nincs automatikusan letöltött ünnepnaptár.

A múlt és jövő a jelenlegi szabályok vetítése: szerkesztésük a múltbeli nézetet is módosítja, törlésük minden megjelenést eltávolít. Nem történeti napló.

## Ellenőrzés

PHP-val: `php tests/schedule.php`. Szintaxis: a projekt PHP fájljain `php -l`, JavaScript: `node --check public_html/js/app.js`.
