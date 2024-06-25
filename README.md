# Yii2 Time tracker

Yii 2 module for time tracking. 

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-time-tracker/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-time-tracker)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-time-tracker/license)](https://packagist.org/packages/zakharov-andrew/yii2-time-tracker)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

The time tracking module for the Yii2 framework is a comprehensive solution for monitoring time spent on various tasks within projects. It allows users to easily start, stop, and record time intervals for specific tasks, as well as provides detailed reports on time spent.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require zakharov-andrew/yii2-time-tracker
```
or add

```
"zakharov-andrew/yii2-time-tracker": "*"
```

to the ```require``` section of your ```composer.json``` file.

Subsequently, run

```
./yii migrate/up --migrationPath=@vendor/zakharov-andrew/yii2-time-tracker/migrations
```

in order to create the settings table in your database.

Or add to console config

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@console/migrations', // Default migration folder
                '@vendor/zakharov-andrew/yii2-time-tracker/src/migrations'
            ]
        ]
        // ...
    ]
    // ...
];
```

## License

**yii2-time-tracker** it is available under a MIT License. Detailed information can be found in the `LICENSE.md`.
