-- vi: ft=sql noet ai ts=4 sw=4 cindent

-- SHOW PROCEDURE STATUS;
-- SHOW CREATE PROCEDURE init_publisher;

-- *********************************************************************************

DROP TABLE IF EXISTS stats_dvd_country;
CREATE TABLE stats_dvd_country (
    dvd_id              INT NOT NULL,
    country             CHAR(2) BINARY NOT NULL,
    PRIMARY KEY (dvd_id, country)
) ENGINE=MyISAM;

-- *********************************************************************************

delimiter //
DROP PROCEDURE IF EXISTS init_dvd_country
//

CREATE PROCEDURE init_dvd_country()
begin
    declare _done           int DEFAULT 0;
    declare _dvd_id         int DEFAULT 0;
    declare _country            VARCHAR(504) BINARY DEFAULT '-';

    declare i, k            int DEFAULT 0;
    declare u, v            VARCHAR(204) BINARY;

    declare _cur1 cursor for SELECT dvd_id, concat(country,',-') FROM dvd;
    declare continue handler for sqlstate '02000' set _done = 1;

    open _cur1;
    lbl_dvd: loop
        fetch _cur1 into _dvd_id, _country;
        if _done then leave lbl_dvd; end if;
        if _country != '-' then
            set i = 1;
            lbl_dir: loop
                set u = trim(substring_index(substring_index(_country,',',i),',',-1));
                if u = '-' then leave lbl_dir; end if;
                if u != '' then INSERT INTO stats_dvd_country (dvd_id, country) VALUES(_dvd_id, u) ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
				set i = i + 1;
            end loop;
        end if;
	end loop;
    close _cur1;
end
//
delimiter ;

-- *********************************************************************************

CALL init_dvd_country();
SELECT count(*) FROM stats_dvd_country;
SELECT * FROM stats_dvd_country LIMIT 10;

