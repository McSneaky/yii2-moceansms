Yii2 MoceanSMS
=================
Mocean SMS sending for Yii2

Based on mikk150/yii2-messentesms

[![Build Status](https://travis-ci.org/mcsneaky/yii2-moceansms.svg?branch=master)](https://travis-ci.org/mcsneaky/yii2-moceansms) [![codecov](https://codecov.io/gh/mcsneaky/yii2-moceansms/branch/master/graph/badge.svg)](https://codecov.io/gh/mcsneaky/yii2-moceansms)

Usage
-----

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'sms' => [
            'class' => 'mcsneaky\moceansms\Provider',
            'username' => 'myUsername',
            'password' => 'myPassword',
        ],
    ],
];
```

You can then send an SMS as follows:

```php
Yii::$app->sms->compose('Your awesome SMS')
     ->setFrom('Yii2')
     ->setTo('+15417543010')
     ->send();
```
