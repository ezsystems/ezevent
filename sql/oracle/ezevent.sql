-- DROP TABLE IF EXISTS ezevent;
CREATE TABLE ezevent (
  id                            integer NOT NULL,
  parent_event_id               integer NOT NULL,
  contentobject_attribute_id    integer NOT NULL,
  version                       integer NOT NULL,
  start_date                    integer NOT NULL,
  end_date                      integer NOT NULL,
  event_type                    integer DEFAULT 14,
  is_parent                     integer DEFAULT 0,
  is_temp                       integer DEFAULT 0,
  PRIMARY KEY( id, contentobject_attribute_id, version )
);

CREATE SEQUENCE s_event;
CREATE OR REPLACE TRIGGER ezevent_tr
BEFORE INSERT ON ezevent FOR EACH ROW WHEN (new.id IS NULL)
BEGIN
  SELECT s_event.nextval INTO :new.id FROM dual;
END;
/

CREATE INDEX ezevent_coaid_version ON ezevent ( contentobject_attribute_id, version );
CREATE INDEX ezevent_parent_event_id ON ezevent ( parent_event_id );


