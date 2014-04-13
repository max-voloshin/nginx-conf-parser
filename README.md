[![Build Status](https://travis-ci.org/max-voloshin/nginx-conf-parser.svg?branch=master)](https://travis-ci.org/max-voloshin/nginx-conf-parser)

Goal
====
Show full (parsed) nginx config for further analysis

Usage
=====

Show config:

``./nginx-conf-parser /etc/nginx/nginx.conf``

Find all server names:

``./nginx-conf-parser /etc/nginx/nginx.conf | grep -E "\sserver_name\s"``

Installation
============

Via [Composer](https://getcomposer.org/download/):

``composer create-project max-voloshin/nginx-conf-parser /path/to/ dev-master``

Run tests
=========

``phpunit``

License
=======
MIT