# enophp

PHP implementation of the eno library specification

Development Status: **Approaching mid-november 2018 release**

## Installation

Release pending

## Getting started

Create an eno document, for instance `intro.eno`:

```eno
Greeting: Hello World!
```

A minimal example to read this file with `enophp`:

```php
$input = file_get_contents('intro.eno');

$document = Eno\Parser::parse($input);

echo( $document->field('Greeting') );  // prints 'Hello World!'
```
