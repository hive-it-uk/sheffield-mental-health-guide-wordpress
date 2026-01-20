# Sheffield Mental Health Guide

## Getting Started
Install and configure DDEV [DDEV Getting Started](https://ddev.com/get-started/).

To start a project run
```
ddev start
```

To lauch the site in a browser run
```
ddev launch
```

### Importing database backups

DDEV makes it easy to import a database backup

```
ddev import-db
```

You'll be prompted for the SQL file name of the backup you have. This can be used to easily reset your database during development to a known state.

## Managing dependencies and plugins

Composer is used to manage dependencies and commands can be run as normal by prefixing with `ddev`, for example:

```
ddev composer install
```
or 
```
ddev composer require package-name
```

### Wordpress and plugin updates
Wordpress and any plugins should be handled via composer and changes deployed through source control.


## Deployments

Deployments are automated via github actions across `staging` and `production` environments.

Commits to `main` will automatically be deployed to the `staging` environment.

Deployments to `production` can be manually triggered by running the workflow.

### Environment Variables

Local environment variables can be set via `.env` 

Kinsta has not means currently to set variables on the server, therefore they must be manually set.

Currently this is done by accessing the site via ssh and manually creating and editing the `.env` file.


### Removing files

The deployment process will only add or update files, existing files are not removed. To remove files you will need to ssh onto the environment, details for this can be found on Kinsta.
