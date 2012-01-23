Installation
============

* If you're already familiar with httpd, directly run the webapp from a browser. Others continue reading the article

== Finding the Settings ==

* find /etc/ | grep httpd\.conf$

This fetches you the location of your apache server config file. In ubuntu it might be apache2.conf

* egrep -iw '^user|^group' /etc/httpd/conf/httpd.conf

/etc/httpd/conf/httpd.conf is the config file that is fetched using the first command. In this stage,
notedown the username and groupname of the http-user.

* cd /var/www/labyrinth/

Navigate to the application source folder

* sudo chown -R httpd-user:httpd-group .

httpd-user is the username, and httpd-group is the group name fetched from the egrep run on httpd.conf

* Now you can proceed to the installation by opening the webapp from a browser.



