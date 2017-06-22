# SendGrid FuelPHP

This is a mail driver for SendGrid of Fuelphp.

Currently you can simply send e-mail.

## Usage

```json
T.B.D.
```

```php email.php
return [
    'defaults' => [
        'driver' => 'sendgrid',
            'sendgrid' => [
            'key' => 'SG.your-api-key'
        ],
    ],
];
```

```php
$mail = \Email::forge();
$mail->to($to, $to_name)
    ->subject($subject)
    ->body($body)
    ->send();
```

## License

MIT.
