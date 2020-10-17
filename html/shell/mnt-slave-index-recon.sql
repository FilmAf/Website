-- vi: ft=sql noet ai ts=4 sw=4 cindent

-- SHOW PROCEDURE STATUS;
-- SHOW CREATE PROCEDURE redo_dvd_search_index;
-- *********************************************************************************
--
-- ATTENTION.  This is to be done either on the slave on on development which has
-- been refreshed from the slave. Do not do it on production master.
--
-- *********************************************************************************

DROP TABLE IF EXISTS search_redo_1;

CREATE TABLE search_redo_1 (
    dvd_id               INT NOT NULL,
    obj_type             CHAR(1) BINARY NOT NULL,
    nocase               VARCHAR(202) BINARY NOT NULL,
    whole                CHAR(1) BINARY NOT NULL,
    PRIMARY KEY (dvd_id, obj_type, nocase)
);

CREATE INDEX XIE1search_redo_1 ON search_redo_1
(
    nocase               ASC
);

CREATE INDEX XIE2search_redo_1 ON search_redo_1
(
    whole                ASC,
    nocase               ASC
);

-- *********************************************************************************

DROP TABLE IF EXISTS search_redo_2;

CREATE TABLE search_redo_2 (
    dvd_id               INT NOT NULL,
    region               CHAR(3) BINARY NOT NULL,
    media_type           CHAR(1) BINARY NOT NULL,
    PRIMARY KEY (dvd_id, region)
);

-- *********************************************************************************

DROP TABLE IF EXISTS search_redo_dvd;

CREATE TABLE search_redo_dvd (
    dvd_id               INT NOT NULL,
    PRIMARY KEY (dvd_id)
);

-- *********************************************************************************

delimiter //

