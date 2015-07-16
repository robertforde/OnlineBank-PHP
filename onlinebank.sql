-- phpMiniAdmin dump 1.9.150108
-- Datetime: 2015-03-20 13:11:42
-- Host: 
-- Database: onlinebank

/*!40030 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `username` varchar(20) NOT NULL DEFAULT '',
  `accNo` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`,`accNo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES ('Batman','22587092'),('Batman','76433053'),('Captain America','50700875'),('ironman','22927991'),('joebloggs','82756478'),('robforde123','19853864'),('robforde123','28547837'),('robforde123','31205638'),('robforde123','34323015'),('robforde123','37992592'),('robforde123','42985408'),('robforde123','61931402'),('robforde123','67652997'),('robforde123','89539869'),('robforde123','98847371'),('superman55','87693621'),('superman66','32633993'),('thehulk','18934713'),('wonderwoman33','75229371');
/*!40000 ALTER TABLE `account` ENABLE KEYS */;

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `accNo` int(11) DEFAULT NULL,
  `type` varchar(25) DEFAULT NULL,
  `tranAccNo` int(11) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `comment` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES ('1','2015-03-16','robforde123','19853864','Lodgement',NULL,'100','to open account'),('2','2015-03-16','robforde123','19853864','Withdrawal',NULL,'10','Needed a 10er'),('3','2015-03-16','robforde123','19853864','Lodgement',NULL,'10.5','Spare'),('4','2015-03-16','robforde123','19853864','Lodgement',NULL,'50.67','Change'),('5','2015-03-16','robforde123','19853864','Lodgement',NULL,'99.15','Nearly a hundred'),('6','2015-03-16','robforde123','19853864','Withdrawal',NULL,'10','I need 10 please'),('7','2015-03-16','robforde123','19853864','Lodgement',NULL,'20','i have 20 to lodge'),('8','2015-03-17','robforde123','19853864','Lodgement',NULL,'1999','Won this'),('9','2015-03-17','robforde123','19853864','Lodgement',NULL,'380.5','Saving'),('10','2015-03-17','robforde123','19853864','Withdrawal',NULL,'200','Need some cash'),('11','2015-03-17','robforde123','19853864','Withdrawal',NULL,'1','test withdrawal'),('12','2015-03-17','robforde123','19853864','Withdrawal',NULL,'11','another test'),('13','2015-03-17','robforde123','19853864','Withdrawal',NULL,'343',''),('14','2015-03-17','robforde123','19853864','Lodgement',NULL,'92.1','232132432'),('15','2015-03-17','robforde123','19853864','Transfer Out','22587092','95.4','test transfer'),('16','2015-03-17','Batman','22587092','Transfer In','19853864','95.4','test transfer'),('17','2015-03-19','robforde123','19853864','Lodgement',NULL,'100','4545686'),('18','2015-03-19','robforde123','19853864','Transfer Out','22587092','95.2','I owe it'),('19','2015-03-19','robforde123','22587092','Transfer In','19853864','95.2','I owe it'),('20','2015-03-19','robforde123','19853864','Transfer Out','98847371','250','Pay back'),('21','2015-03-19','robforde123','98847371','Transfer In','19853864','250','Pay back'),('22','2015-03-19','robforde123','19853864','Lodgement',NULL,'120','lodge please'),('23','2015-03-19','robforde123','19853864','Withdrawal',NULL,'55.5','I need this');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(20) DEFAULT NULL,
  `title` varchar(4) DEFAULT NULL,
  `forename` varchar(20) DEFAULT NULL,
  `surname` varchar(20) DEFAULT NULL,
  `addr1` varchar(30) DEFAULT NULL,
  `addr2` varchar(30) DEFAULT NULL,
  `addr3` varchar(30) DEFAULT NULL,
  `addr4` varchar(30) DEFAULT NULL,
  `sec_question` varchar(100) DEFAULT NULL,
  `sec_answer` varchar(20) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('robforde123','password01','Mr','Robert','Forde','14 Ayrfield Road','Dublin 13','','','What is the name of your first school?','Our Lady Immaculate','robert-forde@hotmail.com'),('joebloggs','password02','Mr','Joe','Bloggs','252 Main Street','Dumfadden Road','Cobh','Co. Cork','What is the name of your first pet?','Spot','joebloggs@gmail.com'),('superman55','password03','greg','kljnkl','nknk','ln','klmnklnb','jkbhb','bjkn,','nm,nkln','nkljnlkhjihuh','superman@hotmail.com'),('spiderman66','password04','pkwf','l;ewfe','kl;fwewfe','sadfsed','dsa','sad','sadsad','rewrewre','wrewrew','lfewfe'),('wonderwoman33','password05','uhuh','ujkbnj','bj','bj','bj','lb','jk','bj','bjk','gregre'),('thehulk','password06',';hji','hl;hn','kjl','nhljk','nj','klb','jkb','jbjk','b;','fgrwafj'),('ironman','password07','jbhj','gyjgj','hb','jkj','iu','8y','g','hjjhg','gkj','lkjki'),('Captain America','password08','Mr','Captain','America','Who Knows','','','','What does my shield do?','cool stuff','captain@gmail.com'),('Batman','password09','Mr','Bruce','Wayne','Big House','Mansion Road','Expensive Town','Gotham','If I am a bat then why can I not fly?','I am also a man','batman@hotmail.com');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;


-- phpMiniAdmin dump end
