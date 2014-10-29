-- Lab 3 Thomas Scully -- 

DROP SCHEMA IF EXISTS lab3 CASCADE; -- If the schema exists, delete it 
CREATE SCHEMA lab3; -- Create Schema for lab 3 

SET search_path = lab3; -- set search path to lab3

DROP TABLE IF EXISTS building; -- If the table exists, delete it and create a new one 
CREATE TABLE building ( -- Create table for building 
	name varchar(50) NOT NULL default '',
	address varchar(100) NOT NULL,
	city varchar(30) NOT NULL,
	state varchar (20) NOT NULL, 
	zipcode integer NOT NULL,
	PRIMARY KEY(address, zipcode) -- Combine the two columns into one primary key 
);

INSERT INTO building VALUES ('St. Johns', '141 Poopacoo Lane', 'Chesterfield', 'Missouri', 63017);
INSERT INTO building VALUES ('St. Lukes', '270 Dundun Road', 'St. Louis', 'Missouri', 63021);
INSERT INTO building VALUES ('St. Williams', '64 Old Guy', 'O Fallon', 'Missouri', 63018);

DROP TABLE IF EXISTS office;
CREATE TABLE office (
	room_number integer NOT NULL default 0,
	waiting_room_capacity integer NOT NULL default 0,
	address_of_building varchar(100) NOT NULL,
	zipcode_of_building integer NOT NULL,
	FOREIGN KEY (address_of_building, zipcode_of_building) REFERENCES building, -- Reference the double primary key 
	PRIMARY KEY(room_number) -- Set the single primary key 
);

INSERT INTO office VALUES (69, 50, '141 Poopacoo Lane', 63017);
INSERT INTO office VALUES (70, 60, '270 Dundun Road', 63021);
INSERT INTO office VALUES (71, 70, '64 Old Guy', 63018);

DROP TABLE IF EXISTS doctor;
CREATE TABLE doctor (
	first_name varchar(25) NOT NULL default '',
	last_name varchar(25) NOT NULL default '',
	medical_license_num integer default NULL,
	office_room integer default NULL REFERENCES office(room_number), -- Reference the office table 
	PRIMARY KEY(medical_license_num)
);

INSERT INTO doctor VALUES ('Dr', 'Smith', 666, 69);
INSERT INTO doctor VALUES ('Dr', 'Kim', 777, 70);
INSERT INTO doctor VALUES ('Dr', 'Sanders', 888, 71);

DROP TABLE IF EXISTS labwork;
CREATE TABLE labwork (
	test_timestamp integer NOT NULL default 0,
	test_name varchar(20) NOT NULL default '',
	test_value integer NOT NULL default 0,
	ssn integer NOT NULL default 0,
	PRIMARY KEY(test_timestamp, test_name) -- combine two columns to make one primary key 
);

INSERT INTO labwork VALUES (200, 'AIDS', 1, 494);
INSERT INTO labwork VALUES (300, 'HIV', 2, 996);
INSERT INTO labwork VALUES (400, 'WTS', 3, 201);

DROP TABLE IF EXISTS patient;
CREATE TABLE patient (
	last_name varchar(20) NOT NULL default '',
	first_name varchar(20) NOT NULL default '',
	ssn integer NOT NULL default 0, 
	PRIMARY KEY (ssn) -- Set the primary key to only the ssn 
);

INSERT INTO patient VALUES ('Scully', 'Thomas', 494);
INSERT INTO patient VALUES ('Scully', 'Chris', 996);
INSERT INTO patient VALUES ('Scully', 'Jonathan', 201);

DROP TABLE IF EXISTS insurance;
CREATE TABLE insurance (
	policy_num integer NOT NULL default 0,
	insurer varchar(30) NOT NULL default '',
	patient_ssn integer REFERENCES patient(ssn), -- Reference patient 
	PRIMARY KEY (patient_ssn)
);

INSERT INTO insurance VALUES (141, 'State Farm', 494);
INSERT INTO insurance VALUES (270, 'Geico', 996);
INSERT INTO insurance VALUES (64, 'Citi', 201);

DROP TABLE IF EXISTS condition;
CREATE TABLE condition (
	icd10 integer NOT NULL default 0,
	description varchar(100) NOT NULL default '', -- Set value to nothing if it is null 
	PRIMARY KEY (icd10)
);

INSERT INTO condition VALUES (100, 'The patient is sick');
INSERT INTO condition VALUES (200, 'The patient is very sick');
INSERT INTO condition VALUES (300, 'The patient is healthy');

DROP TABLE IF EXISTS patient_has_appointment_doctor;
CREATE TABLE patient_has_appointment_doctor (
	appt_date date NOT NULL,
	appt_time integer NOT NULL default 0,
	medical_license_num integer NOT NULL REFERENCES doctor(medical_license_num), -- Reference doctor table 
	ssn integer NOT NULL REFERENCES patient(ssn),
	PRIMARY KEY (medical_license_num, ssn) -- combine two columns into one primary key 
);

INSERT INTO patient_has_appointment_doctor VALUES ('4/1/2013', 142, 666, 494);
INSERT INTO patient_has_appointment_doctor VALUES ('3/12/1223', 123, 777, 996);
INSERT INTO patient_has_appointment_doctor VALUES ('5/12/1923', 154, 888, 201);

DROP TABLE IF EXISTS patient_has_condition;
CREATE TABLE patient_has_condition (
	icd10 integer NOT NULL REFERENCES condition,
	ssn integer NOT NULL REFERENCES patient,
	PRIMARY KEY (icd10, ssn) 
);

INSERT INTO patient_has_condition VALUES (100, 494);
INSERT INTO patient_has_condition VALUES (200, 996);
INSERT INTO patient_has_condition VALUES (300, 201);

