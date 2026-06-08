-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: university_portal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `university_portal`
--

/*!40000 DROP DATABASE IF EXISTS `university_portal`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `university_portal` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `university_portal`;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `class_date` date NOT NULL,
  `status` varchar(40) NOT NULL,
  PRIMARY KEY (`attendance_id`),
  KEY `idx_attendance_student_id` (`student_id`),
  KEY `idx_attendance_section_id` (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (73,8,7,'2026-05-09','Present'),(74,8,7,'2026-05-16','Present'),(75,8,7,'2026-05-23','Absent'),(76,8,7,'2026-05-30','Present'),(77,8,8,'2026-05-10','Present'),(78,8,8,'2026-05-17','Present'),(79,8,8,'2026-05-24','Absent'),(80,8,8,'2026-05-31','Present'),(81,8,9,'2026-05-11','Present'),(82,8,9,'2026-05-18','Present'),(83,8,9,'2026-05-25','Absent'),(84,8,9,'2026-06-01','Present'),(85,9,7,'2026-05-09','Present'),(86,9,7,'2026-05-16','Absent'),(87,9,7,'2026-05-23','Present'),(88,9,7,'2026-05-30','Present'),(89,9,8,'2026-05-10','Present'),(90,9,8,'2026-05-17','Absent'),(91,9,8,'2026-05-24','Present'),(92,9,8,'2026-05-31','Present'),(93,9,9,'2026-05-11','Present'),(94,9,9,'2026-05-18','Absent'),(95,9,9,'2026-05-25','Present'),(96,9,9,'2026-06-01','Present'),(97,10,7,'2026-05-09','Absent'),(98,10,7,'2026-05-16','Present'),(99,10,7,'2026-05-23','Present'),(100,10,7,'2026-05-30','Present'),(101,10,8,'2026-05-10','Absent'),(102,10,8,'2026-05-17','Present'),(103,10,8,'2026-05-24','Present'),(104,10,8,'2026-05-31','Present'),(105,10,9,'2026-05-11','Absent'),(106,10,9,'2026-05-18','Present'),(107,10,9,'2026-05-25','Present'),(108,10,9,'2026-06-01','Present');
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(40) NOT NULL,
  `title` varchar(180) NOT NULL,
  `credit` int(11) NOT NULL,
  PRIMARY KEY (`course_id`),
  UNIQUE KEY `course_code` (`course_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,'CSE101','Introduction to Programming',3),(2,'MAT101','Calculus I',3),(3,'ENG101','Academic English',3),(4,'CSE201','Data Structures and Algorithms',3),(5,'BUS210','Principles of Marketing',3),(6,'ENG205','Business Communication',3);
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(160) NOT NULL,
  `location` varchar(160) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `category` varchar(80) NOT NULL DEFAULT 'Academic',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`event_id`),
  KEY `idx_events_event_date` (`event_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Orientation and advising clinic','Auditorium','2026-06-11','10:00:00','Academic','2026-06-06 04:50:04'),(2,'Career services workshop','Room 402','2026-06-18','14:30:00','Career','2026-06-06 04:50:04');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faculty`
--

DROP TABLE IF EXISTS `faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faculty` (
  `faculty_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `department` varchar(120) NOT NULL,
  PRIMARY KEY (`faculty_id`),
  KEY `idx_faculty_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculty`
--

LOCK TABLES `faculty` WRITE;
/*!40000 ALTER TABLE `faculty` DISABLE KEYS */;
INSERT INTO `faculty` VALUES (7,15,'Computer Science and Engineering'),(8,16,'Business Administration'),(9,17,'English and Humanities');
/*!40000 ALTER TABLE `faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_books`
--

DROP TABLE IF EXISTS `library_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `author` varchar(140) NOT NULL,
  `isbn` varchar(40) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `total_copies` int(11) NOT NULL DEFAULT 1,
  `available_copies` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_books`
--

LOCK TABLES `library_books` WRITE;
/*!40000 ALTER TABLE `library_books` DISABLE KEYS */;
INSERT INTO `library_books` VALUES (1,'Database System Concepts','Silberschatz, Korth, Sudarshan','9780073523323','CSE',6,4,'2026-06-06 04:50:04'),(2,'Clean Code','Robert C. Martin','9780132350884','CSE',5,3,'2026-06-06 04:50:04'),(3,'Principles of Economics','N. Gregory Mankiw','9781305585126','Business',4,2,'2026-06-06 04:50:04');
/*!40000 ALTER TABLE `library_books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notices` (
  `notice_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(160) NOT NULL,
  `body` text NOT NULL,
  `audience` enum('all','admin','faculty','student') NOT NULL DEFAULT 'all',
  `priority` enum('normal','important','urgent') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notice_id`),
  KEY `idx_notices_audience_created` (`audience`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notices`
--

LOCK TABLES `notices` WRITE;
/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
INSERT INTO `notices` VALUES (1,'Registration window open','Students can review available courses and complete advising from the portal.','student','important','2026-06-06 04:50:04'),(2,'Faculty grade deadline','All pending results should be submitted before the end of the current academic week.','faculty','normal','2026-06-06 04:50:04'),(3,'Administrative audit','Please verify student, faculty, course, and section records for the current semester.','admin','normal','2026-06-06 04:50:04'),(4,'test notice','This is a test notice. \r\nThank you for your attention to this matter !!!','all','urgent','2026-06-08 07:37:58');
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `idx_payments_reg_id` (`reg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (19,19,18500.00,'Paid','2026-06-06 06:15:13'),(20,20,19250.00,'Paid','2026-06-06 06:15:13'),(21,21,20000.00,'Paid','2026-06-06 06:15:13'),(22,22,17500.00,'Paid','2026-06-06 06:15:13'),(23,23,18250.00,'Paid','2026-06-06 06:15:13'),(24,24,19000.00,'Paid','2026-06-06 06:15:13'),(25,25,16500.00,'Unpaid','2026-06-06 06:15:13'),(26,26,17250.00,'Unpaid','2026-06-06 06:15:13'),(27,27,18000.00,'Unpaid','2026-06-06 06:15:13');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrations` (
  `reg_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reg_id`),
  KEY `idx_registrations_student_id` (`student_id`),
  KEY `idx_registrations_section_id` (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrations`
--

LOCK TABLES `registrations` WRITE;
/*!40000 ALTER TABLE `registrations` DISABLE KEYS */;
INSERT INTO `registrations` VALUES (19,8,7,'Approved','2026-06-06 06:15:13'),(20,8,8,'Approved','2026-06-06 06:15:13'),(21,8,9,'Approved','2026-06-06 06:15:13'),(22,9,7,'Approved','2026-06-06 06:15:13'),(23,9,8,'Approved','2026-06-06 06:15:13'),(24,9,9,'Approved','2026-06-06 06:15:13'),(25,10,7,'Pending','2026-06-06 06:15:13'),(26,10,8,'Pending','2026-06-06 06:15:13'),(27,10,9,'Pending','2026-06-06 06:15:13');
/*!40000 ALTER TABLE `registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `results` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `gpa` decimal(3,2) NOT NULL,
  PRIMARY KEY (`result_id`),
  KEY `idx_results_student_id` (`student_id`),
  KEY `idx_results_section_id` (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results`
--

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;
INSERT INTO `results` VALUES (19,8,7,'A',4.00),(20,8,8,'A-',3.70),(21,8,9,'B+',3.30),(22,9,7,'B+',3.30),(23,9,8,'A-',3.70),(24,9,9,'B',3.00),(25,10,7,'A-',3.70),(26,10,8,'B+',3.30),(27,10,9,'A',4.00);
/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `section_name` varchar(40) NOT NULL,
  `schedule` varchar(160) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 40,
  `status` varchar(40) NOT NULL DEFAULT 'Open',
  PRIMARY KEY (`section_id`),
  KEY `idx_sections_course_id` (`course_id`),
  KEY `idx_sections_faculty_id` (`faculty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (7,4,7,'A','Sun Tue 10:00-11:30',35,'Open'),(8,5,8,'B','Mon Wed 12:00-13:30',40,'Open'),(9,6,9,'C','Sun Thu 14:00-15:30',32,'Open');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `program` varchar(120) NOT NULL,
  `semester` int(11) NOT NULL,
  `cgpa` decimal(3,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`student_id`),
  KEY `idx_students_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,2,'CSE',1,3.80),(8,18,'BSc in Computer Science and Engineering',5,3.72),(9,19,'BBA in Finance',4,3.48),(10,20,'BA in English',3,3.86);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subject` varchar(180) NOT NULL,
  `category` varchar(80) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Open','In Review','Resolved') NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ticket_id`),
  KEY `idx_support_tickets_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (5,18,'ID card collection','Academic','I need confirmation about my new student ID card collection date.','Open','2026-06-06 06:15:13','2026-06-06 06:15:13'),(6,15,'Projector issue in Room 402','Technical','The classroom projector needs maintenance before the next lecture.','In Review','2026-06-06 06:15:13','2026-06-06 06:15:13');
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','faculty','student') NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Portal Admin','admin@gmail.com','$2y$10$vWdq5JN4LzPlTGq8UgF8cePPEB8kWeTgPbQjbiEcZsCuLeZhhPD/e','admin'),(2,'Mahim','mahim@m.ca','$2y$10$5M8OxBl2/crnbtgVx9Q5wewj9AirsIGTmN6mVtF4VxaxDVBVye06G','student'),(15,'Dr. Mahmudul Karim','mahmudul.karim@uiu.com','$2y$10$iF22/pEUSIj/2uDv.DMxmOZVJD9rUK7vmila8g0RLrLwyrHUX4c/W','faculty'),(16,'Prof. Nusrat Jahan','nusrat.jahan@uiu.com','$2y$10$TE/0K8Ad2majlymbHuOMOun4eQAdopk2xBYpDisYEem9afzNpWUzO','faculty'),(17,'Dr. Rezaul Haque','rezaul.haque@uiu.com','$2y$10$e5AAOGPkmP/c0EkvmQdqeO.92xvmvLX40gnDJ1hIWkun6d6eDIHwW','faculty'),(18,'Tanvir Hossain','tanvir.hossain@uiu.com','$2y$10$scsQbj/lbuTL8Xts6eGzi.FLnhy37HnzxtgO3ls6vGKI4KH3Y6rWS','student'),(19,'Mehjabin Akter','mehjabin.akter@uiu.com','$2y$10$XPx1FPoq3uzE9PsC9KYSn.YgoqfMShi3t5d0mb.g14eE73GXphSQO','student'),(20,'Samiul Islam','samiul.islam@uiu.com','$2y$10$MoeZLHhRc/LHAmBTwEPct.40K4BHU3J6brVMHZDiTOztM0tDkjzca','student');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'university_portal'
--

--
-- Dumping routines for database 'university_portal'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-08 13:42:48
