# Geeklog Ban plugin - version 2.0.0

* Current Maintainers: [Geeklog Community Members](https://github.com/orgs/Geeklog-Plugins/people)
* Original Author: Tom Willett
* Release Date: November 21, 2015

**Requires minimum Geeklog version 2.1.1 and MySQL 4.1+**

* Features
* Changelog
* Installing
* Upgrading
* Configuration
* Profile Integration

## Summary of features

The Ban plugin allows you to ban people and bots from your website.  You can ban a bot/user by IP, Referer, User Agent or Script Name. Case insensitive regular expressions are used to give you great flexibility.  The php eregi function is used to do the matches.

Why would you want to ban bots from your website?  There are at least three good reasons.  

* They eat up bandwidth and resources -- I have had bots come to my website and slurp every web page as fast as they can.  Numerous times I have recorded several hundred page accesses per minute by one ip or bot.  
* You might not agree with what the bot is going to do with the information.  Three I ban are [Cyveillance] (http://www.cyveillance.com/), [NameProtect] (http://www.nameprotect.com/), and [Turnitin] (http://www.turnitin.com/static/index.html). If you are not familiar with them check them out.  
* Privacy concerns dictate that I do what I can to ban email harvesters from my site.  

All of these reasons and more are the reasons I ban certain bots.

Here are two links out of several that can give more background on the bot issues: 

[Mark Pilgrim's write up] (http://diveintomark.org/archives/2003/02/26/how_to_block_spambots_ban_spybots_and_tell_unwanted_robots_to_go_to_hell) and [Webmaster World] (http://www.webmasterworld.com/forum13/687.htm).  The later requires free registration but has a very good discussion and a more extensive block list. Both of these websites use apache rewrite rules to ban bots but the Ban plugin will work anywhere Geeklog will and does not require fiddling with apache.  Both the apache rewrite rules and the Ban Plugin use regular expression matches and are thus compatible.

Briefly how the Ban Plugin works is it checks every page access against a list from your database of banned bots/users.  You can identify the person/bot to be banned by 4 different criteria:  REMOTE_ADDR (this is the IP of the user/bot), HTTP_REFERER (obvious), HTTP_USER_AGENT (the browser of Agent String), and SCRIPT_NAME (the page being accessed).  Lets give some examples of how to use these.

Say I want to ban the bot/user from IP 192.168.0.2.  I would go to the Ban Admin page and enter REMOTE_ADDR into the 'Ban Type' box and 192.168.0.2 into the 'Data' box.  But wait, this is a regular expression match so to keep from matching 192.168.0.20 etc I would need to enter 192.168.0.2$ to tell it that it was the end of the  IP string.  You might also want to add ^ to the beginning to signify that it starts at the beginning.  If you don't know regular expressions and you want to match the complete string put ^ at the start and $ at the end.  If you don't know regular expressions, now would be a good time to start learning.  [Here is a good cheat sheet] (http://www.regexlib.com/CheatSheet.htm) to jog your memory. 

Here is a more complicated example I use to ban the Cyveillance bot:<br>I set the Ban Type again to REMOTE_ADDR and the data to ^63\.148\.99\.2(2[4-9]|[3-4][0-9]|5[0-5])$ .  This tells it to match the range 63.148.99.224 - 63.148.99.255 inclusive.

Another Example: say I want to ban the bot that has the HTTP_REFERER variable always set to a page that contains iaea.org.  I would set Ban Type to HTTP_REFERER and Date to iaea\.org.  I had to escape the period because that is a special character in regexs.

Another Example: I want to ban various email harvesters.  I set Ban Type to HTTP_USER_AGENT and Data to e?mail.?(collector|magnet|reaper|siphon|sweeper|harvest|collect|wolf). To say it simply it would match: email.collector, mail magnet, emailreaper, mailsiphon, etc, etc.

One final usage example:  I want to turn off a page on my website.  I would put SCRIPT_NAME in the Ban Type and the name of the page in Data. 

Here is the listing of my ban list.  Before you blindly make it your I would invite you to at least look at the two links I gave above. Your mileage may vary.

INSERT INTO gl_ban VALUES ('REMOTE_ADDR','^63\.148\.99\.2(2[4-9]|[3-4][0-9]|5[0-5])$');
INSERT INTO gl_ban VALUES ('REMOTE_ADDR','^12\.148\.196\.(12[8-9]|1[3-9][0-9]|2[0-4][0-9]|25[0-5])$');
INSERT INTO gl_ban VALUES ('REMOTE_ADDR','^12\.148\.209\.(19[2-9]|2[0-4][0-9]|25[0-5])$');
INSERT INTO gl_ban VALUES ('REMOTE_ADDR','^64\.148\.49\.6([6-9])$');
INSERT INTO gl_ban VALUES ('HTTP_REFERER','iaea\.org');
INSERT INTO gl_ban VALUES ('HTTP_USER_AGENT','cherry.?picker');
INSERT INTO gl_ban VALUES ('HTTP_USER_AGENT','compatible ; MSIE 6.0');
INSERT INTO gl_ban VALUES ('HTTP_USER_AGENT','e?mail.?(collector|magnet|reaper|siphon|sweeper|harvest|collect|wolf)');
INSERT INTO gl_ban VALUES ('HTTP_USER_AGENT','Indy Library');
INSERT INTO gl_ban VALUES ('HTTP_USER_AGENT','Microsoft URL Control');
INSERT INTO gl_ban VALUES ('HTTP_USER_AGENT','NPBot');
INSERT INTO gl_ban VALUES ('HTTP_USER_AGENT','TurnitinBot');

Config.php in the plugins/ban/ directory has two variables that control options. 

* $_BAN_log Controls whether bans are logged to <geeklog>/logs/ban.log (same directory where your Geeklog error and access logs are.)
* $_BAN_page Setting this to a page will display that page when the bot/person is banned.  Setting it to '' will just display a blank page.  Caution do not set this to one of our Geeklog Pages or a refresh look will occur.  There is a sample  404.html page in the package you can use.
