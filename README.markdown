Szakdolgozat
============

A dolgozat új verzióit a `szakdolgozat.pdf` fájl frissítésével teszem közzé.

Keretrendszer
=============

Funkcionalitás
--------------

Jelenleg az alábbi komponensek elérhetők:

* osztálybetöltő
* konfiguráció (php, ini, xml formátumok támogatásával)
* naplózás (stdout illetve fájl kimenet)

Rendszerkövetelmények
---------------------

* PHP 5.3.x (ajánlott 5.3.5)
* Bármilyen kompatibilis web szerver illetve operációs rendszer
* A tesztek futtatásához: `PHPUnit`
* A kód-lefedettség jelentésekhez: `Xdebug` PHP-kiegészítés

`PHPUnit` a `pear` PHP-csomagkezelő segítségével telepíthető az alábbi parancsok kiadásával:

    pear channel-discover pear.phpunit.de
    pear channel-discover components.ez.no
    pear channel-discover pear.symfony-project.com
    pear channel-discover pear.php-tools.net
    pear install phpunit/PHPUnit
    pear install pat/vfsStream-beta

`Xdebug` letöltés és telepítés:
`http://www.xdebug.org/`

A tesztek futtatásához a `framework` könyvtárból a `phpunit` parancsot kell kiadni.
A kód-lefedettség jelentéseket a `framework/coverage/` könyvtárba készíti, ha érzékeli az `Xdebug` jelenlétét.

Példa alkalmazás
================

A keretrendszer elkészülte után, illetve vele párhuzamosan fejlesztem le, rendszerkövetelményei lényegében azonosak lesznek a keretrendszerével.
