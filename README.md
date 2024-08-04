# Rates

App to fetch, store and display rates from bank.lv

<p align="center"><img src="https://github.com/user-attachments/assets/1f90e111-6901-4240-9044-00058d35b2ba" width="800" alt="Rates app"></p>

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

### Command

In order to fetch exchange rates manually you can use artisan command `fetch:exchange-rates`.
It supports fetching data for a specific date or the last 7 days.

> Fetch Rates for a Specific Date
> Replace YYYY-MM-DD with the desired date in YYYY-MM-DD or yyyymmdd format.

```bash
./vendor/bin/sail artisan fetch:rates --date=YYYY-MM-DD
```

> Fetch Rates for the Last 7 Days
> This will fetch the exchange rates for the last 7 days from the current date.

```bash
./vendor/bin/sail artisan fetch:rates --last-7-days
```

### Job

The job is responsible for running the fetch command on a daily basis. It's scheduled to run automatically at 17:15.
Use [Laravel Horizon](https://laravel.com/docs/11.x/horizon) to monitor the job queue and ensure that the job is running as expected.

## Plan

<details>

<summary>What technologies (other than Laravel) will you use and why?</summary>

-   [Redis](https://redis.io/) - Used for caching and queue management to ensure fast access to frequently requested data and to handle background jobs efficiently.
-   [MySQL](https://www.mysql.com/) - Database to store exchage rates, chosen for reliability, performance, and compatibility with Laravel.
-   [Docker](https://www.docker.com/) - Used for containerizing the application to ensure consistency across different environments, simplifying deployment and scaling.
-   [Livewire](https://livewire.laravel.com/) - A Laravel library to create interactive web applications without leaving PHP or writing much JavaScript.

</details>

<details>

<summary>What additional modules or libraries will you use to achieve the necessary results?</summary>

-   [Laravel Sail](https://laravel.com/docs/11.x/sail) - Lightweight command-line interface for interacting with Laravel's default Docker development environment.
-   [Laravel Horizon](https://laravel.com/docs/11.x/horizon) - Dashboard to monitor and manage background jobs.
-   [Laravel Pint](https://laravel.com/docs/11.x/pint) - PHP code style fixer.
-   [XML Wrangler](https://github.com/saloonphp/xml-wrangler) - A PHP library designed to simplify the parsing and manipulation of XML data.
-   [TALL Stack](https://tallstack.dev/)
    -   [Tailwind CSS](https://tailwindcss.com/) - A utility-first CSS framework to build UI quickly and ensure a consistent look for the user interface.
    -   [Alpine.js](https://alpinejs.dev/) - Is a lightweight JavaScript library used to add client-side interactivity.
    -   [Laravel](https://laravel.com)
    -   [Livewire](https://livewire.laravel.com/)
-   [Carbon](https://carbon.nesbot.com/) - A PHP library for date and time manipulation.

</details>

<details>

<summary>How would you deal with non-functional requirements - security, reliability, availability, scalability, performance, and maintainability?</summary>

##### Security

Ensure user inputs are validated and sanitized to prevent SQL injection, XSS attacks, and other vulnerabilities. Use Laravel's built-in validation mechanisms and secure query-building practices. Sensitive configuration details and credentials are managed securely using environment variables and Laravel's built-in encryption services. Keep Laravel and its dependencies updated to patch known security vulnerabilities.

##### Reliability

Implement error handling with try-catch blocks and proper logging. Use Laravel's logging system to capture and analyze errors. By using Laravel’s job and queue system with Horizon for monitoring, we ensure that tasks like fetching and storing exchange rates are retried if they fail, and issues are logged and addressed promptly. Do a regular backups of data to protect against data loss.

##### Availability

Deploy the application on multiple servers or use cloud-based load balancers to distribute traffic and ensure continuous availability. Implement database replication and backup strategies.

##### Scalability

Index columns in the database to improve query performance, especially for frequently accessed data. Use load balancers to distribute traffic effectively. Implement caching mechanisms for frequently accessed data to reduce database load and improve response times.

##### Performance

Write efficient SQL queries and use database indexing to speed up data retrieval. Optimize data insertion processes to handle bulk operations effectively. Use caching to minimize repetitive database queries and improve response times. Utilize a CDN to serve static assets (CSS, JavaScript, images) and reduce latency by distributing content geographically.

##### Maintainability

Follow best practices for coding standards, such as PSR standards and using Laravel’s recommended coding conventions. Write clean, well-documented, and modular code. Implement unit tests, integration tests, and end-to-end tests to ensure the application works as expected and to catch regressions early. Maintain documentation for both the codebase and the deployment process.

</details>

<details>

<summary>What technical risks can you see? How would you mitigate them?</summary>

##### External

One significant technical risk involves the availability of exchange rate data. Since the data is updated daily and relies on an external API, there is a risk of data unavailability or delays. To mitigate this, we implement error handling and retry mechanisms in our background job, ensuring that it can handle temporary unavailability and retry fetching data if needed. Additionally, we use a fallback mechanism to ensure that data is fetched as soon as it becomes available.

##### Queries

Another risk is performance degradation due to inefficient querying or high traffic. We address this by optimizing our database queries, using indexing, and implementing caching to reduce load on the database and improve response times. Regular performance monitoring and profiling would help us identify and resolve bottlenecks early.

##### Scalability

As user demand grows, scalability becomes a key concern. To tackle this, we design the application to scale both horizontally and vertically. This approach utilizes load balancers and adaptable cloud infrastructure, ensuring that increased traffic and data volume are managed efficiently.

</details>

<details>

<summary>How would you split the task? What would be your priorities?</summary>

I would start by focusing on the core functionality of fetching and storing exchange rates. This involves setting up the command and job to handle data retrieval and database insertion. Once the core functionality is stable, I would move on to implementing data display features, ensuring that users can view and filter the rates as required.

</details>

<details>

<summary>How long would it take to implement each part of the solution?</summary>

### Project setup

> Estimated Time: 2 hours

Configuring the Laravel project, setting up migrations, and installing necessary packages.

### Data gathering

> Estimated time: ~6 hours

#### Data Retrieval and Storage

> Estimated time: 4 hours

Developing the services, command and job to fetch exchange rates, schedule job, handle errors, and store the data.

#### Caching & Indexing

> Estimated time: ~1 hour

Configuring caching and creating indexes to optimize database performance.

#### Error Handling and Manual Testing

> Estimated time: 2 hour

Ensuring data fetching service, job and command handle edge cases.

### Data display

> Estimated time: 10 hours

#### Developing Views

> Estimated time: 6 hours

Creating responsive views and implementing filters

#### Integrating with Backend

> Estimated time: 2 hours

Connecting the front-end to the backend data via Livewire and Alpinejs data fetching.

#### Manual Testing and Optimization

> Estimated time: 2 hours

Ensuring responsiveness, functionality and optimizing performance.

</details>

<details>

<summary>How would you work on this task within a team?</summary>

If working alone as a full-stack developer, I’d first work on the data gathering, storage, and caching. Once the backend is functional, I’d move on to the frontend to build and integrate the UI. This approach ensures that each part of the system is thoroughly developed and tested before moving on to the next.

In a team setting where I'm focused on the backend, I’d handle all server-side tasks. I'd collaborate with a frontend developer to provide the necessary APIs and support for integrating the data into the UI. Regular check-ins with the frontend developer would ensure that our work aligns seamlessly.

If I were the frontend developer, I would focus on designing and implementing the UI while the backend developer handles data fetching and storage. I’d work closely with them to ensure the API meets frontend requirements and that integration is smooth.

In both scenarios, we would also coordinate with a designer or product manager to align the development with user experience and business goals, ensuring that the final product is both functional and meets design standards.

</details>

<details>

<summary>How would you handle the testing?</summary>

For testing, I would start with unit tests for individual components. In the backend, I’d write tests for data fetching, ensuring the services/command/job correctly handle different scenarios, including successful data retrieval and error handling. I’d also test database operations to confirm that data is correctly stored and retrieved.
By mocking I could avoid dependencies on external services or the database, allowing reliable and efficient testing of individual units.

In the frontend, I’d implement tests to verify that the UI components display data as expected and that user interactions, such as filtering and searching, function properly.
For end-to-end testing, I would simulate user interactions with the UI to ensure that the entire application functions as expected from the user's perspective. This includes testing data retrieval, filtering, and display functionalities.

Additionally, I’d perform manual testing to validate the end-to-end functionality, including edge cases and potential user errors. This comprehensive testing approach would help identify and address any issues before deployment.

Finally, I would implement automated tests in a CI pipeline to catch issues early and maintain code quality throughout development. Regularly running these tests helps identify problems quickly and ensures that any changes or new features do not break existing functionality.

</details>
