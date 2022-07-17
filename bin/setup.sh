#!/usr/bin/env bash

# variables
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd );

DIR_ROOT="${DIR/\/bin/\/php}";


echo "Install laravel lumen";

composer create-project --prefer-dist laravel/lumen .

echo "Install required packages"

composer require darkaonline/swagger-lume flipbox/lumen-generator fruitcake/laravel-cors illuminate/redis laravel/lumen-framework michaelachrisco/readonly predis/predis

echo "Add config files"
rm -r ${DIR}/config
cp ${DIR_ROOT}/config ${DIR}

echo "Add app.php"
rm -r ${DIR}/bootstrap
cp ${DIR_ROOT}/bootstrap ${DIR}

echo "Add Traits"
rm -r ${DIR}/app/Traits
cp ${DIR_ROOT}/Traits ${DIR}/app

echo "Add Datasource"
rm -r ${DIR}/app/Datasource
cp ${DIR_ROOT}/Datasource ${DIR}/app

echo "Add Jobs"
rm -r ${DIR}/app/jobs
cp ${DIR_ROOT}/jobs ${DIR}/app

echo "Add Services"
rm -r ${DIR}/app/Services
cp ${DIR_ROOT}/Services ${DIR}/app

echo "Add Example Models"
rm -r ${DIR}/app/Models
cp ${DIR_ROOT}/Models ${DIR}/app

echo "Add Example Controllers"
rm -r ${DIR}/app/Http/Controllers
cp ${DIR_ROOT}/Controllers ${DIR}/app/Http


echo "Templates setup successfully";

