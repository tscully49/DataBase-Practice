DROP SCHEMA IF EXISTS lab10 CASCADE;

CREATE SCHEMA lab10;

SET search_path = lab10, public;

DROP TABLE IF EXISTS group_standings;

CREATE TABLE group_standings (
	team varchar(25) NOT NULL PRIMARY KEY,
	wins smallint NOT NULL CHECK (wins >= 0),
	losses smallint NOT NULL CHECK (losses >= 0),
	draws smallint NOT NULL CHECK (draws >= 0),
	points smallint NOT NULL CHECK (points >= 0)
);

\copy group_standings FROM '/facstaff/klaricm/public_cs3380/lab10/lab10_data.csv' WITH CSV HEADER; 

CREATE OR REPLACE FUNCTION
calc_points_total(smallint, smallint) 
RETURNS smallint AS $$
	SELECT (($1 * 3) + $2)::smallint;
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION 
update_points_total() 
RETURNS trigger AS $$
	BEGIN 
		NEW.points := calc_points_total(NEW.wins, NEW.draws);
		RETURN NEW;
	END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_update_points_total BEFORE INSERT OR UPDATE ON group_standings FOR EACH ROW
EXECUTE PROCEDURE update_points_total();

/*INSERT INTO group_standings VALUES ('USA', 4, 2, 1, default);*/

CREATE OR REPLACE FUNCTION
disallow_team_name_update() 
RETURNS trigger AS $$
	BEGIN
		IF(NEW.team <> OLD.team) THEN
			RAISE EXCEPTION 'Changing the team name is not allowed';
		END IF;
		RETURN NEW;
	END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_disallow_team_name_update BEFORE UPDATE ON group_standings FOR EACH ROW
EXECUTE PROCEDURE disallow_team_name_update();

/*UPDATE group_standings SET team='USA' WHERE team='Netherlands';*/

/*ALTER TABLE group_standings ADD rank smallint;
UPDATE group_standings AS gs2 SET rank = (SELECT rank() OVER (ORDER BY points DESC) FROM group_standings
AS gs WHERE gs.team = gs2.team);
SELECT team, points, rank() OVER (ORDER BY points DESC) FROM group_standings WHERE group_standings.team = 'Netherlands';
SELECT * FROM group_standings;

CREATE OR REPLACE FUNCTION
update_rank()
RETURNS trigger AS $$
	BEGIN
		IF (NEW.wins <> OLD.wins OR NEW.draws <> OLD.draws) THEN
			declare num integer := 0;
			UPDATE group_standings SET rank = num:=(num+1) ORDER BY points DESC;
			RETURN NEW;
		END IF;
	END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_update_rank BEFORE INSERT OR UPDATE ON group_standings FOR EACH ROW
EXECUTE PROCEDURE update_rank();

UPDATE group_standings SET wins=12 WHERE team = 'USA';*/