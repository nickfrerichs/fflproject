import sys
import MySQLdb
import MySQLdb.cursors
import config as c

CURRENT_VERSION = '0.2'

db = MySQLdb.connect(host=c.DBHOST, user=c.DBUSER, passwd=c.DBPASS, db=c.DBNAME, cursorclass=MySQLdb.cursors.DictCursor)
cur = db.cursor()

def main():
    version = get_db_version()
    if version == CURRENT_VERSION:
        sys.exit("\nDatabase is already the current version: %s\n" % (CURRENT_VERSION))

    while version != CURRENT_VERSION:
        print "\nUpgrading database "+version+" to next version.\n"
        version = upgrade_db(version)


def upgrade_db(version):
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


main()
