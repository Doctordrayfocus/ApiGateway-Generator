# LANG defines the programming language to be used.
# To use this variable (e.g make setup LANG=php)
LANG ?= php

# Commands
generate:
	 chmod -R 775 bin
	./bin/${LANG}/setup.sh
