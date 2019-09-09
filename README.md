[![Build Status](https://travis-ci.org/rmp-up/wp-fixtures.svg?branch=master)](https://travis-ci.org/rmp-up/wp-fixtures)
[![Coverage Status](https://coveralls.io/repos/github/rmp-up/wp-fixtures/badge.svg)](https://coveralls.io/github/rmp-up/wp-fixtures)

# WordPress Fixture Generator

> Expressive fixtures persistence in WordPress (and some plugins).

GDPR and fluent data is a PITA while developing solutions
to specific problems for your customers.
[Faker](https://github.com/fzaninotto/Faker)
and [Alice](https://github.com/nelmio/alice)
is already there for Symfony, Nette, Zend and now for WordPress to ...

* ... create data using wp-cli or plain PHP.
* ... generate **tons of pages, posts, comments, products
      and more** with just a few YAML-lines
* ... seed WordPress with **specific use-cases for testing**

Overall the goal is **simplicity** and **no time wasting crap** (for me and you)
which we achieve by using raw WordPress structures in the YAML instead of
reinventing the wheel.

By now we bring helper for:

* **WordPress** 4.5 - 5.2, e.g. posts, pages, user, options etc.
* **WooCommerce** 3.0 - 3.7, e.g. orders, products etc.

See below how a small Yaml-File can seed the database with tons of entries
or [read the documentation](https://github.com/rmp-up/wp-fixtures/releases)
to find out more about entities and possibilities.


## Install

Download or just

    composer install --dev rmp-up/wp-fixtures

We mostly require what Alice also needs
([see Packagist.org for more details](https://packagist.org/packages/rmp-up/wp-fixtures)):

* PHP
* WordPress

Optional:

* wp-cli 2, which eats YAML and seeds the DB for you

Tests expand continuously to cover a bigger range one day (see Travis CI).


## Usage

Lets define a simple page,
a post using faker
and plenty of comments within one yaml:

```yaml
\RmpUp\WordPress\Fixtures\Entity\Page:
  page_1:
    # Well known wp_insert_post() structure
    post_title: Imprint
    post_content:
      """
      Beth Doe
      Meestreet 42
      1337 Muskegon
      """
    meta_input:
      simple: 1
      flat: # will append like add_post_meta
      - penny
      - claire
      - emily
      - lucy
      complex: # will be serialized
        company: True TV
        best: Ellen


\RmpUp\WordPress\Fixtures\Entity\Post:
  post_{1..10}:
    post_title: '<sentence()>'
    post_content: '<realText()>'
      
  # Just three lines to make 10 pages with random content
  # see https://github.com/fzaninotto/Faker#formatters


\RmpUp\WordPress\Fixtures\Entity\Comment:
  comment_1:
    # Reference one of the above
    comment_post_ID: '@post_1'
    comment_content: 'Jose, what is going on?'

  # 100 comments randomly spread among posts
  comment_{2..102}:
    comment_post_ID: '@post_<numberBetween(1,10)>'
    comment_content: '<realText()>'
    comment_karma: '<numberBetween(5429,87645)>'
```

And many more:

* Users
* Terms
* WooCommerce
  * Products
  * Bundles
  * Orders

## License

Copyright 2019 Mike Pretzlaw (mike-pretzlaw.de)

Permission is hereby granted, free of charge,
to any person obtaining a copy of this software
and associated documentation files (the "Software"),
to deal in the Software without restriction,
including without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software,
and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
