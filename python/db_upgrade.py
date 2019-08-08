import sys
import MySQLdb
import MySQLdb.cursors
import config as c

CURRENT_VERSION = '1.80'

db = MySQLdb.connect(host=c.DBHOST, user=c.DBUSER, passwd=c.DBPASS, db=c.DBNAME, cursorclass=MySQLdb.cursors.DictCursor)
cur = db.cursor()

def main():
    version = get_db_version()

    if version == CURRENT_VERSION:
        sys.exit("\nDatabase is already the current version: %s\n" % (CURRENT_VERSION))

    while version != CURRENT_VERSION:
        print "\nUpgrading database "+version+" to next version.\n"
        version = upgrade_db(version)
    sys.exit('\nDatabase is now at the current version: %s' % (CURRENT_VERSION))


def upgrade_db(version):

    if version == '1.72':
        #########################################
        ## 2019 Season - minor changes         ##
        #########################################

        # Add index to watch table
        query = ('ALTER TABLE `draft_watch` ADD INDEX (`player_id`)')
        cur.execute(query)
        query = ('ALTER TABLE `draft_watch` ADD INDEX (`team_id`)')
        cur.execute(query)


        # Add rank_order to draft_player_rank, default value 999
        if not column_exists("rank_order", "draft_player_rank"):
            query = 'ALTER TABLE `draft_player_rank` ADD `rank_order` INT(11) unsigned DEFAULT 999'
            cur.execute(query)
            db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.80")
        cur.execute(query)
        db.commit()

        return get_db_version() 

    if version == '1.71':
        if not column_exists("last_seen", "player"):
            query = 'ALTER TABLE `player` ADD `last_seen` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"'
            cur.execute(query)            

            query = 'update player set last_seen = NOW()'
            cur.execute(query)
            db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.72")
        cur.execute(query)
        db.commit()

        return get_db_version() 
    
    if version == '1.70':

        cat170 = [
            ('kicking_xptot','Total XPs'),
            ('kicking_fgyds','FG Yds'),
            ('punting_pts','Num Punts'),
            ('kicking_totpfg','FG Pts'),
            ('puntret_lngtd','Lng PR Td'),
            ('kickret_lngtd','Lng KR TD'),

        ]

        for cat in cat170:
            query = ('update nfl_scoring_cat set short_text = "%s" where text_id = "%s"') % (cat[1],cat[0])
            cur.execute(query)
            db.commit()


        query = 'update site_settings set db_version = "%s"' % ("1.71")
        cur.execute(query)
        db.commit()

        return get_db_version() 

    if version == '1.60':
        if not column_exists("short_name", "team"):
            query = 'ALTER TABLE `team` ADD `team_abbreviation` varchar(5) DEFAULT ""'
            cur.execute(query)
            db.commit()

            query = 'update team set team_abbreviation = left(team_name,5)'
            cur.execute(query)
            db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.70")
        cur.execute(query)
        db.commit()

        return get_db_version() 



    if version == "1.51":
        # **** user_login_attempts **** #
        if not table_exists('bench'):
            query = ('CREATE TABLE `bench` ('
                    +'`id` int(11) unsigned NOT NULL AUTO_INCREMENT,'
                    +'`league_id` int(11) unsigned NOT NULL,'
                    +'`team_id` int(11) unsigned NOT NULL,'
                    +'`player_id` int(11) NOT NULL,'
                    +'`week` int(11) NOT NULL,'
                    +'`year` int(11) NOT NULL,'
                    +'`nfl_week_type_id` int(11) NOT NULL,'
                    +'PRIMARY KEY (`id`))')
            cur.execute(query)
            db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.60")
        cur.execute(query)
        db.commit()

        return get_db_version() 


    if version == "1.5":

        query = 'ALTER TABLE ci_sessions CHANGE id id varchar(128) NOT NULL'
        cur.execute(query)
        db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.51")
        cur.execute(query)
        db.commit()

        return get_db_version() 

    if version == "1.4":
        #########################################
        ## Switch to ion auth bunch of changes ##
        #########################################

        # **** user_accounts **** #
        # Rename and create columns in user_accounts table
        if column_exists("uacc_id", "user_accounts"):
            query = 'ALTER TABLE `user_accounts` CHANGE `uacc_id` `id` int(11) unsigned NOT NULL AUTO_INCREMENT'
            cur.execute(query)
        if column_exists("uacc_email", "user_accounts"):
            query = 'ALTER TABLE `user_accounts` CHANGE `uacc_email` `email` varchar(254) NOT NULL'
            cur.execute(query)
        if column_exists("uacc_username", "user_accounts"):
            query = 'ALTER TABLE `user_accounts` CHANGE `uacc_username` `username` varchar(100) NULL'
            cur.execute(query)
        if column_exists("uacc_password", "user_accounts"):
            query = 'ALTER TABLE `user_accounts` CHANGE `uacc_password` `password` varchar(255) NOT NULL'
            cur.execute(query)
        if column_exists("uacc_salt", "user_accounts"):
            query = 'ALTER TABLE `user_accounts` CHANGE `uacc_salt` `salt` varchar(255) DEFAULT NULL'
            cur.execute(query)
        if column_exists("uacc_ip_address", "user_accounts"):
            query = 'ALTER TABLE `user_accounts` CHANGE `uacc_ip_address` `ip_address` varchar(45) NOT NULL'
            cur.execute(query)
        if column_exists("uacc_active", "user_accounts"):
            query = 'ALTER TABLE `user_accounts` CHANGE `uacc_active` `active` tinyint(1) unsigned DEFAULT NULL'
            cur.execute(query)

        query = ('ALTER TABLE `user_accounts` '
                    +'ADD `activation_code` varchar(40) DEFAULT NULL,'
                    +'ADD `forgotten_password_code` varchar(40) DEFAULT NULL,'
                    +'ADD `forgotten_password_time` int(11) unsigned DEFAULT NULL,'
                    +'ADD `remember_code` varchar(40) DEFAULT NULL,'
                    +'ADD `created_on` int(11) unsigned NOT NULL,'
                    +'ADD `last_login` int(11) unsigned DEFAULT NULL,'
                    +'ADD `first_name` varchar(50) DEFAULT NULL,'
                    +'ADD `last_name` varchar(50) DEFAULT NULL,'
                    +'ADD `company` varchar(100) DEFAULT NULL,'
                    +'ADD `phone` varchar(20) DEFAULT NULL')
        cur.execute(query)
        db.commit()

        # Import data from from other places to new user_accounts fields
        query = ('update user_accounts join owner on owner.user_accounts_id = user_accounts.id '
            +'set user_accounts.first_name=owner.first_name, user_accounts.last_name = owner.last_name,'
            +'user_accounts.phone = owner.phone_number, user_accounts.created_on = UNIX_TIMESTAMP(user_accounts.uacc_date_added),' 
            +'user_accounts.last_login = UNIX_TIMESTAMP(user_accounts.uacc_date_last_login)')
        cur.execute(query)
        db.commit()


        # **** user_groups **** #
        # Rename columns in user_groups table
        if column_exists("ugrp_id", "user_groups"):
            query = 'ALTER TABLE `user_groups` CHANGE `ugrp_id` `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT'
            cur.execute(query)
        if column_exists("ugrp_name", "user_groups"):
            query = 'ALTER TABLE `user_groups` CHANGE `ugrp_name` `name` varchar(20) NOT NULL'
            cur.execute(query)
        if column_exists("ugrp_desc", "user_groups"):
            query = 'ALTER TABLE `user_groups` CHANGE `ugrp_desc` `description` varchar(100) NOT NULL'
            cur.execute(query)
        db.commit()

        # **** user_memberships **** #
        # Create table to store user/group relationship and import existing data from user_accounts
        if not table_exists('user_memberships'):
            query = ('CREATE TABLE `user_memberships` ('
                    +'`id` int(11) unsigned NOT NULL AUTO_INCREMENT,'
                    +'`user_id` int(11) unsigned NOT NULL,'
                    +'`group_id` mediumint(8) unsigned NOT NULL,'
                    +'PRIMARY KEY (`id`),'
                    +'KEY `fk_users_groups_users1_idx` (`user_id`),'
                    +'KEY `fk_users_groups_groups1_idx` (`group_id`),'
                    +'CONSTRAINT `uc_users_groups` UNIQUE (`user_id`, `group_id`))')
            cur.execute(query)
            db.commit()

            # Import group memberships from user_accounts
            query = 'select id, uacc_group_fk from user_accounts'
            cur.execute(query)
            for row in cur.fetchall():
                query = 'insert into user_memberships (user_id, group_id) values (%s,%s)' % (str(row['id']), str(row['uacc_group_fk']))
                cur.execute(query)
                # Now that you can be in multiple groups, admins are also in the regular users group
                if row['uacc_group_fk'] == 1:
                    query = 'insert into user_memberships (user_id, group_id) values (%s,%s)' % (str(row['id']), "2")
            db.commit()

        # **** user_login_attempts **** #
        if not table_exists('user_login_attempts'):
            query = ('CREATE TABLE `user_login_attempts` ('
                    +'`id` int(11) unsigned NOT NULL AUTO_INCREMENT,'
                    +'`ip_address` varchar(45) NOT NULL,'
                    +'`login` varchar(100) NOT NULL,'
                    +'`time` int(11) unsigned DEFAULT NULL,'
                    +'PRIMARY KEY (`id`))')
            cur.execute(query)
            db.commit()

        # Delete all extra fields from flexi_auth.
        to_delete = ('uacc_group_fk','uacc_activation_token','uacc_forgotten_password_token',
                    'uacc_forgotten_password_expire','uacc_update_email_token','uacc_update_email',
                    'uacc_suspend','uacc_fail_login_attempts','uacc_fail_login_ip_address',
                    'uacc_date_fail_login_ban','uacc_date_last_login','uacc_date_added')
        for d in to_delete:
            drop_column(d,'user_accounts')
        drop_column('ugrp_admin','user_groups')

        # Delete all extra tables from flexi_auth
        to_delete = ('user_login_sessions','user_privileges','user_privilege_groups','user_privilege_users')
        for d in to_delete:
            drop_table(d)


        # Chat_read should be a team setting, not an owner setting in case they are in more than one league
        if not column_exists("chat_read", "team"):
            query = 'ALTER TABLE `team` ADD `chat_read` INT(11) unsigned DEFAULT 0'
            cur.execute(query)
            db.commit()

        # Copy chat_read values from owner_setting to team tables
        query = ('update team join owner_setting on owner_setting.owner_id = team.owner_id set team.chat_read = owner_setting.chat_read')
        cur.execute(query)
        db.commit()

        drop_column('chat_read','owner_setting')

        query = 'update site_settings set db_version = "%s"' % ("1.5")
        cur.execute(query)
        db.commit()

        #########################################
        ## Moved some menu items around 
        ## If these fail, not a big deal       ##
        #########################################

        try:
            query = 'update menu_item set hide = 1 where text = "Money List" and url = "season/moneylist"'
            cur.execute(query)

            query = 'update menu_item set hide = 1 where text = "Standings" and url = "season/standings"'
            cur.execute(query)

            query = 'update menu_item set menu_bar_id = 2, `order` = 0 where text = "News" and url = "league/news"'
            cur.execute(query)

            query = 'update menu_item set `order` = 1 where text = "Weekly Scores" and url = "season/scores"'
            cur.execute(query)

            query = 'update menu_item set `order` = 2 where text = "Schedule" and url = "season/schedule"'
            cur.execute(query)

            db.commit()
        except:
            pass


        return get_db_version() 

    if version == "1.32":
        # I never did update SD to be LAC for the team positions
        cols = ['cbs_id','first_name','short_name','player_id']
        query = "select id from player where player_id like '%SD%'"
        cur.execute(query)
        for row in cur.fetchall():
            for col in cols:
                query = 'update player set '+col+' = REPLACE('+col+',"SD","LAC") where id = '+str(row['id'])
                cur.execute(query)

        # Also delete any rows where player_id is NULL
        query = 'delete from nfl_statistic where player_id is NULL'
        cur.execute(query)
        deleted = cur.rowcount
        db.commit()
        if deleted > 0:
            print "Purged %s nfl_statistic rows with NULL player_ids" % (str(deleted))

        query = 'update site_settings set db_version = "%s"' % ("1.4")
        cur.execute(query)
        db.commit()
        return get_db_version() 

    if version == "1.31":
        # Add player_id index for player_injury, player_rank, player_researchinfo, player_news
        query = ('ALTER TABLE `player_injury` ADD INDEX (`player_id`)')
        cur.execute(query)
        query = ('ALTER TABLE `player_rank` ADD INDEX (`player_id`)')
        cur.execute(query)
        query = ('ALTER TABLE `player_researchinfo` ADD INDEX (`player_id`)')
        cur.execute(query)
        query = ('ALTER TABLE `player_news` ADD INDEX (`player_id`)')
        cur.execute(query)

        # Add week column to player_injury table
        if not column_exists("week", "player_injury"):
            query = 'ALTER TABLE `player_injury` ADD `week` INT'
            cur.execute(query)

        query = 'update site_settings set db_version = "%s"' % ("1.32")
        cur.execute(query)
        db.commit()
        return get_db_version() 

    if version == "1.3":
        # Forgot to populate player_injury_type table
        add = [{'text_id':'IA',
                'short_text':'Inactive',
                'description':'Players are officially inactive for the current game and will not play'},
                {'text_id':'O',
                'short_text':'Out',
                'description':'Not scheduled to play'},
                {'text_id':'D',
                'short_text':'Doubtful',
                'description':'Players have approximately a 25% chance of playing'},
                {'text_id':'Q',
                'short_text':'Questionable',
                'description':'Players have approximately a 50% chance of playing'},
                {'text_id':'P',
                'short_text':'Probable',
                'description':'Players are very likely to start in the upcoming week'}]
        for a in add:
            query = 'select id from player_injury_type where short_text = "%s"' % (a['short_text'])
            cur.execute(query)
            if cur.rowcount > 0: continue
            query = (('insert into player_injury_type (text_id,short_text,description) values("%s","%s","%s")')
                    %(a['text_id'],a['short_text'],a['description']))
            cur.execute(query)
            db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.31")
        cur.execute(query)
        db.commit()
        return get_db_version() 

    if version == "1.21":
        # player_rank table
        if not table_exists('player_rank'):
            query = ('CREATE TABLE `player_rank` (id INT NOT NULL AUTO_INCREMENT KEY,'
                        +'player_id INT(11),'
                        +'gsisPlayerId VARCHAR(12),'
                        +'rank FLOAT,'
                        +'rank_pos VARCHAR(5),'
                        +'lastUpdated DATETIME NOT NULL)')
            cur.execute(query)


        # player_researchinfo table
        if not table_exists('player_researchinfo'):
            query = ('CREATE TABLE `player_researchinfo` (id INT NOT NULL AUTO_INCREMENT KEY,'
                        +'player_id INT(11),'
                        +'gsisPlayerId VARCHAR(12),'
                        +'percentOwned FLOAT,'
                        +'percentOwnedChange FLOAT,'
                        +'percentStarted FLOAT,'
                        +'percentStartedChange FLOAT,'
                        +'depthChartOrder INT(11),'
                        +'numAdds INT(11),'
                        +'numDrops INT(11),'
                        +'lastUpdated DATETIME NOT NULL)')
            cur.execute(query)

        # player.esbid varchar(10)
        if not column_exists("esbid", "player"):
            query = 'ALTER TABLE `player` ADD `esbid` VARCHAR(10)'
            cur.execute(query)

        # player_injury injury, practicestatus
        if not column_exists("injury", "player_injury"):
            query = 'ALTER TABLE `player_injury` ADD `injury` VARCHAR(50)'
            cur.execute(query) 

        if not column_exists("practiceStatus", "player_injury"):
            query = 'ALTER TABLE `player_injury` ADD `practiceStatus` VARCHAR(100)'
            cur.execute(query)

        query = 'update site_settings set db_version = "%s"' % ("1.3")
        cur.execute(query)
        db.commit()
        return get_db_version() 

    if version == "1.2":
        # Really NFL? You still use LA, fine, changing back
        query = 'update nfl_team set club_id = "LA" where team_name = "Los Angeles Rams"'
        cur.execute(query)
        db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.21")
        cur.execute(query)
        db.commit()
        return get_db_version() 

    if version == "1.1":
        # Add priority_used column
        if not column_exists("priority_used", "waiver_wire_log"):
            query = 'ALTER TABLE `waiver_wire_log` ADD `priority_used` BOOLEAN DEFAULT 0'
            cur.execute(query)

        # Add transaction_week column
        if not column_exists("transaction_week", "waiver_wire_log"):
            query = 'ALTER TABLE `waiver_wire_log` ADD `transaction_week` INT(11)'
            cur.execute(query)

        # LA Rams are now abbreviated LAR
        query = 'update nfl_team set club_id = "LAR" where team_name = "Los Angeles Rams"'
        cur.execute(query)
        db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.2")
        cur.execute(query)
        db.commit()
        return get_db_version()       

    if version == "1.0":
        # waiver_wire_disable_days should be a varchar

        query = "ALTER TABLE  `league_settings` CHANGE  `waiver_wire_disable_days`  `waiver_wire_disable_days` VARCHAR( 7 ) NOT NULL"
        cur.execute(query)
        db.commit()

        # update SD to LAC
        query = "update nfl_team set club_id = 'LAC', team_name = 'Los Angeles Chargers' where club_id = 'SD'"
        cur.execute(query)
        db.commit()

        query = 'update site_settings set db_version = "%s"' % ("1.1")
        cur.execute(query)
        db.commit()
        return get_db_version()

    if version == "0.7":
        # Add player_injury table
        if not table_exists('player_injury'):
            query = ('CREATE TABLE `player_injury` (id INT NOT NULL AUTO_INCREMENT KEY,'
                        +'player_id INT(11),'
                        +'player_injury_type_id INT(11),'
                        +'description VARCHAR(200),'
                        +'last_updated DATETIME NOT NULL)')
            cur.execute(query)

        # Add player_injury_type table
        if not table_exists('player_injury_type'):
            query = ('CREATE TABLE `player_injury_type` (id INT NOT NULL AUTO_INCREMENT KEY,'
                        +'text_id VARCHAR(2) NOT NULL,'
                        +'short_text VARCHAR(20) NOT NULL,'
                        +'description VARCHAR(100) NOT NULL)')
            cur.execute(query)

        # Add waiver_wire_disable_gt
        if not column_exists("waiver_wire_disable_gt", "league_settings"):
	            query = 'ALTER TABLE `league_settings` ADD `waiver_wire_disable_gt` BOOLEAN DEFAULT 0'
        	    cur.execute(query)


        # Add waiver_wire_disable_days
        if not column_exists("waiver_wire_disable_days", "league_settings"):
	            query = 'ALTER TABLE `league_settings` ADD `waiver_wire_disable_days` VARCHAR(7) NOT NULL'
        	    cur.execute(query)
        
        db.commit()
        query = 'update site_settings set db_version = "%s"' % ("1.0")
        cur.execute(query)
        db.commit()
        return get_db_version()


    if version == "0.6":
        if not table_exists('draft_player_rank'):
            query = ('CREATE TABLE `draft_player_rank` (id INT NOT NULL AUTO_INCREMENT KEY,'
                            +'player_id INT(11),'
                            +'gsisPlayerId VARCHAR(12) NOT NULL,'
                            +'rank FLOAT DEFAULT 0,'
                            +'aav FLOAT DEFAULT 0,'
                            +'last_updated DATETIME NOT NULL)')
            cur.execute(query)
            db.commit()

            query = ('ALTER TABLE `draft_player_rank` ADD INDEX (`player_id`)')
            cur.execute(query)

            query = ('ALTER TABLE `draft_watch` ADD INDEX (`player_id`)')
            cur.execute(query)

            query = ('ALTER TABLE `draft_watch` ADD INDEX (`team_id`)')
            cur.execute(query)

        if not column_exists("use_draft_ranks", "league_settings"):
            query = 'ALTER TABLE `league_settings` ADD `use_draft_ranks` BOOLEAN DEFAULT 0'
            cur.execute(query)

        query = 'update site_settings set db_version = "%s"' % ("0.7")
        cur.execute(query)
        db.commit()
        return get_db_version()


    if version == "0.5":
        if not table_exists('player_news'):
            query = ('CREATE TABLE `player_news` (id INT NOT NULL AUTO_INCREMENT KEY,'
                                        +'news_id INT(11) DEFAULT 0,'
                                        +'gsisPlayerId VARCHAR(12) NOT NULL,'
                                        +'news_date DATETIME NOT NULL,'
                                        +'source VARCHAR(25) NOT NULL,'
                                        +'headline VARCHAR(100) NOT NULL,'
                                        +'body TEXT NOT NULL,'
                                        +'analysis TEXT NOT NULL,'
                                        +'player_id INT(11))')
            cur.execute(query)


        query = 'update site_settings set db_version = "%s"' % ("0.6")
        cur.execute(query)
        db.commit()
        return get_db_version()

    if version == "0.4":
        query = 'RENAME TABLE schedule_title TO title_def'
        cur.execute(query)

        #Rename column schedule_title_id to title_def_id in schedule table
        query = 'ALTER TABLE schedule CHANGE COLUMN schedule_title_id title_def_id int(11) DEFAULT 0'
        cur.execute(query)

        #Add title table
        if not table_exists('title'):
            query = ('CREATE TABLE `title` (id INT NOT NULL AUTO_INCREMENT KEY,'
                                        +'team_id INT(11) DEFAULT 0,'
                                        +'title_def_id INT(11) DEFAULT 0,'
                                        +'year INT(11) DEFAULT 0,'
                                        +'schedule_id INT(11) DEFAULT 0,'
                                        +'league_id INT(11) DEFAULT 0)')

            cur.execute(query)
            
        query = 'update site_settings set db_version = "%s"' % ("0.5")
        cur.execute(query)
        db.commit()
        return get_db_version()



    if version == "0.3":
        # These were somehow dropped from 0.2 to 0.3
        if column_exists("desription", "money_list_type"):
            query = 'ALTER TABLE `money_list_type` CHANGE COLUMN `desription` `description` VARCHAR(500) NOT NULL'
            cur.execute(query)
        if not column_exists("title_game", "schedule_game_type"):
            query = 'ALTER TABLE `schedule_game_type` ADD `title_game` BOOLEAN DEFAULT 0'
            cur.execute(query)
        if not column_exists("league_id","schedule_template"):
            query = 'ALTER TABLE `schedule_template` ADD `league_id` INT(11) DEFAULT 0'
            cur.execute(query)

        # Create schedule_title table & add schedule_title_id to schedule table
        if not table_exists("schedule_title"):
            query = ('CREATE TABLE `schedule_title` (id INT NOT NULL AUTO_INCREMENT KEY,'
                                                    +'text VARCHAR(50) NOT NULL,'
                                                    +'display_order INT(11) DEFAULT 0,'
                                                    +'league_id INT(11) DEFAULT 0)')

            cur.execute(query)

        if not column_exists("schedule_title_id", "schedule"):
            query = 'ALTER TABLE `schedule` ADD `schedule_title_id` INT(11) DEFAULT 0'
            cur.execute(query)

        # New menu item in admin section
        query = ('insert into menu_item (menu_bar_id, text, url, `order`, hide, show_noleague) VALUES'
                + '(5, "Past Seasons","admin/past_seasons",7,0,0)')

        cur.execute(query)

        query = 'update site_settings set db_version = "%s"' % ("0.4")
        cur.execute(query)
        db.commit()
        return get_db_version()
        
    if version == "0.2":
        if column_exists('sse_chat','owner_setting') == False:
            query = 'ALTER TABLE owner_setting ADD sse_chat BOOLEAN DEFAULT 0'
            cur.execute(query)
        if column_exists('sse_draft','owner_setting')  == False:
            query = 'ALTER TABLE owner_setting ADD sse_draft BOOLEAN DEFAULT 0'
            cur.execute(query)
        if column_exists('sse_live_scores','owner_setting')  == False:
            query = 'ALTER TABLE owner_setting ADD sse_live_scores BOOLEAN DEFAULT 0'
            cur.execute(query)
        if column_exists('update_key','nfl_live_player')  == False:
            query = 'ALTER TABLE nfl_live_player ADD update_key INT DEFAULT 0'
            cur.execute(query)
        if column_exists('update_key','nfl_live_game')  == False:
            query = 'ALTER TABLE nfl_live_game ADD update_key INT DEFAULT 0'
            cur.execute(query)

        # This was extra a loooong time ago.
        if column_exists('week_type_id','fantasy_statistic_week'):
            query = 'ALTER TABLE fantasy_statistic_week DROP week_type_id'
            cur.execute(query)

        query = 'update site_settings set db_version = "%s"' % ("0.3")
        cur.execute(query)
        db.commit()
        return get_db_version()
        


    if version == "0.1":
        # scoring_def changes
        query = 'ALTER TABLE scoring_def ADD range_start INT DEFAULT 0'
        cur.execute(query)
        query = 'ALTER TABLE scoring_def ADD range_end INT DEFAULT 0'
        cur.execute(query)
        query = 'ALTER TABLE scoring_def ADD is_range BOOLEAN DEFAULT 0'
        cur.execute(query)

        query = 'ALTER TABLE site_settings ADD db_version VARCHAR(5)'
        cur.execute(query)
        query = 'update site_settings set db_version = "%s"' % ("0.2")
        cur.execute(query)
        db.commit()
        return get_db_version()
        # site_settings db_version
        # alter table fantasy_statistic_week alter week_type_id set DEFAULT 0;


    sys.exit('\nUnknown database version.\n')


def get_db_version():
    query = 'show columns from site_settings like "db_version"'
    cur.execute(query)
    row = cur.fetchone()
    if row is None:
        return "0.1"
    else:
        query = "select db_version from site_settings"
        cur.execute(query)
        row = cur.fetchone()
        return row['db_version']
    sys.exit('\nUnknown database version stored in the db.\n')

def column_exists(column, table):
    query = 'show columns from '+table+' like "'+column+'"'
    cur.execute(query)
    row = cur.fetchone()
    if row is None:
        return False
    else:
        return True

def table_exists(table):
    query = 'SHOW TABLES LIKE "%s"' % (table)
    cur.execute(query)
    row = cur.fetchone()
    if row is None:
        return False
    else:
        return True

def drop_column(column, table):
    if column_exists(column, table):
        query = 'ALTER TABLE `'+table+'` DROP COLUMN `'+column+'`'
        cur.execute(query)   

def drop_table(table):
    if table_exists(table):
        query = 'DROP TABLE '+table
        cur.execute(query)

main()
