# SendGrid FuelPHP

This is a mail driver for SendGrid of Fuelphp.

Currently you can simply send e-mail.

## Usage

Add to composer.json.
```json
"require": {
    "koudenpa/sendgrid-fuelphp": "dev-master"
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/7474/sendgrid-fuelphp"
    }
],
```

Add to `config.php`.
```php
...
    'always_load' => array(
        'packages' => array(
...
            'email',
            'sendgrid-fuelphp',
        ),
```

Configure `email.php`.
```php email.php
return array(
    'defaults' => array(
        'driver' => 'sendgrid',
            'sendgrid' => array(
            'key' => 'SG.your-api-key'
        ),
    ),
);
```

Call.
```php
$mail = \Email::forge();
$mail->to($to, $to_name)
    ->subject($subject)
    ->body($body)
    ->send();
```

## License

MIT.
