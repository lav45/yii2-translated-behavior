/**
 * SQLite
 */

DROP TABLE IF EXISTS "post";
CREATE TABLE "post" (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "status_id" INTEGER NOT NULL
);

DROP TABLE IF EXISTS "post_lang";
CREATE TABLE "post_lang" (
  "post_id" INTEGER NOT NULL,
  "lang_id" VARCHAR (5) NOT NULL,
  "title" VARCHAR (128) NOT NULL,
  "description" TEXT NOT NULL,
  PRIMARY KEY ("post_id", "lang_id")
);

DROP TABLE IF EXISTS "status";
CREATE TABLE "status" (
  "id" INTEGER NOT NULL PRIMARY KEY
);

DROP TABLE IF EXISTS "status_lang";
CREATE TABLE "status_lang" (
  "status_id" INTEGER NOT NULL,
  "lang_id" VARCHAR (5) NOT NULL,
  "title" VARCHAR (128) NOT NULL,
  PRIMARY KEY ("status_id", "lang_id")
);
