-- Adminer 4.7.1 PostgreSQL dump

INSERT INTO "articles" ("id", "title", "timestamp", "text", "author_id") VALUES
(1,	'This is a test',	'2019-03-07 07:49:17.605633',	'Lorem ipsum dolor sit amet',	1);

INSERT INTO "cat_art" ("article_id", "category_id") VALUES
(1,	1);

INSERT INTO "categories" ("id", "name") VALUES
(1,	'Random');

INSERT INTO "comments" ("id", "article_id", "author_id", "text", "timestamp") VALUES
(1,	1,	1,	'YoU cAnT DO tHIs!!!',	'2019-03-07 08:00:56.006518');

INSERT INTO "users" ("id", "username", "email", "password_hash", "permissions") VALUES
(1,	'Kwantuum',	'sam@gmail.com',	'eajzepjaz',	2),
(3,	'Samuel le Cruel',	'samuelfdegueldre@gmail.com',	'$2y$10$MtHDJHo8wq6cmWd4QvlnTut/SrWGtiNr4sCcVx4Cp52goF9Vnd99m',	1);

-- 2019-03-07 08:21:19.473914+00
