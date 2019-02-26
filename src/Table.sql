--Script initialisation SQL

--Création de la base de donnés db_blog

CREATE DATABASE db_blog;

--Table articles

CREATE TABLE articles (
  id_article SERIAL PRIMARY KEY,
  title VARCHAR(255),
  article_date DATE,
  content TEXT,
  id_user INT
  -- join Catégorie et Commentaires
);

CREATE TABLE users (
  id_user SERIAL PRIMARY KEY,
  email TEXT NOT NULL UNIQUE,
  hash_pass VARCHAR(255),
  permission INT

);

CREATE TABLE comments (
  id_comment SERIAL PRIMARY KEY,
  id_article INT,
  id_user INT,
  comment_text TEXT,
  comment_date DATE
);

CREATE TABLE category (
  id_cat SERIAL PRIMARY KEY,
  nom_cat VARCHAR(255)
);
CREATE TABLE cat_art(
  id_article INT,
  id_cat INT
);
