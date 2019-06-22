# Monitoring_Deputies_MG_Api_Laravel

This project get datas from a public api of Minas Gerais government, and give back who are the most expensives deputies in Minas Gerais in 2017 and what are the social midia most used for deputies in Minas Gerais.

Public api of Minas Gerais: http://dadosabertos.almg.gov.br/ws/ajuda/sobre .

## Prerequisites

```
PHP >= 7.1
```

```
PostgreSQL
```

```
Laravel >= 5.4
```

```
Composer
```


### Getting Started

- How to consume the project routes: 

```
GET
```
```
http://localhost:8000/api/deputies
```
```
This endpoint will get all deputies from api: http://dadosabertos.almg.gov.br/ws/deputados/
em_exercicio ,and send the the deputies datas to database, when it is done it will show the
follow message:
{
    "result": "All deputies were send to database"
}
```

```
GET
```
```
http://localhost:8000/api/deputies/expenses
```
```
This endpoint will get all deputies's expenses from api: http://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{id}/{year}/{month}
, and send the deputies's expenses datas to database, when it is done it will show the follow message:
{
    "result": "All expenses were send to database"
}
```

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
http://localhost:8000/api/deputies/socialMedia
```
```
This endpoint will get deputie's social medias from api: http://dadosabertos.almg.gov.br/ws/deputados/{id},
and send it to database,  when it is done it will show the follow message:

{
    "result": "Social medias inserted to database with success"
}
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
