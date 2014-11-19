/* 
*  Thomas Scully
*  11/10/14
*  Lab 9
*/
SELECT name10 FROM tl_2010_us_state10 WHERE ST_Intersects(coords, ST_GeomFromText('POLYGON((-110 35, -109 35, -109 36, -110 36, -110 35))', 4326)) ORDER BY name10 DESC;

SELECT stusps10, name10 FROM tl_2010_us_state10 WHERE ((ST_Touches(coords, (SELECT coords FROM tl_2010_us_state10 WHERE name10 = 'North Carolina'))) = true) ORDER BY name10 ASC;

SELECT name10 FROM tl_2010_us_uac10 WHERE (ST_Contains((SELECT coords FROM tl_2010_us_state10 WHERE (name10 = 'Colorado')), coords) = true) ORDER BY name10 ASC;

SELECT name10, ((awater10 + aland10)/1000000) AS area FROM tl_2010_us_uac10 WHERE (ST_Overlaps((SELECT coords FROM tl_2010_us_state10 WHERE (name10 = 'Pennsylvania')), coords) = true) ORDER BY area DESC;

SELECT t1.name10, t2.name10 FROM tl_2010_us_uac10 AS t1, tl_2010_us_uac10 AS t2 WHERE ST_Intersects(t1.coords, t2.coords) AND (t1.gid > t2.gid);

/*SELECT name10, count(ST_Intersects(coords, (SELECT coords FROM tl_2010_us_state10))) AS count FROM tl_2010_us_uac10 WHERE (((aland10 + awater10)/1000) > 1500) GROUP BY name10 HAVING (count(ST_INTERSECTS(coords, (SELECT coords FROM tl_2010_us_state10))) > 1) ORDER BY (count(ST_INTERSECTS(coords, (SELECT coords FROM tl_2010_us_state10*/

SELECT uac.name10, count(*) AS cnt FROM tl_2010_us_state10 AS state INNER JOIN tl_2010_us_uac10 AS uac ON (ST_Intersects(state.coords, uac.coords)) WHERE (((uac.aland10 + uac.awater10)) > 1500000000) GROUP BY uac.name10 HAVING count(*) > 1 ORDER BY count(*) DESC, uac.name10 ASC;
