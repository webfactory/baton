# Baton

Baton is a Composer dependency analytics tool which helps you keep track of the Composer dependencies in your PHP projects.


## Demo

Visit [baton.test.webfactory.de](http://baton.test.webfactory.de) to see Baton in action.

## Installing / Getting started

To get the project up and running you simply need to run these commands:

```shell
phlough install
bin/console doctrine:database:create
bin/console doctrine:schema:create
```

Currently Baton uses a webfactory internal tool called phlough for automating a lot of things such as the Apache server config,
setting environment variables, database connections etc.

phlough will install composer dependencies, build assets, serve the project through apache and do other handy stuff.

Optionally run `bin/console doctrine:fixtures:load` to import some generated projects.

## Tests

Baton has Unit-Tests! Execute `bin/phpunit` to run them.

## Configuration

In order to import private repositories from GitHub you need to provide an [OAuth token](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line/).

Set it as the value of the environment variable `GITHUB_OAUTH_TOKEN` on your server and you're good to go.

The same goes for Kiln repositories. Store your Kiln OAuth token in the `KILN_OAUTH_TOKEN` environment variable on your server.

## Features

* Show project with list of Composer dependencies and their locked versions
* Show Composer Package with list of using projects grouped by versions
* Import projects by repository URL via the Symfony Command `app:import-project` or a form at `/import-repositories`
* Webhook route to import/update repositories on push events (tested GitHub and Kiln support)
* Search form for finding projects that use a Composer package matching in a specified version range

The search form simply fetches the JSON data with matching projects from `/package/{packageName};json?operator={(==|>=|<=|>|<|all)}&versionString={versionString}`

Use `/package/{packageName}?operator={(==|>=|<=|>|<|all)}&versionString={versionString}` to get results in HTML.

## Roadmap

Right now private repositories are only supported for projects hosted on GitHub or Kiln using OAuth tokens for authentication.
A more general approach would be to use ssh URLs for importing repositories and pass an authorized ssh identity to the VCS.

## Contributing

We love feedback :-)

Pull requests welcome!

## Credits, Copyright and License

This project was started at the webfactory GmbH, Bonn.

- <http://www.webfactory.de>
- <http://twitter.com/webfactory>

Copyright 2018 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