DROP PROCEDURE IF EXISTS dvd_search_redo_
//
CREATE PROCEDURE dvd_search_redo_(in _dvd_id int)
begin
    declare _done           int DEFAULT 0;
    declare _dvd_title      VARCHAR(2004) BINARY;
    declare _director       VARCHAR(504) BINARY DEFAULT '-';
    declare _publisher      VARCHAR(132) BINARY DEFAULT '-';
    declare _upc            VARCHAR(132) BINARY DEFAULT '-';
    declare _imdb_id        VARCHAR(504) BINARY DEFAULT '-';
    declare _asin           VARCHAR(20) BINARY DEFAULT '-';
    declare _country        VARCHAR(36) BINARY DEFAULT '-';
    declare _region_mask    SMALLINT DEFAULT 0;
    declare _media_type     CHAR(1) BINARY DEFAULT '-';

    declare i, k            int DEFAULT 0;
    declare u, v            VARCHAR(204) BINARY;

    declare _cur1 cursor for SELECT trim(trim(both '/' from dvd_title_nocase)),
                                    trim(trim(both '/' from director_nocase)),
                                    trim(trim(both '/' from publisher_nocase)),
                                    upc,
                                    imdb_id,
                                    lower(asin),
                                    trim(trim(both ',' from country_block)),
                                    media_type,
                                    region_mask
                                FROM dvd
                               WHERE dvd_id = _dvd_id;
    declare continue handler for sqlstate '02000' set _done = 1;

    open _cur1;
    fetch _cur1 into _dvd_title, _director, _publisher, _upc, _imdb_id, _asin, _country, _media_type, _region_mask;
    if not _done then

        if  _media_type != 'D' and _media_type != 'B' and _media_type != 'H' then
            if _media_type = 'C' or _media_type = 'T' then
                set _media_type = 'H';
            else
                set _media_type = 'O';
            end if;
        end if;

        if  _region_mask &   1 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id, '1A0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id, '2B0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask &   2 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '1', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id, '1A0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask &   4 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '2', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id, '2B0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask &   8 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '3', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask &  16 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '4', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask &  32 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '5', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask &  64 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '6', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask & 128 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   'A', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id, '1A0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask & 256 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   'B', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id, '2B0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;
        if  _region_mask & 512 then INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id,   'C', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type; end if;

        if _country != '-' then
            set _country = concat(_country,',-');
            set i = 1;
            lbl_country: loop
                set v = substring_index(substring_index(_country,',',i),',',-1);
                if v = '-' then leave lbl_country; end if;
                set i = i + 1;
                INSERT INTO search_redo_2 (dvd_id, region, media_type) VALUES(_dvd_id, v, _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
            end loop;
        end if;

        if _asin != '-' then
            INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'A', concat(_asin,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
        end if;

        if _imdb_id != '-' then
            set _imdb_id = concat(_imdb_id,' -');
            set i = 1;
            lbl_imdb: loop
                set v = substring_index(substring_index(_imdb_id,' ',i),' ',-1);
                if v = '-' then leave lbl_imdb; end if;
                set i = i + 1;
                INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'I', concat(v,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
            end loop;
        end if;

        if _upc != '-' then
            set _upc = concat(_upc,' -');
            set i = 1;
            lbl_upc: loop
                set v = substring_index(substring_index(_upc,' ',i),' ',-1);
                if v = '-' then leave lbl_upc; end if;
                set i = i + 1;
                INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'U', concat(v,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
            end loop;
        end if;

        if _publisher != '-' then
            set _publisher = concat(_publisher,'/-');
            set i = 1;
            lbl_pub: loop
                set u = trim(substring_index(substring_index(_publisher,'/',i),'/',-1));
                if u = '-' then leave lbl_pub; end if;
                INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'P', concat(u,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
                set i = i + 1;
                set k = -1;
                lbl_pub2: loop
                    set v = substring_index(u,' ',k);
                    set k = k - 1;
                    if u = v then leave lbl_pub2; end if;
                    INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'P', concat(v,' /'), 'N') ON DUPLICATE KEY UPDATE whole = whole;
                end loop;
            end loop;
        end if;

        if _director != '-' then
            set _director = concat(_director,'/-');
            set i = 1;
            lbl_dir: loop
                set u = trim(substring_index(substring_index(_director,'/',i),'/',-1));
                if u = '-' then leave lbl_dir; end if;
                INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'D', concat(u,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
                set i = i + 1;
                set k = -1;
                lbl_dir2: loop
                    set v = substring_index(u,' ',k);
                    set k = k - 1;
                    if u = v then leave lbl_dir2; end if;
                    INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'D', concat(v,' /'), 'N') ON DUPLICATE KEY UPDATE whole = whole;
                end loop;
            end loop;
        end if;

        if _dvd_title != '-' then
            set _dvd_title = concat(_dvd_title,'/-');
            set i = 1;
            lbl_tit: loop
                set u = trim(substr(trim(substring_index(substring_index(_dvd_title,'/',i),'/',-1)),1,200));
                if u = '-' then leave lbl_tit; end if;
                INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'V', concat(u,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
                set i = i + 1;
                set k = -1;
                lbl_tit2: loop
                    set v = substring_index(u,' ',k);
                    set k = k - 1;
                    if u = v then leave lbl_tit2; end if;
                    INSERT INTO search_redo_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'V', concat(v,' /'), 'N') ON DUPLICATE KEY UPDATE whole = whole;
                end loop;
            end loop;
        end if;

    end if;
    close _cur1;
end
//

delimiter ;

-- *********************************************************************************

delimiter //

DROP PROCEDURE IF EXISTS dvd_search_redo
//
CREATE PROCEDURE dvd_search_redo()
begin
    declare _done     int DEFAULT 0;
    declare _dvd_id   int DEFAULT 0;
    declare _cur1     cursor for SELECT dvd_id FROM search_redo_dvd;
    declare continue  handler for sqlstate '02000' set _done = 1;

	TRUNCATE TABLE search_redo_dvd;
	TRUNCATE TABLE search_redo_1;
	TRUNCATE TABLE search_redo_2;
	INSERT INTO search_redo_dvd SELECT dvd_id FROM dvd;

    open _cur1;
    fetch _cur1 into _dvd_id;

    while not _done do
        call dvd_search_redo_(_dvd_id);
        fetch _cur1 into _dvd_id;
    end while;

    close _cur1;
end
//

delimiter ;

-- *********************************************************************************

call dvd_search_redo();
-- (1 hour 25 min 22.00 sec)

OPTIMIZE TABLE search_redo_2;
OPTIMIZE TABLE search_redo_1;
ANALYZE TABLE search_redo_1;
ANALYZE TABLE search_redo_2;

TRUNCATE TABLE search_redo_dvd;


SELECT a.dvd_id missing from search_all_1 a where not exists (SELECT * from search_redo_1 b where a.dvd_id = b.dvd_id and a.obj_type = b.obj_type and a.nocase = b.nocase);
SELECT a.dvd_id excess from search_redo_1 a where not exists (SELECT * from search_all_1 b where a.dvd_id = b.dvd_id and a.obj_type = b.obj_type and a.nocase = b.nocase);
SELECT a.dvd_id diff from search_redo_1 a join search_all_1 b on a.dvd_id = b.dvd_id and a.obj_type = b.obj_type and a.nocase = b.nocase where a.whole != b.whole;

SELECT a.dvd_id missing from search_all_2 a where not exists (SELECT * from search_redo_2 b where a.dvd_id = b.dvd_id and a.region = b.region);
SELECT a.dvd_id excess from search_redo_2 a where not exists (SELECT * from search_all_2 b where a.dvd_id = b.dvd_id and a.region = b.region);
SELECT a.dvd_id diff from search_redo_2 a join search_all_2 b on a.dvd_id = b.dvd_id and a.region = b.region where a.media_type <> b.media_type;

-- *********************************************************************************
-- get results and compile updates in excel
-- run in master

call update_dvd_search_index(4625,1);
call update_dvd_search_index(5343,1);
call update_dvd_search_index(5532,1);
call update_dvd_search_index(9682,1);
call update_dvd_search_index(10976,1);
call update_dvd_search_index(11748,1);
call update_dvd_search_index(12231,1);
call update_dvd_search_index(12622,1);
call update_dvd_search_index(12625,1);
call update_dvd_search_index(16479,1);

call update_dvd_search_index(18060,1);
call update_dvd_search_index(21466,1);
call update_dvd_search_index(22700,1);
call update_dvd_search_index(24564,1);
call update_dvd_search_index(25495,1);
call update_dvd_search_index(26739,1);
call update_dvd_search_index(26740,1);
call update_dvd_search_index(26749,1);
call update_dvd_search_index(26750,1);
call update_dvd_search_index(33531,1);

call update_dvd_search_index(35545,1);
call update_dvd_search_index(36700,1);
call update_dvd_search_index(36781,1);
call update_dvd_search_index(38355,1);
call update_dvd_search_index(39219,1);
call update_dvd_search_index(50583,1);
call update_dvd_search_index(53252,1);
call update_dvd_search_index(56285,1);
call update_dvd_search_index(67410,1);
call update_dvd_search_index(69596,1);

call update_dvd_search_index(69747,1);
call update_dvd_search_index(70406,1);
call update_dvd_search_index(71446,1);
call update_dvd_search_index(73915,1);
call update_dvd_search_index(90302,1);
call update_dvd_search_index(93608,1);
call update_dvd_search_index(94720,1);
call update_dvd_search_index(94721,1);
call update_dvd_search_index(100393,1);
call update_dvd_search_index(102811,1);

call update_dvd_search_index(103099,1);
call update_dvd_search_index(103758,1);
call update_dvd_search_index(103842,1);
call update_dvd_search_index(104002,1);
call update_dvd_search_index(105581,1);
call update_dvd_search_index(105585,1);
call update_dvd_search_index(105777,1);
call update_dvd_search_index(106118,1);
call update_dvd_search_index(106158,1);
call update_dvd_search_index(107271,1);

call update_dvd_search_index(107272,1);
call update_dvd_search_index(107273,1);
call update_dvd_search_index(108923,1);
call update_dvd_search_index(113935,1);
call update_dvd_search_index(114746,1);
call update_dvd_search_index(115959,1);
call update_dvd_search_index(117372,1);
call update_dvd_search_index(117677,1);
call update_dvd_search_index(120466,1);
call update_dvd_search_index(121741,1);

call update_dvd_search_index(122198,1);
call update_dvd_search_index(124539,1);
call update_dvd_search_index(125428,1);
call update_dvd_search_index(126298,1);
call update_dvd_search_index(126506,1);
call update_dvd_search_index(126926,1);
call update_dvd_search_index(126932,1);
call update_dvd_search_index(126933,1);
call update_dvd_search_index(126980,1);
call update_dvd_search_index(126985,1);

call update_dvd_search_index(126999,1);
call update_dvd_search_index(127118,1);
call update_dvd_search_index(127242,1);
call update_dvd_search_index(127258,1);
call update_dvd_search_index(127457,1);
call update_dvd_search_index(127472,1);
call update_dvd_search_index(127702,1);
call update_dvd_search_index(127826,1);
call update_dvd_search_index(128307,1);
call update_dvd_search_index(128423,1);

call update_dvd_search_index(128478,1);
call update_dvd_search_index(128485,1);
call update_dvd_search_index(128698,1);
call update_dvd_search_index(128831,1);
call update_dvd_search_index(128948,1);
call update_dvd_search_index(128984,1);

-- *********************************************************************************
-- clean up

TRUNCATE TABLE search_redo_dvd;
TRUNCATE TABLE search_redo_1;
TRUNCATE TABLE search_redo_2;

-- *********************************************************************************
