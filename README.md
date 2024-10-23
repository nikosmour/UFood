## About FoodServer

Food is a backend server to help a university with managing the restaurant entry

## Constants

All the variables depending on the app has located in the config folder or in enum structure.
From this rule are not include some message to the user

In the app.php has added also the available_locales that auto calculate the available langs (if
there isn't happened the config:case command)

## Installation

### Commands

#### command that no need to make

For auth command that hapend in the begging of the project you don't have to do anything because the files
has all ready upload to the git. I think it was the command.

````bash
php artisan ui vue --auth
````

To generate a js file with all the routes there. There
is also no need to do it because is auto genarate with composer install in the functionality

````bash
php artisan ziggy:generate
````

#### the rest of the app

````bash
php artisan app:command_run
````

THe above command will run all the bellow command

````bash
composer install
php artisan migrate
php artisan db:seed 
npm install 
npm run dev
php artisan clear-compiled
php artisan ide-helper:generate
php artisan ide-helper:models --write-mixin
php artisan ide-helper:meta
````

### explanation

This command will run all the necessary command to make the database generate fake data and make js and css

````bash
php artisan app:command_run
````

#### For fronted

To generate a vue file and transfer all the enums there

````bash
php artisan app:enum-to-vue-js 
````

install the packages

```bash
npm install 
````

#### For backed

install the packages

````bash
composer install 
````

make the database

```bash
php artisan migrate
php artisan db:seed
````

only for dev
IDE Helper Generator for Laravel

```bash
php artisan clear-compiled
php artisan ide-helper:generate
php artisan ide-helper:models --write-mixin
php artisan ide-helper:meta
````

## License

The FoodServer is open-sourced software without licensed yet


