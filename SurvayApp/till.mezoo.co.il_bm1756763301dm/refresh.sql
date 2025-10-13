DROP TABLE IF EXISTS responders;
DROP TABLE IF EXISTS responderextradatas;
DROP TABLE IF EXISTS feedbacks;
DROP TABLE IF EXISTS feedbackdims;
DROP TABLE IF EXISTS logs;
CREATE TABLE logs (
  id int(11) NOT NULL auto_increment,
  created varchar(255) default NULL,
  username varchar(255) default NULL,
  result varchar(255) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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