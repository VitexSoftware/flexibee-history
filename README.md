FlexiBee Historie
================

Udržuje historii změn v evidencích FlexiBee.

Jak to funguje
--------------

Nejprve se zaregistruje ve FlexiBee webhook, který příjmá veškeré změny. 
Když je změna oznámena, skript si "sáhne" do FlexiBee a vytáhne celý záznam,
který uloží do repozitáře.

Tohoto repozitáře je možné se dotazovat na předchozí podobu záznamů a z ní získat sadu změn.    


Jak se zeptám na změny
----------------------

Tento příklad zobrazí které sloupečky se změnily při poslední editaci položky ceníku číslo 625

```php
    $historik = new \FlexiPeeHP\History\History(625,
        ['evidence' => 'cenik', 'mirror-dir' => $config['mirror-dir']]);
    $change = $historik->getLastDataChange(1);
    print_r($change);
```


Požadavky
------------

1) FlexiBee server s povoleným ChangesAPI
2) php
3) git


Použití
-------

1) upravit konfigurační soubor. **localhost.json** je ukázka
2) z browseru: **setup.php** připraví repozitář GIT/Mongo a zaregistruje webhook do FlexiBee
3) **sudo -H -u www-data bash -c 'php -f mirror.php'** načte výchozí stav FlexiBee do repozitáře změn GIT/MONGO
4) již je možné provádět úpravy ve flexibee.
5) example.php vypíše poslední zmenu v něm specifikovaného objektu - evidence/id

