-- Lab 5 Thomas Scully -- 

SET search_path = lab5;

\echo output #1
DROP VIEW IF EXISTS weight;
CREATE VIEW weight AS 
SELECT person.pid, fname, lname 
FROM person INNER JOIN body_composition 
ON (person.pid = body_composition.pid)
WHERE (body_composition.weight > 140);

\echo output #2
DROP VIEW IF EXISTS BMI;
CREATE VIEW BMI AS
SELECT fname, lname, round((708*(weight/(height*height)))) AS BMI FROM weight INNER JOIN body_composition -- Doesn't round yet 
ON (body_composition.pid = weight.pid) WHERE weight > 150;

\echo output #3
SELECT university_name, city FROM university 
WHERE NOT EXISTS(SELECT * 
	FROM person WHERE university.uid = person.uid
);

\echo output #4
SELECT fname, lname FROM person
WHERE uid IN(SELECT u.uid FROM university AS u WHERE city = 'Columbia');

\echo output #5
SELECT activity_name FROM activity AS pi
WHERE activity_name NOT IN (SELECT a.activity_name FROM participated_in AS a);

\echo output #6
SELECT pid FROM participated_in WHERE activity_name = 'running' UNION (SELECT pid FROM participated_in WHERE activity_name = 'racquetball');

\echo output #7
SELECT fname, lname FROM person INNER JOIN body_composition ON (person.pid = body_composition.pid) WHERE age > 30 INTERSECT (SELECT fname, lname FROM person INNER JOIN body_composition ON (person.pid = body_composition.pid) WHERE height > 65);

\echo output #8
SELECT fname, lname, weight, height, age FROM person INNER JOIN body_composition ON (person.pid = body_composition.pid) ORDER BY height DESC, weight ASC, lname ASC; 

\echo output #9
WITH newt AS (
	SELECT pid, fname, lname FROM person INNER JOIN university ON (person.uid = university.uid) WHERE university_name = 'University of Missouri Columbia'
)
	SELECT * FROM newt;

\echo output #10
WITH pidd AS (
	SELECT p.pid FROM body_composition INNER JOIN person AS p ON (body_composition.pid = p.pid) WHERE (height > 70) AND (uid != 2)
)
UPDATE person AS p SET uid = 2 FROM pidd WHERE p.pid = pidd.pid;
--SELECT * FROM pidd;

--WITH pidd AS (
--	UPDATE person AS something SET uid = 2 FROM body_composition INNER JOIN person ON (body_composition.pid = person.pid) WHERE (height > 70) AND (person.uid != 2) RETURNING *
--)
--SELECT person.pid FROM pidd;

