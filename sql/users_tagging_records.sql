-- MySQL dump 10.13  Distrib 5.5.37-35.0, for Linux (x86_64)
--
-- Host: localhost    Database: crawler
-- ------------------------------------------------------
-- Server version	5.5.37-35.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Final view structure for view `users_tagging_records`
--

/*!50001 DROP TABLE IF EXISTS `users_tagging_records`*/;
/*!50001 DROP VIEW IF EXISTS `users_tagging_records`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`crawler`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `users_tagging_records` AS select `rut`.`record_id` AS `record_id`,`r`.`pagetitle` AS `pagetitle`,`r`.`recordtitle` AS `recordtitle`,`r`.`oid` AS `oid`,`r`.`iid` AS `iid`,`u`.`username` AS `username`,`t`.`text` AS `tag` from (((`record_user_tags` `rut` left join `records` `r` on((`r`.`id` = `rut`.`record_id`))) left join `users` `u` on((`u`.`id` = `rut`.`user_id`))) left join `tags` `t` on((`t`.`id` = `rut`.`tag_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-24  9:19:11
