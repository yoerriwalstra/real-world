# ![Laravel RealWorld Example App](.github/readme/logo.png)

[![RealWorld: Backend](https://img.shields.io/badge/RealWorld-Backend-blueviolet.svg)](https://github.com/gothinkster/realworld)
<!-- [![Tests: status](https://github.com/yoerriwalstra/real-world/actions/workflows/tests.yml/badge.svg)](https://github.com/yoerriwalstra/real-world/actions/workflows/tests.yml) -->
<!-- [![Coverage: percent](https://codecov.io/gh/yoerriwalstra/real-world/branch/main/graph/badge.svg)](https://codecov.io/gh/yoerriwalstra/real-world) -->
<!-- [![Static Analysis: status](https://github.com/yoerriwalstra/real-world/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/yoerriwalstra/real-world/actions/workflows/static-analysis.yml) -->
<!-- [![License: MIT](https://img.shields.io/badge/License-MIT-yellowgreen.svg)](https://opensource.org/licenses/MIT) -->

> Example of a PHP-based Laravel application containing real world examples (CRUD, auth, advanced patterns, etc) that adheres to the [RealWorld](https://github.com/gothinkster/realworld) API spec.

This codebase was created to demonstrate a backend application built with [Laravel framework](https://laravel.com/) including RESTful services, CRUD operations, authentication, routing, pagination, and more.

We've gone to great lengths to adhere to the **Laravel framework** community style guides & best practices.

For more information on how to this works with other frontends/backends, head over to the [RealWorld](https://github.com/gothinkster/realworld) repo.

## How it works

The API is built with [Laravel](https://laravel.com/), making the most of the framework's features out-of-the-box.

## Getting started

The preferred way of setting up the project is using [Laravel Sail](https://laravel.com/docs/sail),
for that you'll need [Docker](https://docs.docker.com/get-docker/) under Linux / macOS (or Windows WSL2).

### Installation

Clone the repository and change directory:

    git clone https://github.com/yoerriwalstra/real-world.git
    cd real-world

Install dependencies (if you have `composer` locally):

    composer create-project

Alternatively you can do the same with Docker:

    docker run --rm -it \
        --volume $PWD:/app \
        --user $(id -u):$(id -g) \
        composer create-project

Start the containers with PHP application and MySQL database:

    ./vendor/bin/sail up -d

(Optional) Configure a Bash alias for `sail` command:

    alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

Migrate the database with seeding:

    sail artisan migrate --seed

## Usage

The API is available at `http://localhost:80/api` (You can change the `APP_PORT` in `.env` file).

### Run tests

    sail artisan test

## Contributions

Feedback, suggestions, and improvements are welcome, feel free to contribute.
