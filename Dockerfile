FROM nginx:alpine

# Install PHP and required extensions (Alpine 3.21+ uses php84)
RUN apk add --no-cache \
    php84 \
    php84-fpm \
    php84-openssl \
    php84-curl \
    php84-mbstring \
    && ln -sf /usr/bin/php84 /usr/bin/php

# Configure PHP-FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/' /etc/php84/php-fpm.d/www.conf \
    && sed -i 's/;listen.owner = nobody/listen.owner = nginx/' /etc/php84/php-fpm.d/www.conf \
    && sed -i 's/;listen.group = nobody/listen.group = nginx/' /etc/php84/php-fpm.d/www.conf \
    && sed -i 's/user = nobody/user = nginx/' /etc/php84/php-fpm.d/www.conf \
    && sed -i 's/group = nobody/group = nginx/' /etc/php84/php-fpm.d/www.conf

# Copy site files
COPY static-site/ /usr/share/nginx/html/
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Create startup script
RUN echo '#!/bin/sh' > /start.sh \
    && echo 'php-fpm84 &' >> /start.sh \
    && echo 'nginx -g "daemon off;"' >> /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
