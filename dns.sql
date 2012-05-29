DROP TABLE IF EXISTS `log`;
DROP TABLE IF EXISTS `domains`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE IF NOT EXISTS `users` (
  id         INT PRIMARY KEY auto_increment,
  name       VARCHAR(50),
  password   BLOB,
  email      VARCHAR(100),
  token      VARCHAR(32),
  toktime    INT,
  INDEX     (name, password(32))
) engine=InnoDB;

CREATE TABLE IF NOT EXISTS `domains` (
  id        INT PRIMARY KEY auto_increment,
  owner     INT,
  domain    VARCHAR(100),
  INDEX(owner, domain),
  FOREIGN KEY(owner) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB;

CREATE TABLE IF NOT EXISTS `log` (
  owner     INT,
  action    VARCHAR(50),
  date      INT,
  ip        VARCHAR(15),
  INDEX(owner, action),
  FOREIGN KEY(owner) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB;
