create table admin(
    admin_id int PRIMARY KEY AUTO_INCREMENT,
    username varchar(20) unique,
    password varchar(20)
);
CREATE TABLE admin_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    action_type ENUM('LOGIN','CREATE', 'READ', 'UPDATE', 'DELETE', 'SMS_SENT'),
    table_name VARCHAR(50),
    affected_row_id INT,
    action_description TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE SET NULL
);
CREATE TABLE admin_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    select_enabled BIT DEFAULT 0
);
INSERT INTO admin_settings (select_enabled) VALUES (0);


create table volunteer(
    volunteer_id int PRIMARY KEY AUTO_INCREMENT,
    full_name varchar(200),
    phone varchar(20) unique,
    email varchar(70),
    zip_code mediumint,
    preference ENUM('Reminder every month'
    , 'Committed every month', 'Committed one time', 'none'),
    car_size ENUM('6', '8', '12', '18' ,'48', 'none'),
    comment varchar(255),
    language Enum('English', 'Arabic', 'Farsi', 'Spanish', 'Urdu', 'Myanmar', 'Pashto', 'Other'),
    approved bit DEFAULT 0,
    replied ENUM('Delivery', 'Packing', 'Both', 'Next month', 'Delete from list', 'No response') DEFAULT 'No response',
    replied_date TIMESTAMP,
    notf_preference text,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE volunteer_archive (
    volunteer_id INT PRIMARY KEY,
    full_name VARCHAR(200),
    phone VARCHAR(20),
    email VARCHAR(70),
    zip_code mediumint,
    preference ENUM('Reminder every month', 'Committed every month', 'Committed one time', 'none'),
    car_size ENUM('6', '8', '12', '18', '48', 'none'),
    comment VARCHAR(255),
    language Enum('English', 'Arabic', 'Farsi', 'Spanish', 'Urdu', 'Myanmar', 'Pashto', 'Other'),
    approved BIT DEFAULT 0,
    replied ENUM('Delivery', 'Packing', 'Both', 'Next month', 'Delete from list', 'No response', 'Picked up', 'Delivered') DEFAULT 'No response',
    replied_date TIMESTAMP,
    notf_preference text,
    reg_date TIMESTAMP,
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

create table recipient(
    recipient_id int AUTO_INCREMENT,
    full_name varchar(100),
    phone varchar(20) unique,
    email varchar(70),
    distributor_id int,
    textable bit,
    num_items tinyint DEFAULT 1,
    address varchar(255),
    apt_num varchar(60),
    comp_name varchar(255),
    gate_code varchar(60),
    city varchar(50),
    zip_code mediumint,
    latitude decimal(10, 8),
    longitude decimal(10, 8),
    hotel_info varchar(125),
    language Enum('English', 'Arabic', 'Farsi', 'Spanish', 'Urdu', 'Myanmar', 'Pashto', 'Other'),
    english ENUM('Fluent', 'Intermediate', 'Basic', 'None'),
    num_adults tinyint,
    num_seniors tinyint,
    num_children tinyint,
    approved bit DEFAULT 0,
    replied ENUM('Yes', 'Owns a car', 'Next month', 'Delete from list', 'No response') DEFAULT 'No response',
    replied_date TIMESTAMP,
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    primary key(recipient_id, full_name, phone)
);

CREATE TABLE recipient_details (
    recipient_id INT PRIMARY KEY,
    gender ENUM('Male', 'Female'),
    householder_name VARCHAR(100),
    date_arrived DATE,
    proxy_name varchar(100),
    proxy_phone varchar(20), 
    age TINYINT,
    country char(2),
    personal_status enum('Married', 'Single', 'Divorced', 'Widowed', 'Separated'),
    work_status ENUM('No Permit', 'Full-Time', 'Part-Time', 'Looking', 'Disability', 'Self-Employed'),
    nationality ENUM('American Indian or Alaska Native', 'Asian'
    ,'Black or African American', 'Hispanic or Latino'
    ,'Native Hawaiian or Other Pacific Islander'
    ,'White',  'Other'),
    income mediumint,
    spouse_name varchar(100),
    spouse_age TINYINT,
    spouse_work ENUM('Single','No Permit', 'Full-Time', 'Part-Time', 'Looking', 'Disability', 'Self-Employed'),
    income_per ENUM('No Income', 'Per Week', 'Per Month', 'Per Year'),
    gov_aid text,
    food_stamps ENUM('All Family Members', 'Some Family Members', 'Not Yet', 'Not Eligible'),
    health_insurance ENUM('All Family Members', 'Some Family Members', 'Not Yet', 'Not Eligible', 'Medicare', 'Cannot Afford'),
    comment text,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_recipient_details_recipient FOREIGN KEY (recipient_id) REFERENCES recipient(recipient_id)
);

create table recipient_children(
    child_id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT,
    name VARCHAR(100),
    gender ENUM('Male', 'Female'),
    age TINYINT,
    school_status ENUM('Yes', 'No', 'Other'),
    job_status ENUM('Yes', 'No', 'Disability', 'No Permit', 'Not Eligible'),
    has_disability BIT
);

CREATE TABLE recipient_archive(
    recipient_id INT PRIMARY KEY,
    full_name varchar(100),
    phone varchar(20),
    email varchar(70),
    proxy_name varchar(100),
    proxy_phone varchar(20), 
    distributor_id int,
    textable bit,
    num_items tinyint DEFAULT 1,
    address varchar(255),
    apt_num varchar(60),
    comp_name varchar(255),
    gate_code varchar(60),
    city varchar(50),
    zip_code mediumint,
    latitude decimal(10, 8),
    longitude decimal(10, 8),
    hotel_info varchar(125),
    language Enum('English', 'Arabic', 'Farsi', 'Spanish', 'Urdu', 'Myanmar', 'Pashto', 'Other'),
    english ENUM('Fluent', 'Intermediate', 'Basic', 'None'),
    spouse_name varchar(100),
    spouse_age TINYINT,
    spouse_work ENUM('Single', 'No Permit', 'Full-Time', 'Part-Time', 'Looking', 'Disability', 'Self-Employed'),
    num_adults tinyint,
    num_seniors tinyint,
    num_children tinyint,
    approved bit,
    replied ENUM('Yes', 'Owns a car', 'Next month', 'Delete from list', 'No response') DEFAULT 'No response',
    replied_date TIMESTAMP,

    gender ENUM('Male', 'Female'),
    age TINYINT,
    householder_name VARCHAR(100),
    date_arrived DATE,
    country char(2),
    personal_status enum('Married', 'Single', 'Divorced', 'Widowed', 'Separated'),
    work_status ENUM('No Permit', 'Full-Time', 'Part-Time', 'Looking', 'Disability', 'Self-Employed'),
    nationality ENUM('American Indian or Alaska Native', 'Asian'
    ,'Black or African American', 'Hispanic or Latino'
    ,'Native Hawaiian or Other Pacific Islander'
    ,'White',  'Other'),
    income mediumint,
    income_per ENUM('No Income', 'Per Week', 'Per Month', 'Per Year'),
    gov_aid text,
    food_stamps ENUM('All Family Members', 'Some Family Members', 'Not Yet', 'Not Eligible'),
    health_insurance ENUM('All Family Members', 'Some Family Members', 'Not Yet', 'Not Eligible', 'Medicare', 'Cannot Afford'),
    comment text,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE sms_templates (
    template_id INT PRIMARY KEY AUTO_INCREMENT,
    template_text TEXT NOT NULL,
    table_name EnUM('recipient','volunteer'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sms_logs (
    sms_id INT AUTO_INCREMENT PRIMARY KEY,
    user EnUM('recipient','volunteer'),
    user_phone VARCHAR(20) NOT NULL,
    sent_message TEXT,
    sent_by INT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'failed'),
    error_message TEXT,
    recieved TEXT,
    recieved_at TIMESTAMP
);

create table location_logs(
    location_id INT PRIMARY KEY AUTO_INCREMENT,
    volunteer_id INT,
    address VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(10, 8),
    method ENUM('Locate me', 'Manually'),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

create table delivery_logs(
    delivery_id INT PRIMARY KEY AUTO_INCREMENT,
    volunteer_id INT,
    recipient_id INT,
    admin_id INT,
    status ENUM('Picked up','Selected', 'Confirmed', 'Completed', 'Deselected'),
    reg_date TIMESTAMP DEFAULT current_timestamp
);


create table delivery(
    delivery_id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_id int,
    volunteer_id int,
    status ENUM('Pending', 'Confirmed', 'Completed', 'Failed', 'Cancelled') DEFAULT 'Pending',
    selected_date TIMESTAMP,
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_delivery_recipient FOREIGN KEY (recipient_id) REFERENCES recipient(recipient_id),
    CONSTRAINT fk_delivery_volunteer FOREIGN KEY (volunteer_id) REFERENCES volunteer(volunteer_id)
);

create table delivery_archive(
    arch_id INT PRIMARY KEY AUTO_INCREMENT,
    delivery_id INT,
    recipient_id int,
    volunteer_id int,
    status ENUM('Pending', 'Confirmed', 'Completed', 'Failed', 'Cancelled') DEFAULT 'Pending',
    selected_date TIMESTAMP,
    update_date TIMESTAMP,
    archive_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE collect_location (
    location_id INT PRIMARY KEY AUTO_INCREMENT,
    address VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(10, 8),
    active BIT DEFAULT 1,
    pickup_time TIME,
    num_items TINYINT,
    phone VARCHAR(20)
);


DELIMITER $$

CREATE TRIGGER after_recipient_insert
AFTER INSERT ON recipient 
FOR EACH ROW
BEGIN
    INSERT INTO delivery (recipient_id, volunteer_id, status, selected_date)
    VALUES (NEW.recipient_id, NULL, 'Pending', null);
END$$

DELIMITER ;

CREATE INDEX idx_recipient_approved_replied_distributor
ON recipient (approved, replied, distributor_id);

CREATE INDEX idx_delivery_status_volunteer
ON delivery (status, volunteer_id, selected_date);

CREATE INDEX idx_delivery_status_volunteer_selected
ON delivery (status, volunteer_id, selected_date);


UPDATE recipient
SET phone = REPLACE(REPLACE(REPLACE(REPLACE(phone, '(', ''), ')', ''), '-', ''), '+', '');
