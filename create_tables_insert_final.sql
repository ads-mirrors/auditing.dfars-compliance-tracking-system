DROP DATABASE rebus;

CREATE DATABASE rebus;

USE rebus;

# Create Tables 
# Table to hold all organizations 
CREATE TABLE IF NOT EXISTS organization
(
org_id INT NOT NULL AUTO_INCREMENT,
org_name VARCHAR(75) NOT NULL,
org_address VARCHAR(100) NOT NULL,
org_city VARCHAR(60) NOT NULL,
org_state_us CHAR(2),
org_state_other CHAR(5),
org_zip VARCHAR(15) NOT NULL,
org_country CHAR(5),
org_parent_branch INT,
org_mnged_serv_provider ENUM('Y','N'),
org_add_date DATETIME DEFAULT CURRENT_TIMESTAMP, 
org_add_uname VARCHAR(100),
org_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
org_update_uname VARCHAR(100),
PRIMARY KEY (org_id),
FOREIGN KEY (org_parent_branch) REFERENCES organization (org_id) 
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Index to help speed up searches and make org_name UNIQUE */
CREATE UNIQUE INDEX organization_uk ON organization (org_name);


CREATE TABLE IF NOT EXISTS user
(
user_id INT NOT NULL AUTO_INCREMENT,
user_fname VARCHAR(60) NOT NULL,
user_midname VARCHAR(60),
user_lname VARCHAR(60) NOT NULL,
user_country_code CHAR(3),
user_phone_num VARCHAR(16) NOT NULL,
user_email VARCHAR(100) NOT NULL, # this field servers as the person's username
 /* Passwords are hashed during user creation or password resets from website */
user_password VARCHAR(255) NOT NULL,
user_temp_flag ENUM('Y', 'N') NOT NULL DEFAULT 'Y',
user_locked_flag ENUM('Y', 'N') NOT NULL DEFAULT 'N',
user_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
user_add_uname VARCHAR(100),
user_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
user_update_uname VARCHAR(100),
org_id INT,
user_manager INT,
PRIMARY KEY (user_id),
FOREIGN KEY (org_id) REFERENCES organization (org_id)
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (user_manager) REFERENCES user (user_id) 
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

# Speeds to retrieval for logins and determining session information and makes user_email UNIQUE
CREATE UNIQUE INDEX user_uk ON user (user_email);

# Table to hold all the different flavors of a system, should be a small table
CREATE TABLE IF NOT EXISTS sys_type
(
type_id INT NOT NULL AUTO_INCREMENT,
type_name VARCHAR(75) NOT NULL,
type_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
type_add_uname VARCHAR(100),
type_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
type_update_uname VARCHAR(100),
PRIMARY KEY (type_id)
) ENGINE=InnoDB;

# Help speed retrieval as type_name is used in certain queries and makes system type unique
CREATE UNIQUE INDEX sys_type_uk ON sys_type (type_name);

# Holds all information for each system an organization keeps scan results for
CREATE TABLE IF NOT EXISTS system
(
sys_id INT NOT NULL AUTO_INCREMENT,
sys_name VARCHAR(100) NOT NULL,
sys_ip_address VARCHAR(20),
sys_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
sys_add_uname VARCHAR(100),
sys_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
sys_update_uname VARCHAR(100),
type_id INT NOT NULL,
org_id INT NOT NULL,
PRIMARY KEY (sys_id),
FOREIGN KEY (type_id) REFERENCES sys_type (type_id) 
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (org_id) REFERENCES organization (org_id) 
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

# Help speed up queries where a list of systems for each organization is needed and makes system names unique within a given organization
CREATE UNIQUE INDEX system_uk ON system (org_id, sys_name);

# This table will be used to hold organization's reasonings for complying or not complying with a requirement 
CREATE TABLE IF NOT EXISTS artifact
(
art_id INT NOT NULL AUTO_INCREMENT,
art_text VARCHAR(255) NOT NULL,
art_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
art_add_uname VARCHAR(100),
art_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
art_update_uname VARCHAR(100),
org_id INT NOT NULL,
PRIMARY KEY (art_id),
FOREIGN KEY (org_id) REFERENCES organization (org_id) 
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

# Needed to speed up retrieval when entering results manually and set artifact text unique to the organization
CREATE UNIQUE INDEX artifact_uk ON artifact (org_id, art_text);

# Table to hold all standards needed
CREATE TABLE IF NOT EXISTS standard
(
stand_id INT NOT NULL AUTO_INCREMENT,
stand_name VARCHAR(100) NOT NULL,
stand_version_rev_num VARCHAR(100) NOT NULL,
stand_effective_date DATE NOT NULL,
stand_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
stand_add_uname VARCHAR(100),
stand_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
stand_update_uname VARCHAR(100),
stand_root INT,
PRIMARY KEY (stand_id),
FOREIGN KEY (stand_root) REFERENCES standard (stand_id)
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

# Needed to speed up queries where a standard name and revision number are needed and the pair is unique
CREATE UNIQUE INDEX standard_uk ON standard (stand_name, stand_version_rev_num);


# Holds the categories for each standard, some categories can be used by more than one standard 
CREATE TABLE IF NOT EXISTS category
(
cat_id INT NOT NULL AUTO_INCREMENT,
cat_name VARCHAR(75) NOT NULL,
cat_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
cat_add_uname VARCHAR(100),
cat_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
cat_update_uname VARCHAR(100),
PRIMARY KEY (cat_id)
) ENGINE=InnoDB;

# Needed to speed up retrieval of category names and numbers when needed and make the pair unique
CREATE UNIQUE INDEX category_uk ON category (cat_name);

# Bridge entity to account for the use of categories by multiple standards and their revisions 
CREATE TABLE IF NOT EXISTS standard_category
(
standcat_id INT NOT NULL AUTO_INCREMENT,
stand_id INT NOT NULL,
cat_id INT NOT NULL,
standcat_num VARCHAR(10) NOT NULL,
standcat_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
standcat_add_uname VARCHAR(100),
standcat_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
standcat_update_uname VARCHAR(100),
PRIMARY KEY (standcat_id),
FOREIGN KEY (stand_id) REFERENCES standard (stand_id) 
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (cat_id) REFERENCES category (cat_id) 
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

# Needed to make sure each standard and category combination only occurs once
CREATE UNIQUE INDEX stand_category_uk ON standard_category (stand_id, cat_id);

# Holds requirement information for each Category
CREATE TABLE IF NOT EXISTS requirement
(
req_id INT NOT NULL AUTO_INCREMENT,
req_num VARCHAR(15) NOT NULL,
req_desc TEXT NOT NULL, # Full description of requirement
req_simple_desc TEXT, # Shortened and easier to understand description for the requirement
req_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
req_add_uname VARCHAR(100),
req_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
req_update_uname VARCHAR(100),
standcat_id INT NOT NULL,
PRIMARY KEY (req_id),
FOREIGN KEY (standcat_id) REFERENCES standard_category (standcat_id)
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

# Needed to speed up retrievals of the requirement's number and description and make the pair unique
CREATE UNIQUE INDEX requirement_uk ON requirement (req_num, req_desc(500));


# This table will hold newer verbiage for requirements of each standard 
CREATE TABLE IF NOT EXISTS req_amendment
(
amend_id INT NOT NULL AUTO_INCREMENT,
amend_effective_date DATE NOT NULL,
amend_desc TEXT NOT NULL,
amend_simple_desc TEXT,
amend_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
amend_add_uname VARCHAR(100),
amend_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
amend_update_uname VARCHAR(100),
req_id INT NOT NULL,
PRIMARY KEY (amend_id),
FOREIGN KEY (req_id) REFERENCES requirement (req_id) 
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

# Needed to speed up retrieval of amendments to requirements
CREATE UNIQUE INDEX req_amendment_uk ON req_amendment (req_id, amend_desc(500));

# Table will hold a limited number of possible ratings for results to each requirement a system is scanned against
CREATE TABLE IF NOT EXISTS rating
(
rate_id INT NOT NULL AUTO_INCREMENT,
rate_name VARCHAR(25) NOT NULL,
rate_abbv VARCHAR(5) NOT NULL,
rate_desc VARCHAR(75) NOT NULL,
rate_root_stand INT NOT NULL,
rate_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
rate_add_uname VARCHAR(100),
rate_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
rate_update_uname VARCHAR(100),
PRIMARY KEY (rate_id),
FOREIGN KEY (rate_root_stand) REFERENCES standard (stand_id)
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE UNIQUE INDEX rating_uk ON rating (rate_abbv, rate_root_stand);

# This table will hold organization provided timelines for their estimated time to satisfy failed requirements
CREATE TABLE IF NOT EXISTS range_time
(
range_id INT NOT NULL AUTO_INCREMENT,
range_desc VARCHAR(50) NOT NULL,
range_min INT NOT NULL,
range_max INT NOT NULL,
range_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
range_add_uname VARCHAR(100),
range_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
range_update_uname VARCHAR(100),
PRIMARY KEY (range_id)
) ENGINE=InnoDB;

# Neeed to speed up retrieval of data when requested by a query
CREATE UNIQUE INDEX range_time_uk ON range_time (range_min, range_max);


# Table to hold all result information for each system that the requirement is tested against for each organization.
# Requirment/system pairs not in this table mean that that requirement has not been ran against that system yet.
CREATE TABLE IF NOT EXISTS system_requirement
(
sysreq_id INT NOT NULL AUTO_INCREMENT,
sys_id INT NOT NULL,
req_id INT NOT NULL, 
sysreq_notes TEXT,
rate_id INT NOT NULL,
range_id INT,
art_id INT NOT NULL,
sysreq_add_date DATETIME DEFAULT CURRENT_TIMESTAMP,
sysreq_add_uname VARCHAR(100),
sysreq_update_date DATETIME ON UPDATE CURRENT_TIMESTAMP,
sysreq_update_uname VARCHAR(100),
PRIMARY KEY (sysreq_id),
FOREIGN KEY (sys_id) REFERENCES system (sys_id) 
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (req_id) REFERENCES requirement (req_id) 
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (rate_id) REFERENCES rating (rate_id) 
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (range_id) REFERENCES range_time (range_id) 
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (art_id) REFERENCES artifact (art_id) 
ON DELETE CASCADE
ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE UNIQUE INDEX system_requirement_uk ON system_requirement (sys_id, req_id, sysreq_add_date);


# Table to store CSV loaded standard for processing into standard->category->standard_category->requirement tables
CREATE TABLE standard_import
(
standname varchar(100) NOT NULL, 
standver varchar(10) NOT NULL,
standdate DATE NOT NULL,
catnum varchar(10) NOT NULL,
catname varchar(75) NOT NULL, 
reqnum varchar(10) NOT NULL,
reqdesc text NOT NULL,
reqsimple text NOT NULL,
ratename varchar(25),
ratedesc varchar(75),
username varchar(100)
) ENGINE=InnoDB;


/* Table to store CSV loaded results for processing into the SYSTEM_REQUIREMENT
 table */
CREATE TABLE result_import
(
orgname VARCHAR(100),
sysname VARCHAR(100),
standname VARCHAR(100),
standver VARCHAR(10),
catnum VARCHAR(10),
reqnum INT,
artifact TEXT,
note TEXT,
ratename VARCHAR(25),
rangename VARCHAR(50),
username VARCHAR(100)
) ENGINE=InnoDB;

/* Table to store failed login attempts, when there are 5 within a two hour period the system will not allow the user to login */
CREATE TABLE login_attempts
(
user_id int(11) NOT NULL,
time varchar(30) NOT NULL
) ENGINE=InnoDB;


/* Create Triggers */
DELIMITER $$
DROP TRIGGER IF EXISTS before_standard_insert$$
CREATE TRIGGER before_standard_insert 
    BEFORE INSERT ON standard
    FOR EACH ROW 
BEGIN
	DECLARE v_stand INT;
	SELECT MIN(stand_id) FROM standard 
		WHERE stand_name = NEW.stand_name GROUP BY stand_name
		INTO v_stand;
	IF (v_stand IS NOT NULL) THEN
		SET NEW.stand_root = v_stand;
	ELSE 
		SET NEW.stand_root = NULL;
	END IF;
END$$


/* Create Functions */
CREATE FUNCTION getuser()
 RETURNS INT(4)
 RETURN @user$$
 
CREATE FUNCTION getsys()
 RETURNS INT(4)
 RETURN @sys$$
 
 
#Procedures
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_org_sp`(IN oname VARCHAR(75), IN oaddr VARCHAR(100), IN ocity VARCHAR(60), IN ostate CHAR(2), IN otherstate CHAR(5), IN ozip VARCHAR(15), IN ocountry CHAR(5), IN parent INT(11), IN oprovider CHAR(1), IN uname VARCHAR(100))
BEGIN

INSERT INTO organization (org_name, org_address, org_city, org_state_us, org_state_other, org_zip, org_country, org_parent_branch, org_mnged_serv_provider, org_add_uname) 
VALUES(oname, oaddr, ocity, ostate, otherstate, ozip, ocountry, parent, oprovider, uname); 

END$$

# Insert system
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_system_sp`(IN sysname VARCHAR(100), IN ipaddr VARCHAR(20), IN uname VARCHAR(100), IN systype INT, IN org INT)
BEGIN

INSERT INTO system (sys_name, sys_ip_address, sys_add_uname, type_id, org_id) 
VALUES(sysname, ipaddr, uname, systype, org); 

END$$

# Insert system type
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_systype_sp`(IN typename VARCHAR(75), IN uname VARCHAR(100))
BEGIN

INSERT INTO sys_type (type_name, type_add_uname) 
VALUES(typename, uname); 

END$$

# Insert new user
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_user_sp`(IN fname VARCHAR(60), IN mname VARCHAR(60), IN lname VARCHAR(60), IN ccode CHAR(3), IN u_phone VARCHAR(16), IN u_email VARCHAR(100), IN u_pass VARCHAR(255), IN uname VARCHAR(100), IN org INT, IN manager INT)
BEGIN

INSERT INTO user (user_fname, user_midname, user_lname, user_country_code, user_phone_num, user_email, user_password, user_add_uname, org_id, user_manager) 
VALUES(fname, mname, lname, ccode, u_phone, u_email, u_pass, uname, org, manager); 

END$$

# Import standard from csv
CREATE DEFINER=`root`@`localhost` PROCEDURE `standard_import`()
BEGIN
DECLARE done INT DEFAULT FALSE;
DECLARE v_oldreq INT;
DECLARE v_olddesc text;
DECLARE v_oldsimple text;
DECLARE v_standcat INT;
DECLARE v_standname VARCHAR(100);
DECLARE v_standver VARCHAR(10);
DECLARE v_catnum VARCHAR(10);
DECLARE v_catname VARCHAR(75);
DECLARE v_reqnum INT;
DECLARE v_standdate DATE;
DECLARE v_reqdesc text;
DECLARE v_reqsimple text;
DECLARE v_username VARCHAR(100);
DECLARE cur1 CURSOR FOR SELECT standname, standver, catnum, catname, reqnum, standdate, reqdesc, reqsimple, username FROM standard_import;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

# Start with standard (least granular); go to requirement (most granular)
INSERT IGNORE INTO standard (stand_name, stand_version_rev_num, stand_effective_date, stand_add_uname)
	SELECT DISTINCT standname, standver, standdate, username FROM standard_import;
INSERT IGNORE INTO category (cat_name, cat_add_uname)
	SELECT DISTINCT catname, username FROM standard_import;
            
OPEN cur1;
read_loop: LOOP
	FETCH cur1 INTO v_standname, v_standver, v_catnum, v_catname, v_reqnum, v_standdate, v_reqdesc, v_reqsimple, v_username;
    IF done THEN
		LEAVE read_loop;
	END IF;
    
	# Will need standcat id for each requirement
    INSERT IGNORE INTO standard_category (stand_id, cat_id, standcat_num, standcat_add_uname)
	VALUES ((SELECT stand_id FROM standard WHERE stand_name = v_standname AND stand_version_rev_num = v_standver),
		(SELECT cat_id FROM category WHERE cat_name=v_catname), v_catnum, v_username);
        
	SELECT MAX(standcat_id) INTO v_standcat FROM standard_category; 
        
	# Check if any standard, category, requirement combos in standard_import exists already in requirement table
	# Nested program to escape stupid bug for select...INTO tripping continue handler
	BEGIN
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = FALSE;
		SELECT req_id INTO v_oldreq FROM v_standard
		WHERE stand_name = v_standname
		AND standcat_num = v_catnum
		AND req_num = v_reqnum;
	END;
    
    IF v_oldreq IS NOT NULL THEN
		# Check if either description has been updated
        SELECT req_desc, req_simple_desc INTO v_olddesc, v_oldsimple FROM requirement 
			WHERE req_id = v_oldreq
            AND req_desc = v_olddesc
            AND req_simple_desc = v_oldsimple;
		# Include amendment if either has changed
		IF v_olddesc IS NOT NULL OR v_oldsimple IS NOT NULL THEN
			INSERT INTO req_amendment (req_id, amend_effective_date, amend_desc, amend_simple_desc, amend_add_uname)
				VALUES (v_oldreq, v_standdate, v_reqdesc, v_reqsimple, v_username);
		END IF;
        
        # Either way, update stand_cat id to most recent
        UPDATE requirement SET standcat_id = v_standcat WHERE req_id = v_oldreq;
        
	ELSE
		INSERT INTO requirement (req_num, req_desc, req_simple_desc, standcat_id, req_add_uname)
			VALUES (v_reqnum, v_reqdesc, v_reqsimple, v_standcat, v_username);
	END IF;
END LOOP;
CLOSE cur1;

INSERT IGNORE INTO rating (rate_name, rate_abbv, rate_desc, rate_root_stand, rate_add_uname)
	SELECT DISTINCT LEFT(ratename,LOCATE('-',ratename) - 1), SUBSTR(ratename,LOCATE('-',ratename) + 1), 
    ratedesc, IFNULL(stand_root, stand_id), v_username
		FROM standard_import si JOIN standard s ON s.stand_name=si.standname
        WHERE ratename != '';
TRUNCATE standard_import;
END$$

# Import standard results from csv
CREATE DEFINER=`root`@`localhost` PROCEDURE `results_import`()
BEGIN

# Remove trailing \r. It will get in the way......
SET SQL_SAFE_UPDATES = 0;
update result_import SET rangename = TRIM(TRAILING '\r' FROM rangename);
SET SQL_SAFE_UPDATES=1;

INSERT IGNORE INTO artifact (org_id, art_text, art_add_uname)
	SELECT DISTINCT org_id, artifact, username FROM result_import ri JOIN organization ur ON ri.orgname = ur.org_name;
INSERT INTO system_requirement (sys_id, req_id, rate_id, range_id, art_id, sysreq_add_uname)
	SELECT DISTINCT sys_id, req_id, rate_id, range_id, art_id, username from result_import ri
		JOIN system s ON ri.sysname=s.sys_name
		JOIN v_standard vs ON ri.standname=vs.stand_name AND ri.standver=vs.stand_version_rev_num AND catnum=standcat_num AND reqnum=req_num
		JOIN rating r ON ri.ratename=r.rate_name AND (vs.stand_root=r.rate_root_stand OR vs.stand_id=r.rate_root_stand)
		LEFT JOIN range_time rt ON ri.rangename=rt.range_desc
		JOIN artifact a ON ri.artifact=a.art_text;
TRUNCATE result_import;

END$$
DELIMITER ;



/* Create Views */ 
CREATE OR REPLACE VIEW v_user_reference (
  user_email,
  user_id,
  org_id,
  org_name,
  org_mnged_serv_provider,
  org_parent_branch,
  sys_id,
  sys_name
)
as
select
  user.user_email,
  user.user_id,
  organization.org_id,
  organization.org_name,
  organization.org_mnged_serv_provider,
  organization.org_parent_branch,
  system.sys_id,
  system.sys_name
FROM organization LEFT JOIN system USING (org_id) 
JOIN user ON organization.org_parent_branch=user.org_id OR organization.org_id=user.org_id
WHERE user_id = getuser();

CREATE OR REPLACE VIEW v_standard (
 stand_id,
 stand_name,
 stand_version_rev_num,
 stand_effective_date,
 stand_root,
 standcat_id,
 standcat_num,
 cat_name,
 req_id,
 req_num,
 req_desc,
 req_simple_desc
)
AS
SELECT
  s.stand_id,
  s.stand_name,
  s.stand_version_rev_num,
  s.stand_effective_date,
  s.stand_root,
  sc.standcat_id,
  sc.standcat_num,
  c.cat_name,
  r.req_id,
  r.req_num,
  r.req_desc,
  r.req_simple_desc
FROM standard s
JOIN standard_category sc USING (stand_id)
JOIN category c USING (cat_id)
JOIN requirement r USING (standcat_id)
ORDER BY stand_name, stand_version_rev_num, standcat_id, req_id;

CREATE OR REPLACE VIEW v_system_requirement (
 sysreq_id,
 sys_id,
 req_id,
 sysreq_add_date,
 sysreq_notes,
 rate_id,
 rate_name,
 range_id,
 range_desc,
 art_id,
 art_text
)
AS
SELECT
  sr.sysreq_id,
  sr.sys_id,
  sr.req_id,
  sr.sysreq_add_date,
  sr.sysreq_notes,
  sr.rate_id,
  ra.rate_name,
  sr.range_id,
  rt.range_desc,
  sr.art_id,
  a.art_text
FROM system_requirement sr
JOIN rating ra USING (rate_id)
LEFT JOIN range_time rt USING (range_id)
JOIN artifact a USING (art_id)
WHERE sys_id = getsys()
ORDER BY sys_id, req_id;


/* Insert test data */
INSERT INTO organization (org_name, org_address, org_city, org_state_us, org_state_other, org_zip, org_country, org_parent_branch, org_mnged_serv_provider)
	VALUES 
	('TRG', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', NULL, 'Y'),
	('Microsoft', '456 Dumb Road', 'Quebec City', NULL, 'QC', 98765,'CAN', NULL, 'Y'),
	('Sony', '789 I Dont Know Where', 'Tokyo', NULL, 'TYO', 45612, 'JPN', NULL, 'Y'),
	('G2 Ops', '205 Business Park Dr. #200', 'Virginia Beach', 'VA', NULL, 23462, 'USA', NULL, 'Y'),
    ('Nintendo', '111 Lena Arch', 'Norfolk', 'VA', NULL, 23518, 'USA', 1, 'N'),
	('D Co.', '999 Fairy Lane', 'Detroit', 'MI', NULL, 78945, 'USA', 2, 'N'),
	('Org 4', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 3, 'N'),
	('Org 5', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 1, 'N'),
	('Org 6', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 2, 'N'),
	('Org 7', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 3, 'N'),
	('Org 8', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 1, 'N'),
	('Org 9', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 2, 'N'),
	('Org 10', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 3, 'N'),
	('Org 11', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 1, 'N'),
	('Org 12', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 2, 'N'),
	('Org 13', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 3, 'N'),
	('Org 14', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 1, 'N'),
	('Org 15', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 2, 'N'),
	('Org 16', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 3, 'N'),
	('Org 17', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 1, 'N'),
	('Org 18', '123 Fake Street', 'San Francisco', 'CA', NULL, 12345, 'USA', 2, 'N');

INSERT INTO user (user_fname, user_midname, user_lname, user_country_code, user_phone_num, user_email, user_password, org_id, user_manager)
	VALUES 
	('Jeff', 'Davis', 'Bauersfeld', 'USA', '757-374-7393', 'jeff@rebus.com', '$2y$10$65AhtlHnI7hSfa/3/L/UeeGVxujsXbUtTdbccpXd77RuElYPY/syS', 1, NULL),
	('Christopher', 'James', 'Christopherson', 'BRZ', '1-555-213-4493', 'chris@rebus.com', '$2y$10$HUq/Lf1ALpMT6T5yOurF3OwW77fJ4x1Cs.2/O/rCwTsKnVzeixC4e', 2, NULL),
	('Christine', 'Sam', 'Pants', 'USA', '999-654-1236', 'christine@rebus.com', '$2y$10$w3oKjM1GtuzqxWrkyh9rAOgbStA96n17xuZttJWUOCu7zeY5puq.K', 3, NULL),
	('Zack', '', 'Cranston', 'USA', '123-456-7890', 'zack@rebus.com', '$2y$10$5GIQUSK5D7KAgsJqqJGgT.gELhAUroluNsBD/cgvwyk2Daq8XEH9e', 2, 2),
	('Daniel', 'James', 'Bond', 'ENG', '1-555-555-5555', 'daniel@rebus.com', '$2y$10$Qgc//w.B63E0hH9WmzAoSuFJRCCVtFnUffyacc4cumr2MVlxNZy/6', 1, 1),
	('Lemuel', 'A', 'Aaronson', 'ENG', '1-555-555-5555', 'lemuel@rebus.com', '$2y$10$68x.Czo10Qvqgp40ji6tMOP7BrBzM06T/tsYoMADBI3UeuGtdcXEK', 3, 3),
	('Barry', 'B', 'Barrison', 'ENG', '1-555-555-5555', 'barry@gmail.com', 'james10hash', 1, 1),
	('Curt', 'C', 'Curtson', 'ENG', '1-555-555-5555', 'curt@gmail.com', 'james10hash', 2, 2),
	('Donald', 'D', 'Donaldson', 'ENG', '1-555-555-5555', 'donald@gmail.com', 'james10hash', 3, 3),
	('Eric', 'E', 'Erickson', 'ENG', '1-555-555-5555', 'eric@gmail.com', 'james10hash', 1, 1),
	('Frank', 'F', 'Frankison', 'USA', '757-374-7393', 'frank@gmail.com', 'jeff10hash', 1, 1),
	('Greg', 'G', 'Gregson', 'BRZ', '1-555-213-4493', 'greg@gmail.com', 'chris10hash', 2, 2),
	('Harold', 'H', 'Haroldson', 'USA', '999-654-1236', 'harold@gmail.com', 'miranda10hash', 3, 3),
	('Ignis', 'I', 'Ignison', 'USA', '123-456-7890', 'ignis@gmail.com', 'brian10hash', 1, 1),
	('James', 'J', 'Jamison', 'ENG', '1-555-555-5555', 'james@gmail.com', 'james10hash', 2, 2),
	('Kelly', 'K', 'Kellison', 'ENG', '1-555-555-5555', 'kelly@gmail.com', 'james10hash', 3, 3),
	('Larry', 'L', 'Larrison', 'ENG', '1-555-555-5555', 'larry@gmail.com', 'james10hash', 5, 1),
	('Michael', 'M', 'Michaelson', 'ENG', '1-555-555-5555', 'michael@gmail.com', 'james10hash', 2, 2),
	('Nicholas', 'N', 'Nicholson', 'ENG', '1-555-555-5555', 'nicholas@gmail.com', 'james10hash', 3, 3),
	('Oscar', 'O', 'Oscarson', 'ENG', '1-555-555-5555', 'oscar@gmail.com', 'james10hash', 1, 1);
    
INSERT INTO sys_type (type_name)
	VALUES 
	('server'),
	('network appliance'),
	('database'),
	('workstation'),
	('software');

INSERT INTO system (sys_name, sys_ip_address, type_id, org_id)
	VALUES 
	('server1', '12.13.14.15', 1, 1),
	('switch1', '120.13.140.15', 2, 2),
	('database1', '20.33.54.115', 3, 3),
	('server2', '12.13.14.16', 1, 4),
	('database2', '20.33.54.116', 3, 5),
	('server3', '12.13.14.15', 1, 6),
	('switch2', '120.13.140.15', 2, 2),
	('database3', '20.33.54.115', 3, 3),
	('server4', '12.13.14.16', 1, 1),
	('database4', '20.33.54.116', 3, 4),	
    ('server5', '12.13.14.15', 1, 5),
	('switch3', '120.13.140.15', 2, 6),
	('database5', '20.33.54.115', 3, 3),
	('server6', '12.13.14.16', 1, 1),
	('database6', '20.33.54.116', 3, 5),	
    ('server7', '12.13.14.15', 1, 1),
	('switch4', '120.13.140.15', 2, 4),
	('database7', '20.33.54.115', 3, 3),
	('server8', '12.13.14.16', 1, 1),
	('database8', '20.33.54.116', 3, 2);
    
INSERT INTO artifact (art_text, org_id)
	VALUES 
	('artifact1 text', 1),
	('artifact2 text', 2),
	('artifact3 text', 3),
	('artifact4 text', 2),
	('artifact5 text', 1),
	('artifact6 text', 1),
	('artifact7 text', 2),
	('artifact8 text', 3),
	('artifact9 text', 2),
	('artifact10 text', 1),
	('artifact11 text', 1),
	('artifact12 text', 2),
	('artifact13 text', 3),
	('artifact14 text', 2),
	('artifact15 text', 1),
	('artifact16 text', 1),
	('artifact17 text', 2),
	('artifact18 text', 3),
	('artifact19 text', 2),
	('artifact20 text', 1);


INSERT INTO standard (stand_name, stand_version_rev_num, stand_effective_date)
	VALUES 
    ('stand1', '3.6',  '2016-01-01'),
    ('stand2', '3.1.2', '2016-02-02'),
    ('stand3', '3.3', '2016-03-03'),
    ('stand1', '3.7',  '2017-04-04'),
    ('stand2', '3.1.3', '2017-04-04');

INSERT INTO category (cat_name)
	VALUES 
	('category1'),
	('category2'),
	('category3'),
	('category4'),
	('category5'),
	('category6'),
	('category7'),
	('category8'),
	('category9'),
	('category10'),
	('category11'),
	('category12');


INSERT INTO standard_category (stand_id, cat_id, standcat_num)
	VALUES
	(1, 1, 1.1),
	(1, 2, 1.2),
	(1, 3, 1.3),
	(1, 4, 2.1),
	(1, 5, 2.2), 
	(4, 1, 1.1),
	(4, 2, 1.2),
	(4, 3, 1.3),
	(4, 4, 2.1),
	(4, 5, 2.2), 
	(2, 6, 1.1),
	(2, 7, 2.1),
	(5, 6, 1.1), 
	(5, 7, 2.1),
    (5, 8, 3.1),
	(3, 9, 1.1),
	(3, 10, 1.2);


INSERT INTO requirement (req_num, req_desc, req_simple_desc, standcat_id)
	VALUES 
	(1, 'Make stronger passwords', 'Password strength', 6),
 	(1, 'Install all the newest security patches', 'Install patches', 7),
 	(1, 'Make sure port 6000 is closed', 'Close ports', 8), 	
	(2, 'Make sure all important data is backed up', 'Backup data', 8),
	(1, 'Make sure folder permissions are set correctly', 'Fix permissions', 9),
	(1, 'requirement 6', 'simple 6', 10),
	(2, 'requirement 7', 'simple 7', 5),
 	(1, 'requirement 8', 'simple 8', 13),
 	(1, 'requirement 9', 'simple 9', 14), 	
	(1, 'requirement 10', 'simple 10', 16),
	(1, 'requirement 11', 'simple 11', 17),
	(1, 'requirement 12', 'simple 12', 15);


INSERT INTO req_amendment (amend_effective_date, amend_desc, req_id)
	VALUES
	(current_date(), 'Make even stronger passwords', 1),
	(current_date(), 'Install even more patches', 2),
	(current_date(), 'Make sure port 6666 is closed now', 3),
	(current_date(), 'Make 3 backups (was 1)', 4),
	(current_date(), 'requirement 8 updated', 11),
 	(current_date(), 'requirement 9 updated', 12);
   

INSERT INTO rating (rate_name, rate_abbv, rate_desc, rate_root_stand)
	VALUES 
	('Not Applicable', 'NA', 'Requirement doesn\'t apply to this system', 1),
	('Not Compliant', 'NC', 'System fails requirement in all respects', 1),
	('Partially Compliant', 'PC', 'System meets some parts of the requirement and fails other parts', 1),
	('Fully Compliant', 'FC', 'System meets all parts of this requirement.', 1),
	('Doesn\'t Apply', 'DA', 'Requirement doesn\'t apply to this system', 2),
	('Open', 'O', 'System fails some or all of this requirement.', 2),
	('Not Reviewed', 'NR', 'This requirements hasn\'t been reviewed yet for this system', 2),
	('Not a Finding', 'NF', 'System meets all aspects of this requirement.', 2),
	('Doesn\'t Apply', 'DA', 'Requirement doesn\'t apply to this system', 3),
	('Open', 'O', 'System fails some or all of this requirement.', 3),
	('Not Reviewed', 'NR', 'This requirements hasn\'t been reviewed yet for this system', 3),
	('Not a Finding', 'NF', 'System meets all aspects of this requirement.', 3);

INSERT INTO range_time (range_desc, range_min, range_max)
	VALUES 
	('Short Term', 0, 6),
	('Medium Term', 7, 12),
	('Long Term', 13, 24);

INSERT INTO system_requirement (sys_id, req_id, sysreq_notes, rate_id, range_id, art_id)
	VALUES 
	(1, 1, 'result1', 1, 1, 1),
	(1, 2, 'result2', 2, 2, 2),
	(1, 3, 'result3', 3, 3, 3),
	(1, 4, 'result4', 4, 2, 4),
	(1, 5, 'result5', 4, 2, 5),
	(1, 6, 'result6', 2, NULL, 6),
	(1, 7, 'result7', 1, 3, 7),
	(2, 1, 'result8', 8, 3, 8),
	(2, 4, 'result9', 9, 2, 9),
	(2, 6, 'result10', 10, 2, 10),
    (2, 8, 'result11', 10, 1, 11),
	(2, 10, 'result12', 12, 3, 12),
	(2, 11, 'result13', 1, 1, 13),
	(2, 2, 'result14', 2, 1, 14),
	(3, 3, 'result15', 3, 2, 15),
    (3, 4, 'result16', 4, 1, 16),
    (2, 9, 'result17', 10, 2, 11);