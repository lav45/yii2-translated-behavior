/**
 * SQLite
 */

DROP TABLE IF EXISTS "lang";
CREATE TABLE "lang" (
  "id" VARCHAR (2) NOT NULL PRIMARY KEY
);

DROP TABLE IF EXISTS "post";
CREATE TABLE "post" (
  "id" INTEGER NOT NULL PRIMARY KEY
);

DROP TABLE IF EXISTS "post_lang";
CREATE TABLE "post_lang" (
  "post_id" INTEGER NOT NULL,
  "lang_id" VARCHAR (2) NOT NULL,
  "title" VARCHAR (128) NOT NULL,
  "description" TEXT NOT NULL,
  PRIMARY KEY ("post_id", "lang_id")
);

