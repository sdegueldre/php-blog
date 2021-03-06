--Script initialisation SQL

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS articles CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS cat_art CASCADE;

--Table articles
CREATE TABLE users (
  id SERIAL PRIMARY KEY NOT NULL,
  username VARCHAR(255) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  permissions INT NOT NULL DEFAULT 0
);

CREATE TABLE articles (
  id SERIAL PRIMARY KEY NOT NULL,
  title VARCHAR(255) NOT NULL,
  timestamp TIMESTAMP DEFAULT NOW(),
  text TEXT NOT NULL,
  author_id INT REFERENCES users(id) ON DELETE CASCADE
  -- join Catégorie et Commentaires
);

CREATE TABLE comments (
  id SERIAL PRIMARY KEY NOT NULL,
  article_id INT REFERENCES articles(id) ON DELETE CASCADE,
  author_id INT REFERENCES users(id) ON DELETE CASCADE,
  text TEXT NOT NULL,
  timestamp TIMESTAMP DEFAULT NOW() NOT NULL
);

CREATE TABLE categories (
  id SERIAL PRIMARY KEY NOT NULL,
  name VARCHAR(255) NOT NULL
);

CREATE TABLE cat_art(
  article_id INT REFERENCES articles(id) ON DELETE CASCADE,
  category_id INT REFERENCES categories(id) ON DELETE CASCADE
);

INSERT INTO "users" ("username", "email", "password_hash", "permissions")
VALUES ('admin',	'admin@admin.com',	'$2y$10$jMVTtkvxqh2QwvhQJU3Gj.MdnsnsczYbbkVnimcPFfFNDcvWmynu.',	2);
