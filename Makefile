#Makefile
lint:
	vendor/bin/phpcs --standard=PSR12 --colors -v src tests

test:
	composer exec --verbose phpunit tests