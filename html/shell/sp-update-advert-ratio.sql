-- vi: ft=sql noet ai ts=4 sw=4 cindent

-- SHOW PROCEDURE STATUS;
-- SHOW CREATE PROCEDURE update_advert_ranges;
-- *********************************************************************************

delimiter //
DROP PROCEDURE IF EXISTS update_advert_ranges_
//

CREATE PROCEDURE update_advert_ranges_(in _loc varchar(16) binary)
begin
    declare _done       int DEFAULT 0;
    declare _vendor     varchar(8) binary DEFAULT '-';
    declare _advert_id  int DEFAULT 0;
    declare _ratio      int DEFAULT 1;
    declare _sum        int DEFAULT 0;
    declare _acc        int DEFAULT 0;
    declare _now        datetime;
    declare _range_beg  int DEFAULT 0;
    declare _range_end  int DEFAULT 0;
    declare _cur1       cursor for SELECT vendor, advert_id, ratio
                                     FROM advert
                                    WHERE effective_beg_tm <= _now and effective_end_tm > _now and (location1 = _loc or location2 = _loc or location3 = _loc);
    declare continue    handler for sqlstate '02000' set _done = 1;

    SELECT now() INTO _now;

    SELECT SUM(ratio) INTO _sum
      FROM advert
     WHERE effective_beg_tm <= _now and effective_end_tm > _now and (location1 = _loc or location2 = _loc or location3 = _loc);

    DELETE advert_range
      FROM advert_range
     WHERE not exists (SELECT *
                         FROM advert b
                        WHERE advert_range.vendor = b.vendor
                          and advert_range.advert_id = b.advert_id
                          and effective_beg_tm <= _now and effective_end_tm > _now and (location1 = _loc or location2 = _loc or location3 = _loc));

    open _cur1;
    fetch _cur1 into _vendor, _advert_id, _ratio;

    while not _done do
        set _acc       = _acc + _ratio;
        set _range_beg = _range_end + 1;
        set _range_end = _acc * 10000 / _sum;
        INSERT INTO advert_range (location, vendor, advert_id, range_beg, range_end) VALUES(_loc, _vendor, _advert_id, _range_beg, _range_end) ON DUPLICATE KEY UPDATE range_beg = _range_beg, range_end = _range_end;
        fetch _cur1 into _vendor, _advert_id, _ratio;
    end while;

    close _cur1;
end
//

delimiter ;

-- *********************************************************************************

delimiter //
DROP PROCEDURE IF EXISTS update_advert_ranges
//

CREATE PROCEDURE update_advert_ranges()
begin
    declare _done       int DEFAULT 0;
    declare _loc        varchar(16) binary DEFAULT '-';
    declare _now        datetime;
    declare _cur1       cursor for SELECT location1 FROM advert WHERE effective_beg_tm <= _now and effective_end_tm > _now and location1 != '-' UNION
                                   SELECT location2 FROM advert WHERE effective_beg_tm <= _now and effective_end_tm > _now and location2 != '-' UNION
                                   SELECT location3 FROM advert WHERE effective_beg_tm <= _now and effective_end_tm > _now and location3 != '-';
    declare continue    handler for sqlstate '02000' set _done = 1;

    SELECT now() INTO _now;

    DELETE advert_range
      FROM advert_range
     WHERE location not in (SELECT location1 FROM advert WHERE effective_beg_tm <= _now and effective_end_tm > _now and location1 != '-' UNION
                            SELECT location2 FROM advert WHERE effective_beg_tm <= _now and effective_end_tm > _now and location2 != '-' UNION
                            SELECT location3 FROM advert WHERE effective_beg_tm <= _now and effective_end_tm > _now and location3 != '-');

    open _cur1;
    fetch _cur1 into _loc;

    while not _done do
		call update_advert_ranges_(_loc);
		fetch _cur1 into _loc;
    end while;

    close _cur1;
end
//

delimiter ;

-- *********************************************************************************

CALL update_advert_ranges();

-- *********************************************************************************

