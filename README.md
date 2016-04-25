[![Build Status](https://travis-ci.org/gintonicweb/users.svg)](https://travis-ci.org/gintonicweb/users)
[![codecov.io](https://codecov.io/github/gintonicweb/users/coverage.svg?branch=master)](https://codecov.io/github/gintonicweb/users?branch=master)

## Warning

Do not use, work in progress

# Users plugin for CakePHP

Fully featured users plugin based on:  
- admad/jwt-authenticate
- foc/crud-users
- muffin/tokenize
- muffin/trash

## Install instructions

via composer:
```
composer require gintoniccms/users:dev-master
```

run the migration
```
bin/cake migrations migrate -p Users
```

in bootstrap.php
```
Plugin::load('Users', ['routes' => true, 'bootstrap' => 'true']);
```

Add the following auth config to your AppController
```
todo
```
