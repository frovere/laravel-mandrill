# Laravel Mandrill

## Installation

You can install the package via composer:

``` bash
$ composer require felrov/laravel-mandrill
```

## Usage

Update your `.env` file by adding your api key and set your mail driver to `mandrill`.
You can choose between `https` and `api` scheme.

```php
MAIL_MAILER=mandrill
MANDRILL_SCHEME=https | api
MANDRILL_SECRET=YOUR-API-KEY-HERE
```

You are ready to use mandrill through [Laravel](https://laravel.com/docs/8.x/mail)

> Remember, when using Mandrill the sending address used in your emails must be a [valid Sender Signature](https://mandrill.zendesk.com/hc/en-us/articles/205582267-About-SPF-and-DKIM) that you have already configured.

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
