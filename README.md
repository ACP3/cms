# ACP3 CMS

The ACP3 CMS is a highly customizable and easy to use Web Content Management System based on PHP and MySQL.

## Features

-   Based on modular components
-   Low barriers for disabled people
-   Automatic generation of breadcrumbs to improve usability even further
-   Secure: protection against SQL-injections, salted passwords, complete input validation...
-   Wordlike text input with the WYSIWYG-Editors CKEditor and TinyMCE
-   Easy to customize: Layout based on html templates, which can be styled with CSS
-   Search engine optimized URIs
-   Access Control Lists, which allow fine grained permissions

If you want to find out more information about the features and the requirements, just go to the official [Project-Website](http://www.acp3-cms.net).

## Installation

To install the current development version directly from github, you have to do the following steps:

Clone the repository into a new directory:

```sh
$ git clone https://gitlab.com/ACP3/cms.git <directory>
```

Make sure that you have composer already installed.

If so, execute the following command from the projects root directory:

```sh
$ composer install
```

Make sure that you have installed node.js with npm and gulp-cli globally.

If so, execute the following command from the projects root directory, to install the necessary frontend development dependencies:

```sh
$ npm install
```

## Contribute

Contributions to the ACP3 CMS are always welcome. Here is how you can contribute to ACP3:

-   [Submit bugs](https://gitlab.com/ACP3/cms/issues) and help us verify fixes
-   [Submit pull requests](https://gitlab.com/ACP3/cms/merge_requests) for bug fixes, features and discuss existing proposals

Please refer to our [Contribution guidelines](https://gitlab.com/ACP3/cms/blob/master/CONTRIBUTING.md) for more details.

## Coding Style Guidelines

### PHP

We are using the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) Coding Style for all PHP files.

## License

This project is licensed under the terms of the [GPL 2.0+](https://gitlab.com/ACP3/cms/blob/master/LICENSE).
