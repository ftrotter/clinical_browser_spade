#!/bin/sh
mongoimport -u ftrotter -p --authenticationDatabase admin --jsonArray --db batea --collection projectarticles --file Medicine.articles.json
mongoimport -u ftrotter -p --authenticationDatabase admin --jsonArray --db batea --collection projectarticles --file Anatomy.articles.json
