DROP TABLE IF EXISTS ezevent;
CREATE TABLE ezevent
(
  id                            int(11) NOT NULL AUTO_INCREMENT,
  parent_event_id               int(11) NOT NULL,
  contentobject_attribute_id    int(11) NOT NULL,
  version                       int(11) NOT NULL,
  start_date                    int(11) NOT NULL,
  end_date                      int(11) NOT NULL,
  event_type                    int(2)  default 14,
  is_parent                     int(1)  default 0,
  is_temp                       int(1)  default 0,
  PRIMARY KEY( id, contentobject_attribute_id, version ),
  KEY( contentobject_attribute_id, version ),
  KEY( parent_event_id ) -- ,
  -- CONSTRAINT FOREIGN KEY ( parent_event_id ) REFERENCES ezevent( id ) --
  -- ON DELETE CASCADE --
);
