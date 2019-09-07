# waycool-php-task

## NGINX Configuration

```
server {
    ## NO-SSL
    # Port for reverse proxy easiness
    listen 5080;
    listen [::]:5080;

    ## SSL
    # listen 443;
    # listen [::]:443;
    # include snippets/self-signed.conf;
    # include snippets/ssl-params.conf;

    root /var/www/workspace/dev/waycool;

    # Add index.php to the list if you are using PHP
    index index.php;

    server_name waycool.local;

    location / {
        # First attempt to serve request as file, then
        # as directory, then fall back to displaying a 404.
        try_files $uri $uri/ =404;
        if (!-e $request_filename) {
            rewrite ^.*$ /index.php last;
        }
    }

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php$ {
        # regex to split $uri to $fastcgi_script_name and $fastcgi_path
        fastcgi_split_path_info ^(.+\.php)(/.+)$;

        # Check that the PHP script exists before passing it
        try_files $fastcgi_script_name =404;

        # Bypass the fact that try_files resets $fastcgi_path_info
        # see: http://trac.nginx.org/nginx/ticket/321
        set $path_info $fastcgi_path_info;
        fastcgi_param PATH_INFO $path_info;

        fastcgi_index index.php;
        include fastcgi.conf;

        include /etc/nginx/fastcgi_params;

        fastcgi_param SCRIPT_FILENAME /var/www/workspace/dev/waycool/index.php;
        fastcgi_pass unix:/run/php/php7.2-fpm.sock;

        fastcgi_param LOCAL_DB_HOST 'localhost';
        fastcgi_param LOCAL_DB_USER 'root';
        fastcgi_param LOCAL_DB_PWD 'p@ssw0rd';
        fastcgi_param LOCAL_DB_NAME 'waycool';
        fastcgi_param DEV_ENVIRONMENT 'development';

        fastcgi_param SMTP_HOST 'smtp.sendgrid.net';
        fastcgi_param SMTP_USER 'MY_USER';
        fastcgi_param SMTP_PASS 'MY_PASS';
        fastcgi_param SMTP_PORT 587;
        fastcgi_param EMAIL_FROM 'vaishak@kallore.in';

        fastcgi_param GOOGLE_MAPS_API_KEY 'YOUR_GOOGLE_MAPS_KEY';
    }

    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    location ~ /\.ht {
            deny all;
    }
}
```

## SSL Self Signed

[Detailed Post](https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-nginx-in-ubuntu-16-04)

Execute following command and enter data as required.

```
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/nginx-selfsigned.key -out /etc/ssl/certs/nginx-selfsigned.crt
```

While we are using OpenSSL, we should also create a strong Diffie-Hellman group, which is used in negotiating Perfect Forward Secrecy with clients.

```
sudo openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048
```

### Create a Configuration Snippet Pointing to the SSL Key and Certificate

```
sudo nano /etc/nginx/snippets/self-signed.conf
```

Paste the following

```/etc/nginx/snippets/self-signed.conf
ssl_certificate /etc/ssl/certs/nginx-selfsigned.crt;
ssl_certificate_key /etc/ssl/private/nginx-selfsigned.key;
```

### Create a Configuration Snippet with Strong Encryption Settings

```
sudo nano /etc/nginx/snippets/ssl-params.conf
```
Paste the following

```/etc/nginx/snippets/ssl-params.conf
# from https://cipherli.st/
# and https://raymii.org/s/tutorials/Strong_SSL_Security_On_nginx.html

ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
ssl_prefer_server_ciphers on;
ssl_ciphers "EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH";
ssl_ecdh_curve secp384r1;
ssl_session_cache shared:SSL:10m;
ssl_session_tickets off;
ssl_stapling on;
ssl_stapling_verify on;
resolver 8.8.8.8 8.8.4.4 valid=300s;
resolver_timeout 5s;
# Disable preloading HSTS for now.  You can use the commented out header line that includes
# the "preload" directive if you understand the implications.
#add_header Strict-Transport-Security "max-age=63072000; includeSubdomains; preload";
add_header Strict-Transport-Security "max-age=63072000; includeSubdomains";
add_header X-Frame-Options DENY;
add_header X-Content-Type-Options nosniff;

ssl_dhparam /etc/ssl/certs/dhparam.pem;
```

Then update the listen section in nginx config file as following.

```
    ## NO-SSL
    # listen 80;
    # listen [::]:80;

    ## SSL
    listen 443;
    listen [::]:443;
    include snippets/self-signed.conf;
    include snippets/ssl-params.conf;
```

## Adjust the Firewall

```
sudo ufw allow 'Nginx Full'
sudo ufw delete allow 'Nginx HTTP'
```