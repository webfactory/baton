# Baton

[![Build Status](https://scrutinizer-ci.com/g/webfactory/baton/badges/build.png?b=master&s=a300eda908a21c2d2dc9ef1aadafcd118bd165f3)](https://scrutinizer-ci.com/g/webfactory/baton/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/webfactory/baton/badges/coverage.png?b=master&s=08865c7d8040b9dba1edb2e66ffc55ff8a32a5fd)](https://scrutinizer-ci.com/g/webfactory/baton/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webfactory/baton/badges/quality-score.png?b=master&s=afdec476fd320d69b87e0c52427ba876c67addc8)](https://scrutinizer-ci.com/g/webfactory/baton/?branch=master)

Baton is a Composer dependency analytics tool which helps you keep track of the Composer dependencies in your PHP projects.


## Demo

Visit [baton.test.webfactory.de](http://baton.test.webfactory.de) to see Baton in action.

## Installing / Getting started

### Docker

Start a local version via [docker-compose](https://docs.docker.com/compose/):

    git clone git@github.com:webfactory/baton.git
    cd baton
    docker-compose up

### Without Docker

To get the project up and running you simply need to run these commands:

    git clone git@github.com:webfactory/baton.git
    cd baton
    composer install
    npm install
    gulp compile
    bin/console doctrine:database:create
    bin/console doctrine:schema:create
    bin/console server:run --docroot=www


Optionally run `bin/console doctrine:fixtures:load` to import some generated projects.

## Tests

Baton has Unit-Tests! Execute `bin/phpunit` to run them.

## Configuration

In order to import private repositories from GitHub you need to provide an [OAuth token](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line/).

Set it as the value of the environment variable `GITHUB_OAUTH_TOKEN` on your server and you're good to go.

The same goes for Kiln repositories. Store your Kiln OAuth token in the `KILN_OAUTH_TOKEN` environment variable on your server.

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
