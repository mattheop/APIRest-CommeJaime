FROM mariadb:latest
USER root
ENV LANG=C.UTF_8

COPY docker/mysql/custom.cnf /etc/mysql/conf.d/custom.cnf
COPY docker/mysql/sql/schema.sql /docker-entrypoint-initdb.d/1-schema.sql
COPY docker/mysql/sql/fixtures.sql /docker-entrypoint-initdb.d/2-fixtures.sql

RUN chmod 0444 /etc/mysql/conf.d/custom.cnf