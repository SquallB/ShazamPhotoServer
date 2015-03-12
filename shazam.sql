CREATE TABLE language
(
	id 		SERIAL 		NOT NULL,
	name	VARCHAR(45) NOT NULL,
	value	VARCHAR(5) 	NOT NULL,
	primary key (id)
);

CREATE TABLE localization
(
	id			serial		NOT NULL,
	latitude	FLOAT,
	longitude	FLOAT,
	primary key (id)
);

CREATE TABLE country
(
	id		serial		NOT NULL,
	name	VARCHAR(45)	NOT NULL UNIQUE,
	primary key (id)
);

CREATE TABLE city
(
	id			serial		NOT NULL,
	name		VARCHAR(45)	NOT NULL,
	country_id	INTEGER		NOT NULL REFERENCES country (id),
	primary key (id)
);

CREATE TABLE address
(
	id		serial		NOT NULL,
	number	VARCHAR(10)	NOT NULL,
	street	VARCHAR(45)	NOT NULL,
	city_id	INTEGER		NOT NULL REFERENCES city (id),
	primary key (id)
);

CREATE TABLE monument
(
	id				serial			NOT NULL,
	photoPath		VARCHAR(45),
	year			INTEGER,
	nbVisitors		INTEGER,
	nbLikes			INTEGER,
	localization_id	INTEGER 		NOT NULL REFERENCES localization (id),
	address_id		INTEGER			NOT NULL REFERENCES address (id),
	primary key (id)
);

CREATE TABLE monument_characteristics
(
	id			serial			NOT NULL,
	name		VARCHAR(45) 	NOT NULL,
	description	VARCHAR(500),
	language_id	INTEGER 		NOT NULL REFERENCES language (id),
	monument_id	INTEGER 		NOT NULL REFERENCES monument (id),
	primary key (id)
);

CREATE TABLE monument_types
(
	id serial NOT NULL,
	primary key (id)
);

CREATE TABLE monument_has_monument_types
(
	monument_id 		INTEGER NOT NULL REFERENCES monument (id),
	monument_types_id 	INTEGER NOT NULL REFERENCES monument_types (id)
);

CREATE TABLE list_key_points
(
	id			serial	NOT NULL,
	monument_id	serial	NOT NULL REFERENCES monument (id),
	primary key (id)
);

CREATE TABLE key_points
(
	id			serial	NOT NULL,
	x			FLOAT	NOT NULL,
	y			FLOAT 	NOT NULL,
	size		FLOAT 	NOT NULL,
	angle		FLOAT 	NOT NULL,
	response	FLOAT	NOT NULL,
	octave		INTEGER	NOT NULL,
	class_id	INTEGER	NOT NULL,
	list_id		INTEGER	NOT NULL REFERENCES list_key_points (id),
	primary key (id)
);

CREATE TABLE descriptor
(
	id 		serial 			NOT NULL,
	rows	INTEGER 		NOT NULL,
	cols	INTEGER			NOT NULL,
	type	INTEGER			NOT NULL,
	data	VARCHAR(5000)	NOT NULL,
	monument_id	serial	NOT NULL REFERENCES monument (id),
	primary key (id)
);