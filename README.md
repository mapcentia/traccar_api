# traccar_api

Configuration in `/app/conf/App.php` :

```php

"traccar" => [
    "db1" => [
        "baseUri" => "http://myhost1.com:80",
        "host" => "myhost1.com",
        "token" => "......",
    ],
    "db2" => [
        "baseUri" => "http://myhost2.com:80",
        "host" => "myhost2.com",
        "token" => "......",
    ],
],

```

Database schema in `model/schema.sql`

Request:

```
GET /extensions/traccar_api/controller/traccar/[database]  
Content-Type: application/json
```