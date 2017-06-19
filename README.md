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

..*IPv4 forwarding
..*DHCP server
..*Local DNS

After you install, you can actually plug your old computer's ethernet into the raspberry pi and get an IP address and an Internet connection. IPTables firewall is also set up so that the only connection to your raspberry pi from the wifi network is port 22 (SSH). This port is used for administration purposes.

The default domain name for the raspberry pi is 'legacyweb.net'.
If you want a different domain name you can edit group_vars/all and change the value of the 'domain_name' variable to whatever you want.

### Web services
Most modern web pages are not compatible with very old versions of browsers, etc. So the main purpose of legacyweb is to provide simple, easy-to-digest web pages that can be accessed and used from older browsers such as Netscape Navigator. It is intended that the number of web services continue to grow with time. Currently the following services are implemented:

..*Maps (maps.legacyweb.net) - A very simple google maps application

You can view all the available services from the home page: **home.legacyweb.net**

The basic rule of thumb for developing web pages is:

..*Keep javascript to a minimum. Only the simplest, most basic javascript is supported by old HTML standards.
..*Do as much as you can on the server side (PHP). On the client side just use form posts and let the server do all the work.
..*Use old HTML features such as tables, not divs.
I have found it helpful to google web dev stuff using google's Tools > Any Time > custom range feature; look only for pages from 2001 and earlier.

### Email
Most email services today use TLS for the connection in order to keep it secure. However, a lot of older email clients do not support this TLS but do email over a plain connection. Legacyweb sets up a 'tunnel' using stunnel4 which your old email client can use to establish connections to sites like gmail (default), gmx or pretty much any email service.

### Local DNS
One of the functions of DNSmasq is to serve as a localized, LAN-only DNS server for your old computer. This is basically to make it easy for you to access the internal pages. Pretty much all hostnames will be mapped to the same address (your gateway IP) since for this project we are hosting all web services from the same raspberry pi. But from the actual web services we determine which actual page to forward to.

There is a tool to add a hostname => page mappings, you can use it like so:

`add-private-host (hostname) (page)`

where 'page' includes the relative path and filename to the page under the /var/www/html directory. As an example:

`add-private-host maps.legacyweb.net gmaps/maps.php`

This is what we use to install the maps page for legacyweb.

Note that all hostnames must end with your domain name: (service).(domain name)
