# Codeception Coverage Checker   [![Build Status](https://travis-ci.com/Nebulosar/CodeceptionCoverageChecker.svg?token=jQEU4f9yyAzUsjfU7pQ5&branch=master)](https://travis-ci.com/Nebulosar/CodeceptionCoverageChecker)
Extension for codeception. Can be used to fail tests if they are under the coverage threshold.

## What it does
CovergeChecker is an extension made for Codeception. You can let your build fail when your code coveage is not high enough using this simple extension. If coverage checks are the only thing you need from an analyser, this is what you want. No need for expensive cloud analysers!


## Install

What you need:  
**Codeception**  
This is pretty obvious hence it is an Codeception extension. See Codeception for more info on [extensions][codeception-extensions]

**XDebug**  
Used for generating the code coverage report. See the [XDebug download page][xdebug-download] to find your installation. If you are unsure which download you need, you could use the [XDebug wizard][xdebug-wizard] to find the right one.

To install using composer:

```
composer require nebulosar/codeception-coverage-checker --dev
```

Simple add the CoverageReporter to the _codeception.yml_ file:
```
extensions:
  enabled:
    - Nebulosar\Codeception\CoverageChecker\CoverageReporter
```

Also make sure you enable the code coverage option of Codeception:
```
coverage:
  enabled: true
```

## Usage
To use this extension you need to add the `--coverage` command line option to your run 


## License 

MIT  
(see license page)


[codeception-extensions]: https://codeception.com/extensions
[xdebug-download]: https://xdebug.org/download.php
[xdebug-wizard]: https://xdebug.org/wizard.php
