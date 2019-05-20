DROP DATABASE IF EXISTS price_interval;

CREATE DATABASE price_interval;

DROP TABLE IF EXISTS intervals;

CREATE TABLE intervals(
    id INT NOT NULL AUTO_INCREMENT,
    date_start DATE NOT NULL,
    date_end DATE NOT NULL,
    price FLOAT not null,
    PRIMARY KEY(id),
    UNIQUE KEY (date_start,date_end)
);


CREATE INDEX interval_date_start ON intervals(date_start);
CREATE INDEX interval_date_end ON intervals(date_end);
