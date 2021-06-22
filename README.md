# traccar_api

Configuration in `/app/conf/App.php` :

```php

"traccar" => [
    "host" => "http://.....",
    "token" => "......",
],

```

Database schema in `schema.sql`

Request:
```
GET /extensions/traccar_api/controller/traccar/[database]  
Content-Type: application/json
```