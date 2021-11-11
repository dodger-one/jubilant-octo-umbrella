# db/Dockerfile
FROM postgres:14

ADD ./docker/db/1_init.sql /docker-entrypoint-initdb.d
#ADD ./docker/db/2_copy.sql /docker-entrypoint-initdb.d
RUN chmod a+r /docker-entrypoint-initdb.d/*
