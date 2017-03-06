FlexiBee Historie
================


Požadavky
------------

1) FlexiBee server
2) php
3) git


Použití
-------

1) upravit konfigurační soubor. localhost.json je ukázka
2) setup.php připraví repozitář GIT/Mongo a zaregistruje webhook do FlexiBee
3) mirror.php načte výchozí stav FlexiBee do repozitáře změn GIT/MONGO
4) již je možné provádět úpravy ve flexibee.
5) example.php vypíše poslední zmenu v něm specifikovaného objektu - evidence/id

