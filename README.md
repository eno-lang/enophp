# enophp

PHP library for parsing, loading and inspecting eno documents

## Installation

    composer require eno-lang/enophp

## Getting started

Create an eno document, for instance `intro.eno`:

```eno
Greeting: Hello World!
```

A minimal example to read this file with `enophp`:

```php
use Eno\Parser;

$input = file_get_contents('intro.eno');

$document = Parser::parse($input);

echo( $document->field('Greeting') );  // prints 'Hello World!'
```

## Complete documentation and API reference

See [archived.eno-lang.org/php/](https://archived.eno-lang.org/php/)

## Running the tests

Install [kahlan](https://github.com/kahlan/kahlan) as development dependency:

```bash
composer install
```

Run the tests:

```bash
./vendor/bin/kahlan
```

Note that the test suite has a high peak memory load at some point, which might,
depending on your system configuration, exceed your PHP maximum memory
threshold. If you run into this you can for instance increase the limit in your
`php.ini` and specify e.g. `memory_limit = 512M`.
