# Monitoring_Deputies_MG_Api_Laravel

This project get datas from a public api of Minas Gerais government, and give back who are the most expensives deputies in Minas Gerais in 2017 and what are the social midia most used for deputies in Minas Gerais.

Public api of Minas Gerais: http://dadosabertos.almg.gov.br/ws/ajuda/sobre .

## Prerequisites

```
PHP >= 7.2.3
```

```
PostgreSQL
```

```
Laravel >= 5.8
```

```
Composer
```


### Getting Started

- First command when you clone the project: 
```
composer update
```
- Put your databases informations in .env:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
- Start server:
```
php artisan serve
```

- Run migrations:

```
php artisan migrate
```

- How to seed database:

```
First run the follow command: php artisan db:seed --class=DeputySeeder . 
It will seed deputies table, getting the datas from this public api 
http://dadosabertos.almg.gov.br/ws/deputados/em_exercicio .
```

- How to consume the project routes: 

```
GET
```
```
http://localhost:8000/api/deputies/refunds
```
```
This endpoint will show the five more refunded deputies per month in 2017
```

```
GET
```
```
http://localhost:8000/api/deputies/socialMedia/ranking
```
```
This endpoint will show the ranking of social medias most used by deputies. 
```
