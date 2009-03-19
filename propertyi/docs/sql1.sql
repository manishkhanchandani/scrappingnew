select count(*) from property_xml where country='in' and flag = 1 and hotel_id != 0

select count(*) from property_xml where country='cn' and flag = 1 and hotel_id != 0

select count(*) from property_xml where country IN ('kg', 'kh', 'kr', 'kw', 'kz', 'la', 'lb', 'lk', 'mm', 'mn', 'mo', 'mv', 'my', 'np', 'om', 'ph', 'pk', 'qa', 'sa', 'sg', 'sy') and hotel_id != 0 and flag = 1 

select count(*) from property_xml where country IN ('ge', 'hk') and hotel_id != 0 and flag = 1 