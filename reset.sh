#!/bin/bash

# reset debug environment
# 

# stop bots

# change dir
cd /home/manazhao/git/ProductTrading 
perl kill_robots.pl

# reset db
mysql -h localhost -u tim211 -ptim211simple ProductTrading < reset_db.sql

# re-launch robots
perl launch.robots.pl

