DROP TABLE IF EXISTS responders;
DROP TABLE IF EXISTS responderextradatas;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS feedbacks;
DROP TABLE IF EXISTS feedbackdims;
DROP TABLE IF EXISTS dimensiontypes;
DROP TABLE IF EXISTS dimensiondatas;
DROP TABLE IF EXISTS dimensiondatagroups;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS divisions;
DROP TABLE IF EXISTS logs;
CREATE TABLE logs (
  id int(11) NOT NULL auto_increment,
  created varchar(255) default NULL,
  username varchar(255) default NULL,
  result varchar(255) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE divisions (
  id int(11) NOT NULL auto_increment,
  companyId int(11) default NULL,
  name varchar(255) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
INSERT INTO divisions VALUES (1,13,'Programmers');
CREATE TABLE companies (
  id int(11) NOT NULL auto_increment,
  parentId int(11) default NULL,
  login varchar(255) default NULL,
  password varchar(255) default NULL,
  contactName varchar(255) default NULL,
  contactPhone varchar(255) default NULL,
  contactEmail varchar(255) default NULL,
  lastPasswordChange int(11) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
INSERT INTO companies VALUES (1,'a@a.com','1234',NULL,NULL,NULL),(7,'Yositilint','yosi123','Yosi Wigman',NULL,'yosi@tilint.com'),(11,'Codetix','123',NULL,NULL,NULL),(13,'admin','d213sad123','Nimrod',NULL,'nimrod@mezoo.co');
CREATE TABLE dimensiondatagroups (
  id int(11) NOT NULL auto_increment,
  attrGroupName varchar(255) default NULL,
  companyDivisionId int(11) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
INSERT INTO dimensiondatagroups VALUES (1,'Menahel_Matnas-Yoshra',7),(3,'Shomer–Merck',7),(5,'Low',11),(6,'Hi',11),(7,'Normal',11),(9,'yoshra1',7),(10,'yoshra2',7);
CREATE TABLE dimensiondatas (
  id int(11) NOT NULL auto_increment,
  dimensionId int(11) default NULL,
  attrGroupId int(11) default NULL,
  average float default NULL,
  standardDeviation float default NULL,
  threshold float default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
INSERT INTO dimensiondatas VALUES (1,1,1,1.895,0.285833,NULL),(2,1,3,1.895,0.285833,NULL),(3,1,5,1.895,0.285833,NULL),(4,1,6,2.02208,0.266667,NULL),(5,1,7,1.895,0.285833,NULL),(6,1,9,1.895,0.285833,NULL),(7,1,10,1.895,0.285833,NULL),(8,3,1,2.80125,0.39375,NULL),(9,3,3,2.80125,0.38625,NULL),(10,3,5,2.80125,0.38625,NULL),(11,3,6,3.04875,0.38625,NULL),(12,3,7,2.80125,0.39375,NULL),(13,3,9,2.80125,0.39375,NULL),(14,3,10,2.80125,0.39375,NULL),(15,4,1,1.78875,0.31375,NULL),(16,4,3,1.78875,0.31375,NULL),(17,4,5,1.78875,0.31375,NULL),(18,4,6,1.88625,0.33125,NULL),(19,4,7,1.78875,0.31375,NULL),(20,4,9,1.78875,0.31375,NULL),(21,4,10,1.78875,0.31375,NULL),(22,5,1,1.78875,0.3675,NULL),(23,5,3,1.78875,0.3675,NULL),(24,5,5,1.78875,0.3675,NULL),(25,5,6,1.75125,0.40375,NULL),(26,5,7,1.78875,0.3675,NULL),(27,5,9,1.78875,0.3675,NULL),(28,5,10,1.78875,0.3675,NULL),(29,6,1,2.82625,0.3525,NULL),(30,6,3,2.82625,0.3525,NULL),(31,6,5,2.82625,0.3525,NULL),(32,6,6,2.77875,0.32875,NULL),(33,6,7,2.82625,0.3525,NULL),(34,6,9,2.82625,0.3525,NULL),(35,6,10,2.82625,0.3525,NULL),(36,7,1,2.13286,0.143393,NULL),(37,7,3,2.13286,0.143393,NULL),(38,7,5,2.13286,0.143393,NULL),(39,7,6,2.21589,0.159464,NULL),(40,7,7,2.13286,0.143393,NULL),(41,7,9,2.13286,0.143393,NULL),(42,7,10,2.13286,0.143393,NULL),(43,8,1,2.49643,0.63696,1.25),(44,8,3,2.49643,0.63696,1.25),(45,8,5,2.49643,0.63696,1.25),(46,8,6,3.00295,0.764732,1.25),(47,8,7,2.49643,0.63696,1.25),(48,8,9,2.49643,0.63696,1.25),(49,8,10,2.49643,0.63696,1.25),(50,9,1,2.8637,1.1389,1.25),(51,9,3,2.8637,1.1389,1.25),(52,9,5,2.8637,1.1389,1.25),(53,9,6,3.79,1.37,1.25),(54,9,7,2.8637,1.1389,1.25),(55,9,9,2.8637,1.1389,1.25),(56,9,10,2.8637,1.1389,1.25);
CREATE TABLE dimensiontypes (
  id int(11) NOT NULL auto_increment,
  name varchar(255) default NULL,
  surveyTypeId int(11) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
INSERT INTO dimensiontypes VALUES (1,'mnipulation',1),(3,'adishut',1),(4,'impulsive',1),(5,'unResponsible',1),(6,'halaklak',1),(7,'sigSum',1),(8,'sumTotal',1),(9,'mzaSum',1);
CREATE TABLE feedbackdims (
  id int(11) NOT NULL auto_increment,
  feedbackId int(11) default NULL,
  dimId int(11) default NULL,
  result float default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE feedbacks (
  id int(11) NOT NULL auto_increment,
  responderId int(11) default NULL,
  surveyId int(11) default NULL,
  rowData text,
  fileName varchar(255) default NULL,
  json text,
  created int(11) default NULL,
  remarks text,
  socialDes varchar(255) default NULL,
  finalGroup varchar(255) default NULL,
  url text,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE questions (
  id int(11) NOT NULL auto_increment,
  dimId int(11) default NULL,
  surveyId int(11) default NULL,
  questionTypeId int(11) default NULL,
  questionName varchar(255) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
INSERT INTO questions VALUES (2,9,1366688,1,'MZA_1'),(3,9,1366688,1,'MZA_2'),(4,9,1366688,1,'MZA_3'),(5,9,1366688,1,'MZA_4'),(6,9,1366688,1,'MZA_5'),(7,9,1366688,1,'MZA_6'),(8,9,1366688,1,'MZA_7'),(9,9,1366688,1,'MZA_8'),(10,9,1366688,1,'MZA_9'),(11,9,1366688,1,'MZA_10'),(12,9,1366688,1,'MZA_11'),(13,9,1366688,1,'MZA_12'),(14,9,1366688,1,'MZA_13'),(15,9,1366688,1,'MZA_14'),(16,9,1366688,1,'MZA_15'),(17,1,1366688,1,'SIG_1'),(18,1,1366688,1,'SIG_3'),(19,1,1366688,1,'SIG_5'),(20,1,1366688,1,'SIG_8'),(21,1,1366688,1,'SIG_10'),(22,1,1366688,1,'SIG_12'),(23,1,1366688,1,'SIG_15'),(24,1,1366688,1,'SIG_17'),(25,1,1366688,1,'SIG_19'),(26,1,1366688,1,'SIG_22'),(27,1,1366688,1,'SIG_24'),(28,1,1366688,1,'SIG_26'),(29,1,1366688,1,'SIG_29'),(30,1,1366688,1,'SIG_31'),(31,1,1366688,1,'SIG_33'),(32,1,1366688,1,'SIG_36'),(33,1,1366688,1,'SIG_38'),(34,1,1366688,1,'SIG_40'),(35,1,1366688,1,'SIG_43'),(36,1,1366688,1,'SIG_45'),(37,1,1366688,1,'SIG_47'),(38,1,1366688,1,'SIG_50'),(39,1,1366688,1,'SIG_52'),(40,1,1366688,1,'SIG_54'),(41,3,1366688,1,'SIG_2'),(42,3,1366688,1,'SIG_9'),(43,3,1366688,1,'SIG_16'),(44,3,1366688,1,'SIG_23'),(45,3,1366688,1,'SIG_30'),(46,3,1366688,1,'SIG_37'),(47,3,1366688,1,'SIG_44'),(48,3,1366688,1,'SIG_51'),(49,4,1366688,1,'SIG_4'),(50,4,1366688,1,'SIG_11'),(51,4,1366688,1,'SIG_18'),(52,4,1366688,1,'SIG_25'),(53,4,1366688,1,'SIG_32'),(54,4,1366688,1,'SIG_39'),(55,4,1366688,1,'SIG_46'),(56,4,1366688,1,'SIG_53'),(57,5,1366688,1,'SIG_6'),(58,5,1366688,1,'SIG_13'),(59,5,1366688,1,'SIG_20'),(60,5,1366688,1,'SIG_27'),(61,5,1366688,1,'SIG_34'),(62,5,1366688,1,'SIG_41'),(63,5,1366688,1,'SIG_48'),(64,5,1366688,1,'SIG_55'),(65,6,1366688,1,'SIG_7'),(66,6,1366688,1,'SIG_14'),(67,6,1366688,1,'SIG_21'),(68,6,1366688,1,'SIG_28'),(69,6,1366688,1,'SIG_35'),(70,6,1366688,1,'SIG_42'),(71,6,1366688,1,'SIG_49'),(72,6,1366688,1,'SIG_56');
CREATE TABLE responderextradatas (
  id int(11) NOT NULL auto_increment,
  responderId int(11) default NULL,
  paramName varchar(255) default NULL,
  val varchar(255) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE responders (
  id int(11) NOT NULL auto_increment,
  divisionId int(11) default NULL,
  gismoId varchar(255) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8