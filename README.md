# Geeklog Ban plugin - version 2.0.4

* Current Maintainers: [Geeklog Community Members](https://github.com/orgs/Geeklog-Plugins/people)
* Original Author: Tom Willett
* Release Date: 2022-01-17

**Requires minimum Geeklog version 2.1.1

* Features
* Changelog
* Installing
* Upgrading
* Configuration

## Summary of features
This plugin is designed for Geeklog version 2.1.1 or higher. It can also work with the GUS plugin (https://github.com/Geeklog-Plugins) version 1.7.3 or higher.

Note: The Ban Plugin should be the first plugin in the Geeklog Administrator Plugin List. If the visitor is banned this saves the additional overhead of having to load the other plugins. You should confirm this after you install/upgrade the plugin as it is not done automatically.

The Ban plugin allows you to ban people and bots from your website.  You can ban a bot/visitor by IP, Referer, User Agent or Script Name.  Exact matches and case insensitive regular expressions (regex) are used to give you great flexibility.  The php preg_match function is used to do the regular expressions matches.

As of Ban plugin v2.0.0 new features include the ability to set a Ban Status for a Ban record, use the Stop Forum Spam Database to ban IPs, and Auto Ban visitors based on their previous visits using the GUS Plugin data. All these features have multiple config settings that can be set in the Ban config.php file.
Ban Plugin v2.0.4 supports PHP 8.0.0 and PostgreSQL, and includes Japanese and Persian language files.

### Ban Status

There are 5 Ban Status types. A White Ban Status allows any visitor that matches the status to proceed to the requested page. This is used to make sure specific visitors that you want cannot be banned (like Admins and Google Bot). White lists are always checked first. The Normal Ban Status is the default. Whatever is banned will be banned until the record is deleted by an Administrator. The rest of the  Ban Statuses will last only a certain time period (TTL = Time To Live) before the Ban record is deleted. By default the Ban Status TTL Short is set to one day, TTL Medium is set to 1 week, and TTL Long is set to 1 month.

### Stop Forum Spam Database (SFS)
You can also now ban IPs automatically that exist in the Stop Forum Spam database (https://www.stopforumspam.com). The Ban plugin doesn't access the database on the SFS website directly as that would be very inefficient. Instead a file stored locally on your server (/plugins/ban/files/bannedips.csv) is used. You can either download the csv file manually (https://www.stopforumspam.com/downloads - All IPs in CSV (one line, comma seperated)) or setup the plugin to auto download the file every so often. It is recommended that the file is downloaded once a week. Also if you do setup the Auto download (this is a beta feature and disabled by default) of the file please make sure it does work (check the Geeklog error.log file) as if the Ban Plugin attempts to download the file to often your own IP could get banned on the SFS website. There are checks in the plugin to prevent downloading the file to often but they may not always be accurate.

### Auto Bans
Auto banning of IPs is based on data from the GUS plugin. It is disabled by default and requires the GUS plugin to be installed and enabled to work. Currently there are 4 options which can be configured in the config.php file. 

It is recommended to use the default settings of the Auto Ban first and then slowly tweak them to your needs. Be VERY careful with the Auto Ban as it could ban bots you want like Googlebot, msnbot, etc... (hint add those to your white list using either the user agent or ip ranges).

<ol>
* Auto Ban by User Agents -  If IP exceeds x number of different user agents in X number of seconds then Ban
* Auto Ban by Hits -  If IP exceeds X number of hits in X number of seconds then Ban
* Auto Ban by Referrer -  Ban IP that matches referrer in X number of seconds and for X number of times
* Auto Ban by URL - Ban IP that request matching URL hit X times in X number of seconds
</ol>

### How To Ban Bots

As of Ban plugin v2.0.1 it was decided to split up the attempt to determine an exact match from a regular expression for BAN items. The main reason for this deals with remote IP addresses. With the introduction of the BAN API which is used by the Forum and GUS plugins a lot more single IP addresses where being added to the BAN plugin. This reason along with the auto BAN functionality the BAN list was become very long. This means loading the BAN list and stepping through each item to do a preg_match became very time consuming and resulted in much longer page loads. With the spiting up of exact matches and regular expressions this means we can do a very quick search of the database for exact matches followed by checking each regular expression BAN item.

Why would you want to ban bots from your website?  There are at least three good reasons.

* They eat up bandwidth and resources -- I have had bots come to my website and slurp every web page as fast as they can.  Numerous times I have recorded several hundred page accesses per minute by one ip or bot.
* You might not agree with what the bot is going to do with the information.  Three I ban are Cyveillance (http://www.cyveillance.com/), NameProtect (http://www.nameprotect.com/), and Turnitin (http://www.turnitin.com/static/index.html).  If you are not familiar with them check them out.
* Privacy concerns dictate that I do what I can to ban email harvesters from my site.

Here are two links out of several that can give more background on the bot issues: Mark Pilgrim's write up (http://diveintomark.org/archives/2003/02/26/how_to_block_spambots_ban_spybots_and_tell_unwanted_robots_to_go_to_hell) and Webmaster World (http://www.webmasterworld.com/forum13/687.htm).  The later requires free registration but has a very good discussion and a more extensive block list.  Both of these websites use apache rewrite rules to ban bots but the Ban plugin will work anywhere Geeklog will and does not require fiddling with apache.  Both the apache rewrite rules and the Ban Plugin use regular expression matches and are thus compatible.

Briefly how the Ban Plugin works is it checks every page access against a list from your database of banned bots/users.  You can identify the person/bot to be banned by 4 different criteria: REMOTE_ADDR (this is the IP of the user/bot), HTTP_REFERER (obvious), HTTP_USER_AGENT (the browser of Agent String), and SCRIPT_NAME (the page being accessed).  These Ban Types are exact match and are very quick to search on. In addition to exact match you can specify a range or regular expression which allows you to ban multiple visitors with just one entry. These Ban Types are based on the 4 Ban Types listed above. Here is the full list of Ban Types:

Exact Matches Ban Types (case insensitive):

* REMOTE_ADDR: Blocks a visitor from a specific IP address. Example: "192.168.1.123"
* HTTP_REFERER: Blocks a visitor that was referred by a specific URL
* HTTP_USER_AGENT: Blocks browsers that use a specific User Agent
* SCRIPT_NAME: Blocks a single page. Do not include domain. Example: "/article.php?story=test-story"

Ban Types that use Regular Expressions and other Range Checks:

* REMOTE_ADDR_RANGE: Easy to understand from and to IP ranges. Example: "63.148.99.224-63.148.99.255"
* REMOTE_ADDR_CIDR: Blocks a range of IPv4 addresses using CIDR notation. Does not support IPv6 addresses. Example: "192.168.100.14/24". For more information see Classless Inter-Domain Routing (https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
* REMOTE_ADDR_REGEX: Ban multiple IP range using a regular expression. Example: "^63\.148\.99\.2(2[4-9]|[3-4][0-9]|5[0-5])$"
* HTTP_REFERER_REGEX:  Ban visitors by HTTP_REFERER using a regular expression. Entering "example\.com" would ban any visitor that was referred from that domain (doesn't matter what page it was) 
* HTTP_USER_AGENT_REGEX: Ban visitors by HTTP_USER_AGENT using a regular expression. For example if your ban was "badbot" any user agent that contained that text would be banned
* SCRIPT_NAME_REGEX: Blocks multiple pages. For example "article.php" would block any user from accessing any article.

Lets give a few more examples of how to use these.

Say I want to ban the bot/user from IP 192.168.0.2.  I would go to the Ban Admin page and enter REMOTE_ADDR_REGEX into the 'Ban Type' box and 192.168.0.2 into the 'Data' box.  But wait, this is a regular expression match so to keep from matching 192.168.0.20 etc I would need to enter 192.168.0.2$ to tell it that it was the end of the  IP string.  You might also want to add ^ to the beginning to signify that it starts at the beginning.  If you don't know regular expressions and you want to match the complete string put ^ at the start and $ at the end.  If you don't know regular expressions, now would be a good time to start learning. Here is a good cheat sheet (http://www.regexlib.com/CheatSheet.htm) to jog your memory.

Here is a more complicated example I use to ban the Cyveillance bot:

I set the Ban Type again to REMOTE_ADDR_REGEX and the data to ^63\.148\.99\.2(2[4-9]|[3-4][0-9]|5[0-5])$ . This tells it to match the range 63.148.99.224 - 63.148.99.255 inclusive.

Another Example: say I want to ban the bot that has the HTTP_REFERER variable always set to a page that contains iaea.org.  I would set Ban Type to HTTP_REFERER_REGEX and Date to iaea\.org.  I had to escape the period because that is a special character in regexs.

Another Example: I want to ban various email harvesters.  I set Ban Type to HTTP_USER_AGENT_REGEX and Data to e?mail.?(collector|magnet|reaper|siphon|sweeper|harvest|collect|wolf). To say it simply it would match: email.collector, mail magnet, emailreaper, mailsiphon, etc, etc.

One final usage example:  I want to turn off a page on my website.  I would put SCRIPT_NAME in the Ban Type and the name of the page in Data.

Config.php in the plugins/ban/ directory has a number of variables that control options. Two of them include:

* $_BAN_log Controls whether bans are logged to <geeklog>/logs/ban.log (same directory where your Geeklog error and access logs are.)
* $_BAN_page Setting this to a page will display that page when the bot/person is banned.  Setting it to '' will just display a blank page.  Caution do not set this to one of our Geeklog Pages or a refresh look will occur.  There is a sample 404.html page in the package you can use.


-----
* 2.0.1 - Release Date: March 30, 2017
