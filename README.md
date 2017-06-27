# phalconphp-dbsessionadapter

Phalcon PHP - Database Session adapter using a Model.

# Installation

1) Copy Code into Respective Folders

 - /app/Library/* 
 - /app/Models/*

2) Database Table

```sql
CREATE TABLE `sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `created_at` (`created_at`),
  KEY `modified_at` (`modified_at`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
```

3) Setup Service
```php
$di->set('session', function () use ($oConfig) {

		// Sessions
		return new DbSessionAdapter(array(
			'name' => 'myawesomeapplication',
			'domain' => 'myawesomeapplication.com',
			'secure' => 1,
			'use_cookies' => 1,
			'hash' => 'sha256',
			'lifetime' => 0
		));

	});
```

4) Use Like Regular Phalcon Sessions

```php
$this->session->set('enjoy_our_code', 'ok');
```

# License
[MIT License](LICENSE)

# Author
[Matthew R. Miller](https://github.com/mattrmiller)
