## For Nette Framework

Autoregisters extensions downloaded via Composer

## License

New BSD

## Dependencies

- Nette Framework 2.0.4

## Installation

1. Get the source code from Github.
2. Register as compiler extension.

```php
$configurator->onCompile[] = function ($configurator, $compiler) {
	$compiler->addExtension('composer', new VojtechDobes\ComposerExtension);
};
```

Since now, all compiler extensions acquired via Composer are automatically registered.

## Options

You may override default naming with this syntax:

```
composer:
	alias:
		dg/dibi: dibi
		kdyby/redis-extension: redis
```

Some common packages are already aliased, see source code for complete list. If you don't want to use these default aliases, it is also possible:

```
composer:
	default: no
```

Aliases listed in config section always override default ones.

You may also use different naming strategy:

```
composer:
	default: short
```

If alias is not defined, it will use the second part of package name.

- `dg/dibi` will become `dibi`
- `kdyby/redis-extension` will become `redis-extension`
