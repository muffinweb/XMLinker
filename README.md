# XMLinker
Gelir Idaresi Baskanligi XML -> XHTML Donusturme
### e-Fatura, e-Arşiv XML'ini HTML içeriğe dönüştürme

```php
require 'XMLinker.php';

XMLinker::preview('xmlfile.xml');

// Eger ciktiyi degiskene atamak isterseniz
XMLinker::preview('xmlfile.xml', true);

```
