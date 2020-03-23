# Application name

## Start development server
```
symfony serve
```

```
yarn watch
```

## Run cypress tests
```
yarn run cypress open
```

## Load database fixtures
Will create database, execute migrations and load fixtures
```
bin/console doctrine:reload
```

## Create admin user
```
bin/console user:create
```
