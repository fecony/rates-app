# Rates

App to fetch, store and display rates from bank.lv

## Prerequisites

Things you will need:

-   [Docker](https://docs.docker.com/get-docker/)

### Getting Started

Clone the project

```bash
git clone git@github.com:Fecony/rates_app.git
```

Go to the project directory

```bash
cd rates_app
```

Copy .env.example file to .env on the root folder.

```bash
cp .env.example .env
```

### Running app ~ Docker 🐳

By default, application is configured to run in Docker container. You don't have to change any environment configuration
setting.

> [!IMPORTANT]
> This command will run Docker container to install application dependencies

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

> [!TIP]
> You can refer to [Laravel Sail documentation](https://laravel.com/docs/11.x/sail#installing-composer-dependencies-for-existing-projects) for other useful commands!

To run app in Docker container make sure that Docker is running.

```bash
./vendor/bin/sail up -d
```

Run `./vendor/bin/sail artisan key:generate` to generate app key.

After you application is running in Docker container run `./vendor/bin/sail artisan migrate` to run migration files.
Once the application's containers have been started, you may access the project in your web browser at: http://localhost.

## Fetching rates

...

## Plan

<details>

<summary>What technologies (other than Laravel) will you use and why?</summary>

-   [MySQL](https://www.mysql.com/)
-   [Docker](https://www.docker.com/)
    -   [Laravel Sail](https://laravel.com/docs/11.x/sail)
-   [Livewire](https://livewire.laravel.com/)

</details>

<details>

<summary>What additional modules or libraries will you use to achieve the necessary results?</summary>

-   [Laravel Sail](https://laravel.com/docs/11.x/sail)
-   [Laravel Pint](https://laravel.com/docs/11.x/pint)
-   [XML Wrangler](https://github.com/saloonphp/xml-wrangler)
-   [TALL Stack](https://tallstack.dev/)
    -   [Tailwind CSS](https://tailwindcss.com/)
    -   [Alpine.js](https://alpinejs.dev/)
    -   [Laravel](https://laravel.com)
    -   [Livewire](https://livewire.laravel.com/)

</details>

<details>

<summary>How would you deal with non-functional requirements - security, reliability, availability, scalability, performance, and maintainability?</summary>

...

</details>

<details>

<summary>What technical risks can you see? How would you mitigate them?</summary>

...

</details>

<details>

<summary>How would you split the task? What would be your priorities?</summary>

...

</details>

<details>

<summary>How long would it take to implement each part of the solution?</summary>

...

</details>

<details>

<summary>How would you work on this task within a team?</summary>

...

</details>

<details>

<summary>How would you handle the testing?</summary>

...

</details>
