import sys
import MySQLdb
import MySQLdb.cursors
import config as c

CURRENT_VERSION = '0.3'

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

    if version == "0.2":
        query = 'ALTER TABLE owner_setting ADD sse_chat BOOLEAN DEFAULT 0'
        cur.execute(query)
        query = 'ALTER TABLE owner_setting ADD sse_draft BOOLEAN DEFAULT 0'
        cur.execute(query)
        query = 'ALTER TABLE owner_setting ADD sse_live_scores BOOLEAN DEFAULT 0'
        cur.execute(query)
        query = 'ALTER TABLE nfl_live_player ADD update_key INT DEFAULT 0'
        cur.execute(query)
        query = 'ALTER TABLE nfl_live_game ADD update_key INT DEFAULT 0'
        cur.execute(query)

        # This was extra a loooong time ago.
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



main()
