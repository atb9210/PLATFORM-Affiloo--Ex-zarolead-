<p align="center"><img src="http://beta2.zarolead.com/images/full_logo.jpeg"></p>

<!--
<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>
-->

# ZaroLead

ZaroLead is a web application that follows you in every step of online sales:

- You can create landings and purchase forms
- You can run and manage ads on all the major platforms (Facebook, Google, ...)
- You can track all the sales
- You can views stats about sales, earnings, ROI, ...
- You can sale as an affiliate by integrating the affiliation networks you want to work with
- You can create, download and store invoices directly from advertising platforms

## Why ZaroLead

ZaroLead is a light yet powerful tool to bring your online business to the next level.

Take a look at the [demo](https://www.google.com/).

## Requirements

- PHP 8
- MySQL
- Composer
- npm

## How to install zarolead

First of all, you need to clone ZaroLead repository:
```
git clone https://github.com/driverfury/zarolead
```

Then you need to install all PHP and all Javascript packages with 
[composer](https://getcomposer.org/) and [npm](https://www.npmjs.com/) 
respectively.
```
composer install
npm install
```

Create you .env file by copying the example file zarolead.env.
```
cp zarolead.env .env
```
Then edit the .env file.<br>

When everything is setup correctly you need to install all the tables in the 
database and setup some package by running the following commands:
```
./migrate.sh
./make_storage_symlink.sh
```

Compile the frontend by running npm:
```
npm run prod
```
