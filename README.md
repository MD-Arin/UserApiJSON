## Introduction

A quick laravel API that implements CRUD on a Users database. However API authentication is not implemented but is added.

## Installation
  - Clone or download the repository and navigate to the project folder
  - Make sure you have composer installed
  - Open a terminal or command line in the folder directory
  - Run the following commands
    ```
    composer install
    ```
    Optional
    ```
    npm install
    ```
    ### NOTE: 
    * You may need to run these commands as root or administrator
 ## Configuration
 - Install Passport
    ```
    composer require laravel/passport
    ```
 - Register Passport as a laravel provider in ```config\app.php```
    ```
    Laravel\Passport\PassportServiceProvider::class,
    ```
 - Migrate the tables to the database
    ```
    php artisan migrate
    ```
 - Generate passport encryption keys
    ```
    php artisan passport:install
    ```
    ### Important
    * Remember to create your own ```.env``` file
    * Generate an application key using: 
    ```
    php artisan key:generate
    ```
    
 

