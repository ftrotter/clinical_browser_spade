#!/bin/sh
mongoimport -u ftrotter -p --authenticationDatabase admin --jsonArray --collection project_articles --file Medicine.articles.json
