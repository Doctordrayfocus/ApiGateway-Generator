# TYPE defines the programming language to be used.
# To use this variable (e.g make setup TYPE=php)
TYPE ?= php

# Commands
generate:
	 chmod -R 775 bin
	./bin/setup.sh ${TYPE}
