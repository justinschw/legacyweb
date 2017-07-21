# Legacyweb
Use modern web on older software/hardware using a raspberry pi as a middle-man.

## Installation
This is intended to be installed onto a raspberry pi with a raspbian installation and a wifi connection to the Internet. The ethernet will be plugged into your old computer.

To install legacyweb, you need to first install ansible. Log in as root and then execute:
`apt-get install ansible`

Then you just run the install script:
`./runinstall.sh`

If you haven't already, you will then probably want to set the user password for the raspberry pi to something other than the default. (something difficult to guess)

## Components
The legacyweb node serves multiple purposes for your older personal computer.

### Router
First and foremost, Legacyweb turns your raspberry pi into a sort of "wireless bridge" for your older computer. The 'gateway' role sets up the following:

* IPv4 forwarding
* DHCP server
* Local DNS

After you install, you can actually plug your old computer's ethernet into the raspberry pi and get an IP address and an Internet connection. IPTables firewall is also set up so that the only connection to your raspberry pi from the wifi network is port 22 (SSH). This port is used for administration purposes.

The default domain name for the raspberry pi is 'legacyweb.net'.
If you want a different domain name you can edit group_vars/all and change the value of the 'domain_name' variable to whatever you want.

### Web services
Most modern web pages are not compatible with very old versions of browsers, etc. So the main purpose of legacyweb is to provide simple, easy-to-digest web pages that can be accessed and used from older browsers such as Netscape Navigator. It is intended that the number of web services continue to grow with time. Currently the following services are implemented:

* Maps (maps.legacyweb.net) - A very simple google maps application.
* Wikipedia (wikipedia.legacyweb.net) - A very stripped down version of wikipedia
* Weather (weather.legacyweb.net) - A simple weather app that lets you view today's weather as well as a five-day forecast.
* YouTube (youtube.legacyweb.net) - A simple youtube app that lets you stream youtube to vlc media player on the pi, which can be connected to your old pc via a tuner card.

**Note on Maps:** You will have to update the google_api_key varilable in group_vars/all with your Google API key before installing.  You can get it at: https://console.developers.google.com/. Click "Credentials" on the left pane, then "Create credentials" to get your API key. On the dashboard you will have to enable the following APIs:

* Google Static Maps API
* Google Maps Directions API
* Google Places API Web Service
* Google Maps Distance Matrix API
* Google Maps Elevation API
* Google Maps Geocoding API
* Google Maps Geolocation API
* Google Maps Roads API
* Google Maps TimeZone API

**Note on Weather:** You need an openweathermaps api key. Update weather_api_key in group_vars/all.

**Note on YouTube:** Instructions on how to set up vlc for this will come.

You can view all the available services from the home page: **home.legacyweb.net**

The basic rule of thumb for developing web pages is:

* Keep javascript to a minimum. Only the simplest, most basic javascript is supported by old HTML standards.
* Do as much as you can on the server side (PHP). On the client side just use form posts and let the server do all the work.
* Use old HTML features such as tables, not divs.
I have found it helpful to google web dev stuff using google's Tools > Any Time > custom range feature; look only for pages from 2001 and earlier.

### Email
Most email services today use TLS for the connection in order to keep it secure. However, a lot of older email clients do not support this TLS but do email over a plain connection. Legacyweb sets up a 'tunnel' using stunnel4 which your old email client can use to establish connections to sites like gmail (default), gmx or pretty much any email service.

I personally use gmx so the default email service used in this project is gmx. You can change it to whatever you like. Just edit group_vars/all and change the following:

* email_service: this is for naming purposes. Default is gmail.
* email_local_pop_port: this is the pop port that your raspberry pi is listening on. You can leave it alone.
* email_remote_pop_port: this is the pop port the remote (TLS-enabled) email service is listening on. Match it to whatever your email service uses.
* email_local_smtp_port: this is the smtp port that your raspberry pi is listening on. You can leave it alone.
* email_remote_pop_port: this is the smtp port the remote (TLS-enabled) email service is listening on. Match it to whatever your email service uses.
* email_remote_pop_server: Your email service's remote pop server.
* email_remote_smtp_server: Your email service's remote smtp server.
* ca_file_path: This is the direct path to the CA cert that your email service uses to authenticate. It should be under /etc/ssl/certs/ so look for the one that is named the same as what authorizes your email service.

If you use gmail, you can use the commented-out section in group_vars/all.

When you set up your email client on your old machine, set the username and password the same way you would for an email client (username is usually your full email, as for gmail), but for your smtp and pop servers, point them at 192.168.3.1, port 110 for pop and port 9025 for smtp (assuming you did not change the gateway IP or ports) and make sure to enable SMTP authentication and use the same credentials there.

If you use a really old client that doesn't support smtp authentication, you will need to set the credentials under the CHANGE ME section. Make sure to delete the ansible files after installation. **Do not** leave a readable plain text file with your username and password. Also on your old computer you will need to set your smtp port to 25 instead of the 9025 port specified above. Basically postfix is going to do all of your smtp authentication for you.

For Gmail users: when you try to log on, it will block you. That is because for some reason google blocks stunnel and says it is not secure. You will get an email about it, and you can optionally enable access. AFAIK stunnel is pretty secure so as long as nobody else has access to the unencrypted channel between your old PC and your raspberry pi, you should be good.

For other GMX users, you can get the information for configuring email here:
legacyweb.wikia.com

The rest of you are on your own. :)

### Local DNS
One of the functions of DNSmasq is to serve as a localized, LAN-only DNS server for your old computer. This is basically to make it easy for you to access the internal pages. Pretty much all hostnames will be mapped to the same address (your gateway IP) since for this project we are hosting all web services from the same raspberry pi. But from the actual web services we determine which actual page to forward to.

There is a tool to add a hostname => page mappings, you can use it like so:

`add-private-host (hostname) (page)`

where 'page' includes the relative path and filename to the page under the /var/www/html directory. As an example:

`add-private-host maps.legacyweb.net gmaps/maps.php`

This is what we use to install the maps page for legacyweb.

Note that all hostnames must end with your domain name: (service).(domain name)

### Windows share
Legacyweb also comes with samba installed. If you are running on a raspberry pi then this works out of the box, otherwise you can edit the variables in group_vars/all. The default workgroup name is "LEGACYWEB" and the user is pi. The share is called "WINSHARE."

You can access this from windows by searching through the network places or by creating a mapped network drive and typing in:
`\\192.168.3.1\winshare`

