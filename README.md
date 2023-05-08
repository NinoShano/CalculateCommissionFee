# Calculate Commission fee


> ### The app allows private and business clients to deposit and withdraw funds in multiple currencies. Clients may be charged a commission fee. All deposits are charged the same way. There are different calculation rules for withdraw of private and business clients.


----------

# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)

Alternative installation is possible without local dependencies relying on [Docker](#docker).

Clone the repository

    git clone https://github.com/NinoShano/CalculateCommissionFee.git

Switch to the repo folder

    cd laravel-realworld-example-app

Install all the dependencies using composer

    composer install

Start the local development server

    php artisan serve

Requirements for excel package
    
    PHP: ^7.2\|^8.0
    Laravel: ^5.8
    PhpSpreadsheet: ^1.21

## How to use

You can now access the server at http://localhost:8000. On the default route upload excel file. With button submit action, which calculates commission fees. 

