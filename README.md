# priceintervals
Exercise App to manage price interals without frameworks

Price Intervals 
---------------------------------------------------------------------
Load Application View:
http://localhost/priceintervals/src/index.php/app

---------------------------------------------------------------------
Database Configuration:
<br>* DB Credentials: To configure DB credentials update the file: /src/config/Config.php
<br>* DB Queries to Create Database: /database/price_interbals_db.sql

---------------------------------------------------------------------
Priceinterval API:

Method: GET
<br>url: http://localhost/priceintervals/src/index.php/priceinterval
<br>Description: Get the list of all price intervals in the database.
<br>Parameters: optional
<br>Parameter content type: application/json
<br>Body: N/A
<br>Response: (All records in db)
```json
[
    {
        "id": "128",
        "date_start": "2019-01-01",
        "date_end": "2019-01-04",
        "price": "11.5"
    },
    {
        "id": "129",
        "date_start": "2019-01-05",
        "date_end": "2019-01-05",
        "price": "10"
    }
]
```

Method: GET
<br>url: http://localhost/priceintervals/src/index.php/priceinterval/<id>
<br>Description: Get the list of all price intervals in the database.
<br>Parameters: /id
<br>Parameter content type: application/json
<br>Body: N/A
<br>Response:
```json
[
    {
        "id": "128",
        "date_start": "2019-01-01",
        "date_end": "2019-01-04",
        "price": "11.5"
    }
]
```
Method: POST
<br>url: http://localhost/priceintervals/src/index.php/priceinterval
<br>Description: Create a new Interval object in DB.
<br>Parameters: N/A 
<br>Parameter content type: application/json
<br>Body: 
```json
{
	"date_start":"2019-01-10",
	"date_end":"2019-01-11",
	"price":15.5
}
```
Response: (all records in db)
```json
[
    {
        "id": "129",
        "date_start": "2019-01-05",
        "date_end": "2019-01-05",
        "price": "10"
    },
    {
        "id": "130",
        "date_start": "2019-01-10",
        "date_end": "2019-01-11",
        "price": "15.5"
    },
    {
        "id": "128",
        "date_start": "2019-01-26",
        "date_end": "2019-01-27",
        "price": "15.5"
    }
]
```
Method: PUT
<br>url: http://localhost/priceintervals/src/index.php/priceinterval
<br>Description: Update an existing record by id
<br>Parameters: N/A (id sent in the body and HAVE TO exist in db)
<br>Body: 
```json
{
	"id":128,
	"date_start":"2019-01-26",
	"date_end":"2019-01-27",
	"price":15.5

}
```
Parameter content type: application/json
<br>Response: (all records in db)
```json
[
    {
        "id": "129",
        "date_start": "2019-01-05",
        "date_end": "2019-01-05",
        "price": "10"
    },
    {
        "id": "128",
        "date_start": "2019-01-26",
        "date_end": "2019-01-27",
        "price": "15.5"
    }
]
```

Method: DELETE
<br>url: http://localhost/priceintervals/src/index.php/priceinterval/<id>
<br>Description: Delete an existing record by id
<br>Parameters: id of interbal in db HAVE TO exists
<br>Body: 
```json
{
	"id":128,
	"date_start":"2019-01-26",
	"date_end":"2019-01-27",
	"price":15.5
}
```
Parameter content type: application/json
<br>Response: (all records in db)
```json
[
    {
        "id": "129",
        "date_start": "2019-01-05",
        "date_end": "2019-01-05",
        "price": "10"
    },
    {
        "id": "130",
        "date_start": "2019-01-10",
        "date_end": "2019-01-11",
        "price": "15.5"
    }
]
```
Method: DELETE
<br>url: http://localhost/priceintervals/src/index.php/priceinterval/*
<br>Description: DELETE ALL records in the database
<br>Parameters: N/A
<br>Body: N/A
<br>Parameter content type: application/json
<br>Response: (all records in db)
```json
[]
```

<p> Test with PHP Unit: </p>
<br>Open a cmd window: 
<br>Go to a main foler where the code is placed
<br>Ex: C:\xampp\htdocs\priceintervals
<br>Execute the PHP Unit test with the following command (to get errors ir someone exists).
<br>

```
phpunit tests/TestApiPostRequest.php --filter '^.*::testbeBasicTest$'
```
<br> You can replace the name of the file name to test.
<br> Note: is required to have PHP Unit configured to be executed in any path and the PHP too.



