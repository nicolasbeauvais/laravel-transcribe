# Laravel Transcribe

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nicolasbeauvais/laravel-transcribe.svg?style=flat-square)](https://packagist.org/packages/nicolasbeauvais/laravel-transcribe)
[![Build Status](https://img.shields.io/travis/nicolasbeauvais/laravel-transcribe/master.svg?style=flat-square)](https://travis-ci.org/nicolasbeauvais/laravel-transcribe)
[![Quality Score](https://img.shields.io/scrutinizer/g/nicolasbeauvais/laravel-transcribe.svg?style=flat-square)](https://scrutinizer-ci.com/g/nicolasbeauvais/laravel-transcribe)
[![Total Downloads](https://img.shields.io/packagist/dt/nicolasbeauvais/laravel-transcribe.svg?style=flat-square)](https://packagist.org/packages/nicolasbeauvais/laravel-transcribe)

Transcribe is a language files manager in your artisan console, it helps you search, update, add, and remove
translation lines with ease. Taking care of a multilingual interface is not a headache anymore.

## Acknowledgment

Laravel Transcribe has been hard-forked from
[themsaid/laravel-langman](https://github.com/themsaid/laravel-langman) in order
to maintain and improve the existing library.

## Installation

Begin by installing the package through Composer. Run the following command in your terminal:

```
$ composer require nicolasbeauvais/laravel-transcribe
```

Once done, add the following line in your providers array of `config/app.php`:

```php
NicolasBeauvais\Transcribe\TranscribeServiceProvider::class
```

This package has a single configuration option that points to the `resources/lang` directory, if only you need to change
the path then publish the config file:

```
php artisan vendor:publish --provider="NicolasBeauvais\Transcribe\TranscribeServiceProvider"
```

## Usage

### Showing lines of a translation file

```
php artisan transcribe:show users
```

You get:

```
+---------+---------------+-------------+
| key     | en            | nl          |
+---------+---------------+-------------+
| name    | name          | naam        |
| job     | job           | baan        |
+---------+---------------+-------------+
```

---

```
php artisan transcribe:show users.name
```

Brings only the translation of the `name` key in all languages.

---

```
php artisan transcribe:show users.name.first
```

Brings the translation of a nested key.

---

```
php artisan transcribe:show package::users.name
```

Brings the translation of a vendor package language file.

---

```
php artisan transcribe:show users --lang=en,it
```

Brings the translation of only the "en" and "it" languages.

---

```
php artisan transcribe:show users.nam -c
```

Brings only the translation lines with keys matching the given key via close match, so searching for `nam` brings values for
keys like (`name`, `username`, `branch_name_required`, etc...).

In the table returned by this command, if a translation is missing it'll be marked in red.

### Finding a translation line

```
php artisan transcribe:find 'log in first'
```

You get a table of language lines where any of the values matches the given phrase by close match.

### Searching view files for missing translations

```
php artisan transcribe:sync
```

This command will look into all files in `resources/views` and `app` and find all translation keys that are not covered in your translation files, after
that it appends those keys to the files with a value equal to an empty string.

### Filling missing translations

```
php artisan transcribe:missing
```

It'll collect all the keys that are missing in any of the languages or has values equals to an empty string, prompt
asking you to give a translation for each, and finally save the given values to the files.

### Translating a key

```
php artisan transcribe:trans users.name
php artisan transcribe:trans users.name.first
php artisan transcribe:trans users.name --lang=en
php artisan transcribe:trans package::users.name
```

Using this command you may set a language key (plain or nested) for a given group, you may also specify which language you wish to set leaving the other languages as is.

This command will add a new key if not existing, and updates the key if it is already there.

### Removing a key

```
php artisan transcribe:remove users.name
php artisan transcribe:remove package::users.name
```

It'll remove that key from all language files.

### Renaming a key

```
php artisan transcribe:rename users.name full_name
```

This will rename `users.name` to be `users.full_name`, the console will output a list of files where the key used to exist.

## Notes

`transcribe:sync`, `transcribe:missing`, `transcribe:trans`, and `transcribe:remove` will update your language files by writing them completely, meaning that any comments or special styling will be removed, so I recommend you backup your files.
