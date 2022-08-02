#Makefile
install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 --colors -v src tests

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer --verbose exec phpunit tests -- --coverage-clover build/logs/clover.xml