CREATE TABLE ezevent (
  ID                         SERIAL   NOT NULL,
  parent_event_id            INT   NOT NULL,
  contentobject_attribute_id INT   NOT NULL,
  version                    INT   NOT NULL,
  start_date                 INT   NOT NULL,
  end_date                   INT   NOT NULL,
  event_type                 SMALLINT   DEFAULT '14'   NOT NULL,
  is_parent                  SMALLINT   DEFAULT '0'   NOT NULL,
  is_temp                    SMALLINT   DEFAULT '0'   NOT NULL,
  CONSTRAINT pk_ezevent PRIMARY KEY ( id,contentobject_attribute_id,version ));

CREATE INDEX contentobject_attribute_id ON ezevent (
      contentobject_attribute_id,
      version);

CREATE INDEX parent_event_id ON ezevent (
      parent_event_id);


