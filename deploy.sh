#!/usr/bin/bash

composer install
composer update

if [ ! -d "public/mail" ]; then 
    ln -s "$PWD/storage/app/mailbox" "public/mailbox"
fi 

if [! -d "public/storage" ]; then
    ln -s "$PWD/storage/app/public" "public/storage"
fi 

if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "Fresh .env copied, please edit to make it work!"
fi

chmod -R 777 "$PWD/storage"

nvm install 12.10
nvm alias default 12.10
nvm use default

npm ci && npm run prod

php artisan migrate
