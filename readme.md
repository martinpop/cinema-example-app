Cinema Example App
==================

Example MVP application in the [Nette Framework](https://nette.org/)
with [jQuery](https://jquery.com/), [Bootstrap 3](http://getbootstrap.com/),
[Font Awesome](http://fontawesome.io/) and [Less.js](http://lesscss.org/).

Live demo at [example.martinpop.cz](http://example.martinpop.cz/).


Installation
------------
	
Requirements are described in [composer.json](https://github.com/martinpop/cinema-example-app/blob/master/composer.json).

1. Download application and copy into your web server.
2. Go to your application directory and run `composer install`
3. Make directories `temp/` and `log/` writable, file `app/model/data/cinema-example.sqlite` too.


Web Server Setup
----------------

The simplest way to get started is to start the built-in PHP server in the root directory of your project:

		php -S localhost:8000 -t www

Then visit `http://localhost:8000` in your browser to see the home page.

For Apache or Nginx, setup a virtual host to point to the `www/` directory of the project and you
should be ready to go.

It is CRITICAL that whole `app/`, `log/` and `temp/` directories are not accessible directly
via a web browser. See [security warning](https://nette.org/security-warning).


Requirements
------------

PHP 5.6.4 or higher and [Composer](https://getcomposer.org/).
