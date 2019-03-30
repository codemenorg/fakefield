# Laravel Fake Field 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codemen/test.svg?style=flat-square)](https://packagist.org/packages/codemen/test)
[![Build Status](https://img.shields.io/travis/codemen/test/master.svg?style=flat-square)](https://travis-ci.org/codemen/test)
[![Quality Score](https://img.shields.io/scrutinizer/g/codemen/test.svg?style=flat-square)](https://scrutinizer-ci.com/g/codemen/test)
[![Total Downloads](https://img.shields.io/packagist/dt/codemen/test.svg?style=flat-square)](https://packagist.org/packages/codemen/test)

Hide original field name from end user. It will generate new field name(fake field) every time while you refresh your page. 

## Installation

You can install the package via composer:

```bash
composer require codemenorg/fakefield
```

## Usage
After installing the package run the following commend to generate `fakefield.php` config file. Here you have to define fake field prefix and Eloquent Model path.  
```
php artisan vendor:publish --provider=Codemen\FakeField\FakeFieldServiceProvider
```

Add `FakeFieldMiddleware` to `app\Http\Kernel.php` web middleware group section.

```php
protected $middlewareGroups = [
        'web' => [
            ...
            \Codemen\FakeField\FakeFieldMiddleware::class,
        ],
        ...
    ];
```

Inside your model you have to define which fields you want to change. 
```php
protected $fakeFields = ['first_name', 'last_name', 'email'];
```

Now you are ready to use fake field inside your form.

```html
<form action="">
    @csrf
    @fakeKey
    <!-- <input type="hidden" name="_fake_key" value="eyJpdiI6Ij...wOG9BPT0ifQ"> -->
    
    <input type="text" name="{{ fake_field('first_name')}}">
    <!-- <input type="text" name="fake_345"> -->
    <input type="text" name="{{ fake_field('last_name')}}">
    <!-- <input type="text" name="fake_53"> -->
    <input type="text" name="{{ fake_field('email')}}">
    <!-- <input type="text" name="fake_24"> -->
    
    <button type="submit">Submit</button>
    
</form>
```

Here `@fakeKey` blade directive will generate a fake key without fake field will not working properly. You can also use `fake_key()` helper function to generate fake key. 

`fake_field()` helper function will generate fake field. 

**If the field is not defined in Model then the function will return original field name.**



### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

To contribute you can create a pull request. But please check all functionality are working before creating pull request. We will publish your name on contribution list. 

### Security

If you discover any security related issues, please email codemenorg@gmail.com instead of using the issue tracker.

## Credits

- [Codemen](https://github.com/codemenorg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
