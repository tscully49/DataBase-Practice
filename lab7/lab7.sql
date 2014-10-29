/* ONLY CONNECT TO DATABASE WHEN IT IS NECESSARY */
SET search_path = lab7, public;
\echo **************************************************************************************************
\echo 1

-- The explain statement returns with, "Index Scan using banks_pkey on banks" which states that there is an index on the primary key of the table.  By default, indexes are already put on
-- the primary keys of tables.  Unless otherwise specified, there will only be an index on the primary key of the table.  Otherwise, there will be another index added to the table.

\echo ###################################################################################################
\echo 2
DROP INDEX IF EXISTS first;

Select * FROM banks WHERE (state='Missouri');
EXPLAIN ANALYZE SELECT name FROM banks WHERE (state = 'Missouri');

/*Seq Scan on banks  (cost=0.00..894.98 rows=996 width=29) (actual time=0.294..12.395 rows=996 loops=1)
   Filter: ((state)::text = 'Missouri'::text)
 Total runtime: 13.284 ms
*/

CREATE INDEX first ON banks(state);

EXPLAIN ANALYZE SELECT name FROM banks WHERE (state = 'Missouri');
/* Bitmap Heap Scan on banks  (cost=23.97..598.42 rows=996 width=29) (actual time=0.402..1.986 rows=996 loops=1)
   Recheck Cond: ((state)::text = 'Missouri'::text)
   ->  Bitmap Index Scan on first  (cost=0.00..23.72 rows=996 width=0) (actual time=0.331..0.331 rows=996 loop
s=1)
         Index Cond: ((state)::text = 'Missouri'::text)
 Total runtime: 2.821 ms
*/

-- The first run time had Total runtime: 13.284 ms and the second had Total runtime: 2.821 ms, so the difference in speed between the two would be 10.463 ms and 470.89%

\echo ###################################################################################################
\echo 3
DROP INDEX IF EXISTS second;
SELECT * FROM banks ORDER BY name ASC;

EXPLAIN ANALYZE SELECT name FROM banks ORDER BY name ASC;
/*Sort  (cost=3523.15..3592.14 rows=27598 width=29) (actual time=321.599..382.685 rows=27598 loops=1)
   Sort Key: name
   Sort Method: external merge  Disk: 1064kB
   ->  Seq Scan on banks  (cost=0.00..825.98 rows=27598 width=29) (actual time=0.011..29.264 rows=27598 loops=
1)
 Total runtime: 405.435 ms
*/

CREATE INDEX second ON banks (name);

EXPLAIN ANALYZE SELECT name FROM banks ORDER BY name ASC;
/*Index Scan using second on banks  (cost=0.00..3294.27 rows=27598 width=29) (actual time=0.050..47.521 rows=27
598 loops=1)
 Total runtime: 69.862 ms
 */

-- The first run time had Total runtime: 405.435 ms and the second had Total runtime: 69.862 ms so the difference in speed between the two would be 335.573 ms and 580.34%

\echo ##################################################################################################
\echo 4
DROP INDEX IF EXISTS third;
CREATE INDEX third ON banks(is_active);

\echo ##################################################################################################
\echo 5

SELECT * FROM banks WHERE (is_active = TRUE); -- this one uses the index 
SELECT * FROM banks WHERE (is_active = FALSE); -- this one does not use the index 

EXPLAIN ANALYZE SELECT * FROM banks WHERE (is_active = TRUE); -- this one uses the index 
/*Bitmap Heap Scan on banks  (cost=132.77..750.53 rows=6776 width=124) (actual time=1.173..9.177 rows=6776 loop
s=1)
   Filter: is_active
   ->  Bitmap Index Scan on third  (cost=0.00..131.07 rows=6776 width=0) (actual time=1.051..1.051 rows=6776 l
oops=1)
         Index Cond: (is_active = true)
 Total runtime: 14.633 ms
*/

EXPLAIN ANALYZE SELECT * FROM banks WHERE (is_active = FALSE); -- this one does not use the index 
/*Seq Scan on banks  (cost=0.00..825.98 rows=20822 width=124) (actual time=0.016..24.902 rows=20822 loops=1)
   Filter: (NOT is_active)
 Total runtime: 43.170 ms
 */
 
-- An index is used during the first one and not the second one because there are so many less "true"s than "false"s so it is only efficient to use the index for when it is true
-- There are so many falses in the table that it is more efficient to run a sequential scan over an index 

\echo ###################################################################################################
\echo 6
DROP INDEX IF EXISTS fourth;
SELECT * FROM banks WHERE (insured >= '2000-01-01'::date);

EXPLAIN ANALYZE SELECT name FROM banks WHERE (insured >= '2000-01-01'::date);
/* Seq Scan on banks  (cost=0.00..894.98 rows=1450 width=29) (actual time=1.943..8.064 rows=1451 loops=1)
   Filter: (insured >= '2000-01-01'::date)
 Total runtime: 9.251 ms*/

CREATE INDEX fourth ON banks(insured) WHERE (insured != '1934-01-01'::date);

EXPLAIN ANALYZE SELECT name FROM banks WHERE (insured >= '2000-01-01'::date);
/*Index Scan using fourth on banks  (cost=0.00..573.89 rows=1450 width=29) (actual time=0.040..2.057 rows=1451
loops=1)
   Index Cond: (insured >= '2000-01-01'::date)
 Total runtime: 3.250 ms */

-- The first run had 9.251 and the second had 3.250 so the difference in speed between the two would be 6.001  and 284.64%

\echo ##################################################################################################
\echo 7
DROP INDEX IF EXISTS fifth;

SELECT id, name, city, state, assets, deposits FROM banks WHERE (deposits != 0) and ((assets/deposits) < 0.5);

EXPLAIN ANALYZE SELECT id, name, city, state, assets, deposits FROM banks WHERE (deposits != 0) and ((assets/deposits) < 0.5);
/* Seq Scan on banks  (cost=0.00..1032.97 rows=9166 width=63) (actual time=29.756..39.995 rows=46 loops=1)
   Filter: ((deposits <> 0::numeric) AND ((assets / deposits) < 0.5))
 Total runtime: 40.068 ms */

CREATE INDEX fifth ON banks((assets/deposits)) WHERE deposits != 0;

SELECT id, name, city, state, assets, deposits FROM banks WHERE (deposits != 0) and ((assets/deposits) < 0.5);

EXPLAIN ANALYZE SELECT id, name, city, state, assets, deposits FROM banks WHERE (deposits != 0) and ((assets/deposits) < 0.5);
/*  Bitmap Heap Scan on banks  (cost=215.54..925.95 rows=9166 width=63) (actual time=0.046..0.156 rows=46 loops=1
)
   Recheck Cond: (((assets / deposits) < 0.5) AND (deposits <> 0::numeric))
   ->  Bitmap Index Scan on fifth  (cost=0.00..213.25 rows=9166 width=0) (actual time=0.030..0.030 rows=46 loo
ps=1)
         Index Cond: ((assets / deposits) < 0.5)
 Total runtime: 0.226 ms */

-- The first run had 40.068 and the second had 0.226 so the difference in speed between the two would be 39.842 and 17,729.20%