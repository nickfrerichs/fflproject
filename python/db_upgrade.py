import sys
import MySQLdb
import MySQLdb.cursors
import config as c

CURRENT_VERSION = '0.6'

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


main()
