#!/usr/bin/env bash

ssh $1 << EOF
rm -rf public_html
git clone https://github.com/sdegueldre/php-blog.git public_html
cd public_html
composer install
sed -i 's/\/css\/master.css/css\/master.css/' 'templates/common.twig'
psql -f src/Table.sql
psql -f src/initialData.sql
EOF
