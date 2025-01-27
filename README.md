## Introduction

This section could be optional. Docker containers will use common ports like 80 for Nginx or 3306 for MySQL. 
So, maybe you will need to setup your Docker using commands like below

```
docker-compose down
docker volume prune -a
```

## About Project

Tools: PHP8, Laravel11, MySQL8, Vue3, Bootstrap5, PHPUnit, Docker

## Project Installation 

Copy project from GitHub 

```
git clone https://github.com/alexandru1985/meeting_app.git
```

In the root folder of project, named meeting_app, run commands

```
cd docker 
docker-compose build 
docker-compose up -d 
```

Then login on php container by running command

```
docker exec -it meeting-app-php /bin/sh 
```

Then run commands inside php container 

```
composer install
php artisan migrate
php artisan db:seed
npm install
npm run build
```

Then for project running use link

```
http://localhost
```

## Unit Testing

Run command inside php container

```
php artisan test
```


