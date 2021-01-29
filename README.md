# CodeIgniter HMVC and ci-phpunit-test

* CodeIgniter 3.1.11
* codeigniter-modular-extensions-hmvc codeigniter-3.x
  * From <https://github.com/lemondropsarl/webase>
* PHPUnit 7.5.20
* ci-phpunit-test 1.0.x@dev

If you want to see PHP5 code, check out <https://github.com/kenjis/ci-hmvc-ci-phpunit-test/tree/php54>.

## Note

* codeigniter-modular-extensions-hmvc is popular but a very complex system, and is against CodeIgniter's basic design. It brings complexity to CodeIgniter.
* So to work CodeIgniter HMVC and ci-phpunit-test together is still under way. See <https://github.com/kenjis/ci-phpunit-test/issues/34>.
* If you can avoid to use it, I recommend not use it.
  * If you look for only modular system, see <https://github.com/kenjis/codeigniter-simple-module>.
  * If you want reusable *widgets*, you can find *A Simple Widget System* in the Book *[Practical CodeIgniter 3](https://leanpub.com/practicalcodeigniter3)*. Or see <https://github.com/kenjis/codeigniter-widgets>.

## Requirements

* PHP 7.2 or later
* composer

## Note to Use

* Please try to use `MX_Controller` instead of `CI_Controller`, if you get errors.

## How to Run Tests

~~~
$ git clone https://github.com/kenjis/ci-hmvc-ci-phpunit-test.git
$ cd ci-hmvc-ci-phpunit-test/
$ composer install
$ vendor/bin/phpunit -c application/tests/
~~~

## References

* https://github.com/bcit-ci/CodeIgniter
* https://github.com/kenjis/codeigniter-composer-installer
* https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
* https://github.com/sebastianbergmann/phpunit
* https://github.com/kenjis/ci-phpunit-test
