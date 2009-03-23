create view poii_reviewcount AS SELECT sum( `totalreview` ) AS `cnt` , `country` AS `country`
FROM `poii_xml`
WHERE `poi_id` <>0
AND `gotit` =1
AND `totalreview` >0
GROUP BY `country`;

create view poii_nofound AS SELECT count( `country` ) AS `cnt` , `country` AS `country`
FROM `poii_xml`
WHERE `poi_id` <>0
AND `gotit` =0
GROUP BY `country`;


create view poii_noreview AS select count(`country`) AS `cnt`,`country` AS `country` from `poii_xml` where `poi_id` <> 0 and `gotit` = 1 and `totalreview` < 1 group by `country`;


create view poii_review AS SELECT count( `country` ) AS `cnt` , `country` AS `country`
FROM `poii_xml`
WHERE `poi_id` <>0
AND `gotit` =1
AND `totalreview` >0
GROUP BY `country`;

create view poii_total AS SELECT count( `country` ) AS `cnt` , `country` AS `country`
FROM `poii_xml`
WHERE `poi_id` <>0
GROUP BY `country`;