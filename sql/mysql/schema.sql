CREATE TABLE ezevent (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_event_id int(11) NOT NULL,
  contentobject_attribute_id int(11) NOT NULL,
  version int(11) NOT NULL,
  start_date int(11) NOT NULL,
  end_date int(11) NOT NULL,
  event_type int(4) NOT NULL DEFAULT '14',
  is_parent int(4) NOT NULL DEFAULT '0',
  is_temp int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (id,contentobject_attribute_id,version),
  KEY contentobject_attribute_id (contentobject_attribute_id,version),
  KEY parent_event_id (parent_event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

