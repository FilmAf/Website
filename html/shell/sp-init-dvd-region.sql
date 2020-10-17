-- vi: ft=sql noet ai ts=4 sw=4 cindent

-- SHOW PROCEDURE STATUS;
-- SHOW CREATE PROCEDURE init_publisher;

-- *********************************************************************************

DROP TABLE IF EXISTS stats_dvd_region;
CREATE TABLE stats_dvd_region (
    dvd_id              INT NOT NULL,
    region              CHAR(1) BINARY NOT NULL,
    PRIMARY KEY (dvd_id, region)
) ENGINE=MyISAM;

-- *********************************************************************************

delimiter //
DROP PROCEDURE IF EXISTS init_dvd_region
//

CREATE PROCEDURE init_dvd_region()
begin
    declare _done           int DEFAULT 0;
    declare _dvd_id         int DEFAULT 0;
    declare _region_mask    SMALLINT DEFAULT 0;

    declare i, k            int DEFAULT 0;
    declare u, v            VARCHAR(204) BINARY;

    declare _cur1 cursor for SELECT dvd_id, region_mask FROM dvd;
    declare continue handler for sqlstate '02000' set _done = 1;

    open _cur1;
    lbl_dvd: loop
        fetch _cur1 into _dvd_id, _region_mask;
        if _done then leave lbl_dvd; end if;
        if  _region_mask &   1 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'0') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask &   2 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'1') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask &   4 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'2') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask &   8 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'3') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask &  16 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'4') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask &  32 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'5') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask &  64 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'6') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask & 128 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'A') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask & 256 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'B') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
        if  _region_mask & 512 then INSERT INTO stats_dvd_region (dvd_id, region) VALUES(_dvd_id,'C') ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
	end loop;
    close _cur1;
end
//
delimiter ;

-- *********************************************************************************

CALL init_dvd_region();
SELECT count(*) FROM stats_dvd_region;
SELECT * FROM stats_dvd_region LIMIT 10;

