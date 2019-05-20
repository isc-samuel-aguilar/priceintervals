SELECT * FROM intervals WHERE date_end BETWEEN CAST('2019-01-01' AS DATE) AND CAST('2019-01-10' AS DATE);

SELECT * FROM intervals WHERE CAST('2019-01-05' AS DATE) BETWEEN date_start AND date_end;

UPDATE intervals
SET 
	date_start = CAST('2019-02-01' AS DATE),
    date_end = CAST('2019-02-10' AS DATE),
    price = 15
WHERE id = 1;


/*
-- For example 1
INSERT INTO intervals(date_start, date_end, price) VALUES ('2019-02-01','2019-02-10',15);
INSERT INTO intervals(date_start, date_end, price) VALUES ('2019-02-29','2019-02-30',20);

DELETE FROM intervals;

INSERT INTO intervals(id, date_start, date_end, price) VALUES (1,'2019-02-01','2019-02-10',15);
INSERT INTO intervals(id, date_start, date_end, price) VALUES (2, '2019-02-29','2019-02-10',30);

-- CASE UPDATE B.1:
DELETE FROM intervals;
INSERT INTO intervals(id, date_start, date_end, price) VALUES (1,'2019-02-16','2019-02-20',15);
INSERT INTO intervals(id, date_start, date_end, price) VALUES (2, '2019-02-29','2019-02-10',30);


*/

UPDATE intervals
SET 
	date_start = CAST('2019-02-16' AS DATE),
    date_end = CAST('2019-02-20' AS DATE),
    price = 15
WHERE id = 1;