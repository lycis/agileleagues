drop view if exists last_week_logs;

create view last_week_logs as
select `activity`.`id` AS `activity_id`, concat(
	(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and `log`.`acquired` = (curdate() - interval 1 day)))),',',
	(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 2 day)))),',',
	(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 3 day)))),',',
	(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 4 day)))),',',
	(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 5 day)))),',',
	(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 6 day)))),',',
	(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 7 day))))) AS `logs` 
from `activity` where (`activity`.`inactive` = 0) order by `activity`.`id`