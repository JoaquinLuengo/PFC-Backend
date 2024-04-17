FROM nginx:1.19
#ENV http_proxy "http://10.7.6.6:3128"
#ENV https_proxy "http://10.7.6.6:3128"
ARG UID
EXPOSE $UID

RUN adduser -u ${UID} --disabled-password --gecos "" appuser

COPY default.conf /etc/nginx/conf.d/
