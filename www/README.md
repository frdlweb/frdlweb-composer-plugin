# www

directory

## The www directory

This is the directory where you SHOULD publish your productional sites (e.g. CMS) on the web.
It is highly recommended to create one main directory www/my-example-project each project and then 
the administrator SHOULD route the projects domain to this directory!

Example:
<VirtualHost 93.184.216.34:80>
  ServerName example.com
  DocumentRoot /usr/home/www/my-example-project
</VirtualHost>


## Adding/creating projects and servers

You do not need to create projects manually, it is recommended to use the Webfan Application Composer Backends 
appropriate Wizards!

