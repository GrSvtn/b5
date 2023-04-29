CREATE TABLE user_pass (
    user_id int(10) unsigned NOT NULL,
    login varchar(16) NOT NULL,
    hash_pass varchar(16) NOT NULL,
    FOREIGN KEY (user_id)  REFERENCES form(id),
    PRIMARY KEY (user_id)
);