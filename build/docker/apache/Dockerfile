ARG APACHE_VERSION=""
FROM httpd:${APACHE_VERSION:+${APACHE_VERSION}-}alpine

RUN apk update; \
    apk upgrade;

RUN echo "IncludeOptional /usr/local/apache2/conf/sites-enabled/*.conf" \
    >> /usr/local/apache2/conf/httpd.conf
