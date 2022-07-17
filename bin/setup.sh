#!/usr/bin/env bash

# variables
LANG=$1
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd );

DIR_ROOT="${DIR/\/bin}";


if [[ $LANG == "php" ]]
then
	echo "Install laravel lumen";
	composer create-project --prefer-dist laravel/lumen ${DIR}/temp

	cp -a ${DIR}/temp/. ${DIR_ROOT}
	rm -r ${DIR}/temp

	echo "Install required packages"

	composer require darkaonline/swagger-lume flipbox/lumen-generator fruitcake/laravel-cors illuminate/redis laravel/lumen-framework michaelachrisco/readonly predis/predis

	echo "Add config files"
	cp -r ${DIR}/php/config ${DIR_ROOT}

	echo "Add app.php"
	rm -r ${DIR_ROOT}/bootstrap
	cp -r ${DIR}/php/bootstrap ${DIR_ROOT}

	echo "Add Traits"
	cp -r ${DIR}/php/Traits ${DIR_ROOT}/app

	echo "Add Datasource"
	cp -r ${DIR}/php/Datasource ${DIR_ROOT}/app

	echo "Add Jobs"
	rm -r ${DIR_ROOT}/app/Jobs
	cp -r ${DIR}/php/Jobs ${DIR_ROOT}/app

	echo "Add Services"
	cp -r ${DIR}/php/Services ${DIR_ROOT}/app

	echo "Add Example Models"
	rm -r ${DIR_ROOT}/app/Models
	cp -r ${DIR}/php/Models ${DIR_ROOT}/app

	echo "Add Example Controllers"
	rm -r ${DIR_ROOT}/app/Http/Controllers
	cp -r ${DIR}/php/Controllers ${DIR_ROOT}/app/Http

	echo "Remove redundant files"
    rm -r ${DIR}/php

	echo "Templates setup successfully";
fi


