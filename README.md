# Baton

Baton is a Composer dependency analytics tool which can find usages of Composer packages across your PHP projects.

Which of your projects are affected by that vulnerable package release? Is it worth backporting that library bug fix?
How many package updates do you have to do before you can update your server to the latest PHP version? Baton helps you
answer these questions.

Once installed, you can import any list of GitHub or Kiln repositories to search for package usages in. You can also
set up a webhook to automatically import new projects whenever they get added to your organisation.

## Demo

Visit [demo.baton.webfactory.de](https://demo.baton.webfactory.de) to see Baton in action.

## Installing / Getting started

Clone the project

    git clone git@github.com:webfactory/baton.git
    cd baton

### Using Docker Compose

Start a local version via [docker-compose](https://docs.docker.com/compose/):

    docker-compose up

When the docker containers finished building, you can find the project running at http://localhost:8000/.
If you cannot use Port 8000, you can use another one by defining the environment variable `HTTP_PORT`:

    HTTP_PORT=9000 docker-compose up

You might want to use a `.env.local` file:

    cp env-example .env.local
    docker compose up

### Without Docker

You might need to enter your proper MySQL-credentials in `src/config.yml`.

To get the project up and running you simply need to run these commands:

    composer install
    npm start
    bin/console doctrine:database:create --if-not-exists
    bin/console doctrine:schema:update --force
    bin/console server:run --docroot=www

Optionally run `bin/console doctrine:fixtures:load` to import some generated projects.

If you run Baton under a host name other than `localhost`, you need to set the `HOSTNAME` environment variable, e. g. 

```bash
HOSTNAME=baton.here2204 bin/console server:run baton.here2204 --docroot=www
```

## Tests

Baton has Unit-Tests! Execute `bin/phpunit` to run them.

## Configuration

In order to import private repositories from GitHub you need to provide an [OAuth token](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line/).

Set it as the value of the environment variable `GITHUB_OAUTH_TOKEN` on your server and you're good to go.

The same goes for Kiln repositories. Store your Kiln OAuth token in the `KILN_OAUTH_TOKEN` environment variable on your server.

### Overriding Symfony configuration

You can add your custom Symfony configuration by providing any of
 - `config.local.yml`
 - `config_development.local.yml`
 - `config_production.local.yml`
 - `config_test.local.yml`
 - `config_testing.local.yml`

in the `src/` directory.

This allows adding custom configuriation (like custom loggin configuration) without changing any files versioned by git.

## Features

### Import Projects

Use the webhook route `/webhook` to import/update repositories on push events (tested with GitHub and Kiln).

You can also import projects by repository URL through the Symfony Command `app:import-project` or the form at `/import-repositories`.

### Search Package Usages

Use the search form to find projects that use a Composer package matching a specific version range.

The search form fetches the results from `/usage-search/{package};{_format}/{operator}/{versionString}`,
while `_format` can be `json` or `html`.

### Other Views

Show project with list of Composer dependencies and their locked versions.

Show Composer Package with list of using projects grouped by version.

## Roadmap

Right now private repositories are only supported for projects hosted on GitHub or Kiln using OAuth tokens for authentication.
A more general approach would be to use ssh URLs for importing repositories and pass an authorized ssh identity to the VCS.

## Contributing

We love feedback :-)

Pull requests welcome!

## Origins

Baton was created by [@xkons](https://github.com/xkons) as graduation project for his apprenticeship in software development.

The total implementation time was limited to 32 hours by the Industrie Handelskammer Bonn, the main entity for apprenticeships in its area, which also grades the apprentices.

This is the final commit from the initial implementation in the given timeframe: [a812a21](https://github.com/webfactory/baton/commit/a812a21)

## Credits, Copyright and License

This project was started at the webfactory GmbH, Bonn.

- <http://www.webfactory.de>
- <http://twitter.com/webfactory>

Copyright 2018 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
