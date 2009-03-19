CREATE VIEW `ip_nofound` AS select count(`property_xml`.`country`) AS `cnt`,`property_xml`.`country` AS `country` from `property_xml` where ((`property_xml`.`hotel_id` <> 0) and (`property_xml`.`gotit` = 0) and (`property_xml`.`flag` = 1)) group by `property_xml`.`country`;


CREATE VIEW `ip_noreview` AS select count(`property_xml`.`country`) AS `cnt`,`property_xml`.`country` AS `country` from `property_xml` where ((`property_xml`.`hotel_id` <> 0) and (`property_xml`.`gotit` = 1) and (`property_xml`.`totalreview` < 1) and (`property_xml`.`flag` = 1)) group by `property_xml`.`country`;


CREATE VIEW `ip_review` AS select count(`property_xml`.`country`) AS `cnt`,`property_xml`.`country` AS `country` from `property_xml` where ((`property_xml`.`hotel_id` <> 0) and (`property_xml`.`gotit` = 1) and (`property_xml`.`totalreview` > 0) and (`property_xml`.`flag` = 1)) group by `property_xml`.`country`;


CREATE VIEW `ip_total` AS select count(`property_xml`.`country`) AS `cnt`,`property_xml`.`country` AS `country` from `property_xml` where ((`property_xml`.`hotel_id` <> 0) and (`property_xml`.`flag` = 1)) group by `property_xml`.`country`;

CREATE VIEW `ipreviewcount` AS select sum(`property_xml`.`totalreview`) AS `cnt`,`property_xml`.`country` AS `country` from `property_xml` where ((`property_xml`.`hotel_id` <> 0) and (`property_xml`.`flag` = 1) and(`property_xml`.`totalreview` > 0))  group by `property_xml`.`country`;