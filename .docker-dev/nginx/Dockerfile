FROM nginx:1.17-alpine
WORKDIR "/sources"

ARG DOCKER_USER
ARG DOCKER_UID
ARG DOCKER_GID

RUN addgroup -S -g ${DOCKER_GID} ${DOCKER_USER} && \
    adduser -S -u ${DOCKER_UID} -G ${DOCKER_USER} ${DOCKER_USER}

# Change nginx user to the owner of sources
RUN sed -i s/"user  nginx"/"user $DOCKER_USER"/g /etc/nginx/nginx.conf
