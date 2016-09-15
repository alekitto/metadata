# Metadata management library in PHP

[![Build Status](https://travis-ci.org/alekitto/metadata.svg?branch=master)](https://travis-ci.org/alekitto/metadata)
[![Code Coverage](https://scrutinizer-ci.com/g/alekitto/metadata/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alekitto/metadata/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alekitto/metadata/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alekitto/metadata/?branch=master)

## Overview

This library provides utilities for metadata loading, management and retrieval for PHP classes, methods and properties.

## Installation

Install with composer

```bash
$ composer require kcs/metadata
```

## Usage

A metadata factory responsible for retrieving metadatas for a given class.  
To create a metadata factory you can implement `MetadataFactoryInterface`
on your own class or extend the `AbstractMetadataFactory`.

```php
use Kcs\Metadata\Factory\MetadataFactoryInterface;

class Factory implements MetadataFactoryInterface
{
    public function getMetadataFor($class)
    {
        ...
    }
}
```

```php
use Kcs\Metadata\Factory\AbstractMetadataFactory;

class Factory extends AbstractMetadataFactory
{
    protected function createMetadata(\ReflectionClass $class)
    {
        return new MyClassMetadata($class);
    }
}
```


This allows you to specify which implementation of `ClassMetadataInterface`
will be used for your metadata simply extending the `createMetadata` method.

If you extend the `AbstractMetadataFactory` class (or use `MetadataFactory` class
which creates a `ClassMetadata` instance for class metadatas), you have to create
your metadata loader class implementing `LoaderInterface`.

```php
use Kcs\Metadata\Loader\LoaderInterface;

class Loader implements LoaderInterface
{
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        ...
    }
}
```

If more than one source is available for your metadatas (Annotations, YAMLs, XMLs,
etc.) you can use the `ChainLoader` class, adding your loaders to it.

## Validation

When a metadata is loaded the factory `validate` method is called with the newly loaded
metadata as argument and the `kcs_metadata.metadata_loaded` event is dispatched
(if the event dispatcher is present).  
You can extend `validate` or listen for the metadata loaded event and check 
for metadata validity. If a validation error occurs you have to throw an
`InvalidMetadataException`.

## Metadata classes

You can extend the provided classes `ClassMetadata`, `MethodMetadata` and `PropertyMetadata`
to build your metadata informations.  
By default all the public properties are serialized in cache (if cache is
present). You can customize this behaviour by exteding the `__sleep` method
of the metadata classes, returning an array of serialized properties.

## License

This library is released under the MIT license

## Contributions

Contributions are always welcome.
Feel free to open an issue or submit a PR to improve this library.
