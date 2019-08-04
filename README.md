# Codeception Coverage Checker   [![Build Status](https://travis-ci.com/Nebulosar/CodeceptionCoverageChecker.svg?token=jQEU4f9yyAzUsjfU7pQ5&branch=master)][travis-build]
Extension for codeception. Can be used to fail tests if they are under the coverage threshold.

## What it does
CovergeChecker is an extension made for Codeception. You can let your build fail when your code coveage is not high enough using this simple extension. If code coverage checks are the only thing you need from an analyser, this is what you want. No need for expensive cloud analysers!

**Example**  
Take for example your build on Travis. By runing your tests with codeception you can see if your code still works. Now, you can also check if there is enough code covered by those tests! This repository is a living example of that.

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
    - Nebulosar\Codeception\CoverageChecker
```

Also make sure you enable the code coverage option of Codeception:
```
coverage:
  enabled: true
  check: lines
```

## Usage
To use this extension you need to add the `--coverage` command line option when you run your tests. It works just the same as when you use codeception to generate a coverage report for you. Enabling the `coverage` tag in your _codeception.yml_ file simply is not enough.

### Configuration options
All configuration for this plugin can be done under the `coverage` tag in your _codeception.yml_ file. Here is an example of all the options. They are explained below.
```
coverage:
  enabled: true
  check:
    classes:
      low_limit: 65
      high_limit: 85
    methods:
      low_limit: 65
      high_limit: 85
    lines:
      low_limit: 65
      high_limit: 85
```

**Enabled**  
This setting is a default Codeception setting which needs to be set to `true` when you want to use the coverage part of codeception.
At this moment, CoverageChecker also lisents to this setting to determine whether to check you coverage or not.

**Check**  
This is the extension setting. When you add this attribute you have three types you can add: **classes**, **methods** and **lines**.
This determines what you want to check. If you are only interested in line coverage you can simple use the line `check: lines` and the extension will work.

**Limit**
Each type of check has two attributes you may set. These are the **low_limit** and the **high_limit**.  
* When the code coverage is below the low limit, it will throw a `CodeCoverageException` and your build failes.  
* When the code coverage is below the high limit, it will give you a warning.  
* When the code coverage is above the high limit, it will give you a message that the coverage is above the limit and will be green. (success)

If you choose not to supply a limit, it will take the default which are 60% (low limit) and 80% (high limit). 

### Command line options
**Coverage option**  
The `--coverage` options is mandatory, but can also be replaced by any of the coverage options. Listed they are:
* `--coverage`
* `--coverage-xml`
* `--coverage-html`
* `--coverage-text`
* `--coverage-crap4j`
* `--coverage-phpunit`

**Colours**
Some people like things flat and take console output very serious. Because of this, support is added for the `--no-colors` option.
When using this option, output will be colourless.

## Support
When you find something that does not work like it should:  
When you find something that can be improved:  
When you think you are a neat open source programmer:  

Help improve this extension for all!
File an issue, the steps to reproduce and, if you can, open a pull request with the suggested fix!

## License 

MIT  
(see license page)

[travis-build]: https://travis-ci.com/Nebulosar/CodeceptionCoverageChecker
[codeception-extensions]: https://codeception.com/extensions
[xdebug-download]: https://xdebug.org/download.php
[xdebug-wizard]: https://xdebug.org/wizard.php
