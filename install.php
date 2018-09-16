<?php
/*
  THIS SCRIPT WILL SELF-DESTRUCT AFTER EXECUTION!
*/
$db = include_once(__DIR__."/include/use_db.php");
$db->getPDO()->exec("
CREATE TABLE IF NOT EXISTS pizzapp_bill (
  bill_id bigint(20) NOT NULL AUTO_INCREMENT,
  user_id bigint(20) NOT NULL,
  cost bigint(20) NOT NULL,
  time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (bill_id),
  KEY FK_user_id (user_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


CREATE TABLE IF NOT EXISTS pizzapp_orders (
  user_id bigint(21) NOT NULL,
  product_id bigint(21) NOT NULL,
  bill_id bigint(20) NOT NULL,
  quantity bigint(21) NOT NULL,
  PRIMARY KEY (user_id,product_id,bill_id),
  KEY pizzapp_orders_ibfk_2 (product_id),
  KEY pizzapp_orders_ibfk_3 (bill_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS pizzapp_products (
  product_id bigint(21) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL,
  price decimal(10,2) NOT NULL,
  image varchar(50) NOT NULL,
  PRIMARY KEY (product_id),
  UNIQUE KEY name (name)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS pizzapp_sessions (
  session_id bigint(21) NOT NULL AUTO_INCREMENT,
  user_id bigint(21) NOT NULL,
  token varchar(35) NOT NULL,
  email varchar(40) NOT NULL,
  time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (session_id),
  UNIQUE KEY user_id (user_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS pizzapp_users (
  user_id bigint(21) NOT NULL AUTO_INCREMENT,
  email varchar(40) NOT NULL,
  password varchar(200) NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

ALTER TABLE pizzapp_bill
  ADD CONSTRAINT pizzapp_bill_ibfk_1 FOREIGN KEY (user_id) REFERENCES pizzapp_users (user_id) ON UPDATE CASCADE;

ALTER TABLE pizzapp_orders
  ADD CONSTRAINT pizzapp_orders_ibfk_1 FOREIGN KEY (user_id) REFERENCES pizzapp_users (user_id) ON UPDATE CASCADE,
  ADD CONSTRAINT pizzapp_orders_ibfk_2 FOREIGN KEY (product_id) REFERENCES pizzapp_products (product_id) ON UPDATE CASCADE,
  ADD CONSTRAINT pizzapp_orders_ibfk_3 FOREIGN KEY (bill_id) REFERENCES pizzapp_bill (bill_id) ON UPDATE CASCADE;

ALTER TABLE pizzapp_sessions
  ADD CONSTRAINT pizzapp_sessions_ibfk_1 FOREIGN KEY (user_id) REFERENCES pizzapp_users (user_id) ON UPDATE CASCADE;
");

echo "Installation finished. Check your database.";
class SelfDestroy{function __destruct(){unlink(__FILE__);}}
$installation_finished = new SelfDestroy();
?>