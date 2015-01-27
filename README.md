#embarc-utils

Utilities for Embarc Information Technology

##Modules
- [x] Alerts & Reminders
- [ ] Device Simulator
- [x] Courier Charge Calculator
- [ ] Employee Attendance Management
- [x] Inventory Management
- [ ] Server Log Maintenance
- [x] Server Status Utility
- [ ] Settings
- [x] SMTP Checking Utility
- [x] Users

##Settings up Ruby on Ubuntu
This process has been tested on Ubuntu 12.04 LTS, but should work without any changes with future releases. However, it is recommended to visit the official [RVM](http://rvm.io/) website to check for any changes before starting the process.

1. Update sources using `sudo apt-get update`
2. Install curl for downloading RVM `sudo apt-get install curl`
3. Download and install the latest stable verison of RVM `\curl -L https://get.rvm.io | bash -s stable`
4. Load RVM settings using `source /etc/profile.d/rvm.sh`
5. Install RVM dependencies `rvm requirements`
6. Now we install the latest stable version of Ruby `rvm install ruby`. This should automatically install rubygems, in case it doesn't you can manually install rubygems using `rvm rubygems current`
7. Set the currently downloaded version of Ruby as default `rvm use ruby --default`
8. To use scheduler as a background process you need to install [Daemons](http://daemons.rubyforge.org/) `gem install daemons`
9. To use .gpx files with Device Simulator install [Nokogiri](http://nokogiri.org/) `gem install nokogiri`

##Starting Alerts & Reminders
To start alerts and reminders, so that mails are sent automatically to the scheduled recipients, you need to run a ruby script `ruby ruby/schedule.rb`, which will automatically convert itself to process running in background.

##Backup the database
1. Change your working directory to setup, using `cd setup`
2. Run the setup file `./setup -b`
3. A backup file will be created in the setup directory itself, named embarcUtilities.sql

##Restore the database
1. Change your working directory to setup, using `cd setup`
2. Make sure the backup file is present in this folder, named as embarcUtilities.sql
3. Run the setup file `./setup -r`
4. Data from the backup file will be transferred to the appropriate database

##TODO
1. Update courier settings to use preferences table
2. Build GUI for Device Simulator
3. Add domain and port options in Device Simulator
