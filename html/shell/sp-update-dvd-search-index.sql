-- vi: ft=sql noet ai ts=4 sw=4 cindent

-- SHOW PROCEDURE STATUS;
-- SHOW CREATE PROCEDURE update_dvd_search_index;

delimiter //
drop procedure if exists update_dvd_search_index
//

create procedure update_dvd_search_index(in _dvd_id int, in _is_update int)
begin
    declare _done           int DEFAULT 0;
    declare _abs_id         int DEFAULT 0;
    declare _dvd_title      VARCHAR(2002) BINARY;				-- 2000 + 2
    declare _director       VARCHAR(502) BINARY DEFAULT '-';	--  500 + 2
    declare _director2      VARCHAR(504) BINARY DEFAULT '-';	--  500 + 4
    declare _publisher      VARCHAR(130) BINARY DEFAULT '-';	--  128 + 2
    declare _publisher2     VARCHAR(132) BINARY DEFAULT '-';	--  128 + 4
    declare _upc            VARCHAR(130) BINARY DEFAULT '-';	--  128 + 2
    declare _imdb_id        VARCHAR(502) BINARY DEFAULT '-';	--  500 + 2
    declare _asin           VARCHAR(18) BINARY DEFAULT '-';		--   16 + 2
    declare _country        VARCHAR(34) BINARY DEFAULT '-';		--   32 + 2
    declare _country2       VARCHAR(36) BINARY DEFAULT '-';		--   32 + 4
    declare _language2      VARCHAR(36) BINARY DEFAULT '-';		--   32 + 4
    declare _region_mask    SMALLINT DEFAULT 0;
    declare _media_type     CHAR(1) BINARY DEFAULT '-';

    declare i, k            int DEFAULT 0;
    declare u, v            VARCHAR(506) BINARY;				--  504 + 2

    declare _cur1 cursor for SELECT trim(trim(both '/' from dvd_title_nocase)),
                                    trim(trim(both '/' from director_nocase)),
                                    trim(trim(both '/' from publisher_nocase)),
                                    upc,
                                    imdb_id,
                                    lower(asin),
                                    trim(trim(both ',' from country_block)),
                                    media_type,
                                    region_mask,
									concat(director,',-'),
									concat(publisher,',-'),
									concat(country,',-'),
									concat(orig_language,',-')
                                FROM dvd
                               WHERE dvd_id = _dvd_id;
    declare continue handler for sqlstate '02000' set _done = 1;

    open _cur1;
    fetch _cur1 into _dvd_title, _director, _publisher, _upc, _imdb_id, _asin, _country, _media_type, _region_mask, _director2, _publisher2, _country2, _language2;
    if not _done then

		set _abs_id = _dvd_id;
        if _is_update = 1 then
            set _dvd_id = - _dvd_id;
            delete from search_all_1 where dvd_id = _dvd_id;
            delete from search_all_2 where dvd_id = _dvd_id;
        end if;

        if  _media_type != 'D' and _media_type != 'B' and _media_type != 'H' then
            if _media_type = 'C' or _media_type = 'T' then
                set _media_type = 'H';
            else
                set _media_type = 'O';
            end if;
        end if;

        if  _region_mask &   1 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id, '1A0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id, '2B0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   '0'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask &   2 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '1', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id, '1A0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   '1'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask &   4 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '2', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id, '2B0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   '2'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask &   8 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '3', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   '3'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask &  16 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '4', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   '4'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask &  32 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '5', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   '5'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask &  64 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   '6', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   '6'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask & 128 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   'A', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id, '1A0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   'A'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask & 256 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   'B', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
                                    INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id, '2B0', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   'B'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

        if  _region_mask & 512 then INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id,   'C', _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
									INSERT INTO stats_dvd_region (dvd_id, region)		  VALUES(_abs_id,   'C'				) ON DUPLICATE KEY UPDATE dvd_id = dvd_id;			end if;

		delete from stats_dvd_language where dvd_id = _abs_id;
		if _language2 != '-' then
		set i = 1;
			lbl_language2: loop
				set u = trim(substring_index(substring_index(_language2,',',i),',',-1));
				if u = '-' then leave lbl_language2; end if;
				if u != '' then INSERT INTO stats_dvd_language (dvd_id, language) VALUES(_abs_id, u) ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
				set i = i + 1;
			end loop;
		end if;

        if _country != '-' then
            set _country = concat(_country,',-');
            set i = 1;
            lbl_country: loop
                set v = substring_index(substring_index(_country,',',i),',',-1);
                if v = '-' then leave lbl_country; end if;
                set i = i + 1;
                INSERT INTO search_all_2 (dvd_id, region, media_type) VALUES(_dvd_id, v, _media_type) ON DUPLICATE KEY UPDATE media_type = _media_type;
            end loop;
        end if;

		delete from stats_dvd_country where dvd_id = _abs_id;
		if _country2 != '-' then
			set i = 1;
			lbl_country2: loop
			set u = trim(substring_index(substring_index(_country2,',',i),',',-1));
			if u = '-' then leave lbl_country2; end if;
			if u != '' then INSERT INTO stats_dvd_country (dvd_id, country) VALUES(_abs_id, u) ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
			set i = i + 1;
			end loop;
		end if;

        if _asin != '-' then
            INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'A', concat(_asin,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
        end if;

        if _imdb_id != '-' then
            set _imdb_id = concat(_imdb_id,' -');
            set i = 1;
            lbl_imdb: loop
                set v = substring_index(substring_index(_imdb_id,' ',i),' ',-1);
                if v = '-' then leave lbl_imdb; end if;
                set i = i + 1;
                INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'I', concat(v,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
            end loop;
        end if;

        if _upc != '-' then
            set _upc = concat(_upc,' -');
            set i = 1;
            lbl_upc: loop
                set v = substring_index(substring_index(_upc,' ',i),' ',-1);
                if v = '-' then leave lbl_upc; end if;
                set i = i + 1;
                INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'U', concat(v,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
            end loop;
        end if;

        if _publisher != '-' then
            set _publisher = concat(_publisher,'/-');
            set i = 1;
            lbl_pub: loop
                set u = trim(substring_index(substring_index(_publisher,'/',i),'/',-1));
                if u = '-' then leave lbl_pub; end if;
                INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'P', concat(u,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
                set i = i + 1;
                set k = -1;
                lbl_pub2: loop
                    set v = substring_index(u,' ',k);
                    set k = k - 1;
                    if u = v then leave lbl_pub2; end if;
                    INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'P', concat(v,' /'), 'N') ON DUPLICATE KEY UPDATE whole = 'N';
                end loop;
            end loop;
        end if;

		delete from stats_dvd_pub where dvd_id = _abs_id;
		if _publisher2 != '-,-' then
			set i = 1;
			lbl_pub2: loop
				set u = trim(substring_index(substring_index(_publisher2,',',i),',',-1));
				if u = '-' then leave lbl_pub2; end if;
				if u != '' then INSERT INTO stats_dvd_pub (dvd_id, publisher) VALUES(_abs_id, u) ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
				set i = i + 1;
			end loop;
		end if;

        if _director != '-' then
            set _director = concat(_director,'/-');
            set i = 1;
            lbl_dir: loop
                set u = trim(substring_index(substring_index(_director,'/',i),'/',-1));
                if u = '-' then leave lbl_dir; end if;
                INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'D', concat(u,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
                set i = i + 1;
                set k = -1;
                lbl_dir2: loop
                    set v = substring_index(u,' ',k);
                    set k = k - 1;
                    if u = v then leave lbl_dir2; end if;
                    INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'D', concat(v,' /'), 'N') ON DUPLICATE KEY UPDATE whole = 'N';
                end loop;
            end loop;
        end if;

		delete from stats_dvd_dir where dvd_id = _abs_id;
		if _director2 != '-,-' then
			set i = 1;
			lbl_dir2: loop
				set u = trim(substring_index(substring_index(_director2,',',i),',',-1));
				if u = '-' then leave lbl_dir2; end if;
				if u != '' and INSTR(u,'(-)') = 0 then INSERT INTO stats_dvd_dir (dvd_id, director) VALUES(_abs_id, u) ON DUPLICATE KEY UPDATE dvd_id = dvd_id; end if;
				set i = i + 1;
			end loop;
		end if;

        if _dvd_title != '-' then
            set _dvd_title = concat(_dvd_title,'/-');
            set i = 1;
            lbl_tit: loop
                set u = trim(substring_index(substring_index(_dvd_title,'/',i),'/',-1));
                if u = '-' then leave lbl_tit; end if;
				set u = substring(u, 1, 200);
                INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'V', concat(u,' /'), 'Y') ON DUPLICATE KEY UPDATE whole = 'Y';
                set i = i + 1;
                set k = -1;
                lbl_tit2: loop
                    set v = substring_index(u,' ',k);
                    set k = k - 1;
                    if u = v then leave lbl_tit2; end if;
                    INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES(_dvd_id, 'V', concat(v,' /'), 'N') ON DUPLICATE KEY UPDATE whole = 'N';
                end loop;
            end loop;
        end if;

        if _is_update = 1 then
            set _dvd_id = - _dvd_id;

            delete from search_all_1 where dvd_id = _dvd_id;
            update search_all_1 set dvd_id = _dvd_id where dvd_id = - _dvd_id;

            delete from search_all_2 where dvd_id = _dvd_id;
            update search_all_2 set dvd_id = _dvd_id where dvd_id = - _dvd_id;
        end if;

    end if;
    close _cur1;
end
//

delimiter ;


