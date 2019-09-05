# waycool-php-task

# NGINX Configuration

```
server {
    listen 80;
    listen [::]:80;

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
    }

    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    location ~ /\.ht {
            deny all;
    }
}
```