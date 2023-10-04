-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: yavuzlar_obs
-- ------------------------------------------------------
-- Server version	8.0.34

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL,
  `class_teacher_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_teacher_id_idx` (`class_teacher_id`),
  CONSTRAINT `class_teacher_id` FOREIGN KEY (`class_teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (23,'ZAYOTEM 2023',74),(24,'YAVUZLAR 2023',75),(25,'CYBERYUM 2023',76);
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes_students`
--

DROP TABLE IF EXISTS `classes_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes_students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `class_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id_idx` (`student_id`),
  KEY `class_id_idx` (`class_id`),
  CONSTRAINT `class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `student_id` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes_students`
--

LOCK TABLES `classes_students` WRITE;
/*!40000 ALTER TABLE `classes_students` DISABLE KEYS */;
INSERT INTO `classes_students` VALUES (18,77,23),(19,78,23),(20,79,24),(21,80,24),(22,81,25),(23,82,25);
/*!40000 ALTER TABLE `classes_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `lesson_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `exam_score` tinyint NOT NULL,
  `exam_date` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_student_id_idx` (`student_id`),
  KEY `exam_lesson_id_idx` (`lesson_id`),
  KEY `exam_class_id_idx` (`class_id`),
  CONSTRAINT `exam_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `exam_lesson_id` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`),
  CONSTRAINT `exam_student_id` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES (26,77,19,23,80,'2023-10-04 20:03:00'),(27,77,20,23,22,'2023-10-04 11:03:00'),(28,78,19,23,21,'2023-10-05 20:03:00'),(29,78,20,23,98,'2023-10-11 12:03:00'),(30,79,17,24,21,'2023-10-05 20:03:00'),(31,79,18,24,88,'2023-10-10 20:04:00'),(32,80,17,24,15,'2023-10-18 11:04:00'),(33,80,18,24,100,'2023-10-20 20:04:00'),(34,81,21,25,99,'2023-10-19 09:05:00'),(35,81,22,25,88,'2023-10-13 20:05:00'),(36,82,21,25,71,'2023-10-12 20:05:00'),(37,82,22,25,89,'2023-10-18 11:05:00');
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lessons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `teacher_user_id` int DEFAULT NULL,
  `lesson_name` varchar(225) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id_idx` (`teacher_user_id`),
  CONSTRAINT `users_id` FOREIGN KEY (`teacher_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` VALUES (17,75,'Web Security'),(18,75,'SQL Injection'),(19,74,'Reverse Engineering'),(20,74,'Viruses'),(21,76,'Blockchain 101'),(22,76,'Smart Contract');
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(225) DEFAULT NULL,
  `surname` varchar(225) DEFAULT NULL,
  `username` varchar(225) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'boran','mesum','root','$argon2id$v=19$m=65536,t=4,p=1$WEdXSEI1UGVvM2M0bnY4Sw$0u7HHFxK8RUbsAXwPwzQKk/D01tLCpeFLP7+vLVlQys','admin','2023-09-29 11:30:45'),(74,'t1','t1','t1','$argon2id$v=19$m=65536,t=4,p=1$ZlA0cWFEZUlNM3o0cjNSZw$OUYBg/fwQD8ir4+EbziQW4Jky2hy1YoufELjqFRsrwc','teacher','2023-10-03 18:56:03'),(75,'t2','t2','t2','$argon2id$v=19$m=65536,t=4,p=1$S2p4SGI2blZkWEFqZi5wdg$FRORx7TIoCtGSljqyT8+zUyf4BcG3Qv9hhCtYWiLqhc','teacher','2023-10-03 18:56:08'),(76,'t3','t3','t3','$argon2id$v=19$m=65536,t=4,p=1$VHBkQk40by9RUkFnNWIxRA$a77QsRkvvMDbnVMz9F1xpI7evQN9NDZo9WPERxnCpnI','teacher','2023-10-03 18:56:13'),(77,'s1','s1','s1','$argon2id$v=19$m=65536,t=4,p=1$M09BQ2NDeWg3dHdWdll0Sg$ltU2Xx+kviENvzr/pETqxtisV7TmJjpkYPIT2/93XVA','student','2023-10-03 18:59:59'),(78,'s2','s2','s2','$argon2id$v=19$m=65536,t=4,p=1$QUN2RVdod0lxZzVVVFF0VQ$8SiT5GGcZJpxVWjUIWArIflKML38bPqbVpldEGsqaxs','student','2023-10-03 19:00:03'),(79,'s3','s3','s3','$argon2id$v=19$m=65536,t=4,p=1$TFB6UUNJOUdRb1VTalkyOA$EhJl9VRojzfsyYNl7hZdt0PNTH8nzQRTB2N2xMdOt74','student','2023-10-03 19:00:07'),(80,'s4','s4','s4','$argon2id$v=19$m=65536,t=4,p=1$S0p5RU5HblFaRkMvdGpULg$GcavzsrFnToc85dn0VlGLEKo2yW31+RuneCFJ0ZRqRg','student','2023-10-03 19:00:11'),(81,'s5','s5','s5','$argon2id$v=19$m=65536,t=4,p=1$RHZtaUlYblhYNk1JZ0ZwcQ$P7LOW6CERUclG3E6izk2RNEgYlSBQLLZ/OM2ugdAKAs','student','2023-10-03 19:00:16'),(82,'s6','s6','s6','$argon2id$v=19$m=65536,t=4,p=1$dUJRQjR5NlFHT0k4cVJ1Wg$7MO66Oyu2GbD1GPGwBU/K7yy/5NAHUmc7YSFjGhRCLE','student','2023-10-03 19:00:20');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-10-04 12:37:38
