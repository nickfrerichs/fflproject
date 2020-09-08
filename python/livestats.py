import sys, os
import MySQLdb
import MySQLdb.cursors
import collections
import json
import argparse
import stat_functions as f
import math
import time
import datetime
import subprocess
import shieldquery
import queries as q
import config as c
from pprint import pprint

db = MySQLdb.connect(host=c.DBHOST, user=c.DBUSER, passwd=c.DBPASS, db=c.DBNAME, cursorclass=MySQLdb.cursors.DictCursor)
cur = db.cursor()
cur.execute("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));")

query = 'select current_timestamp'
cur.execute(query)
sql_now = cur.fetchone()['current_timestamp']
unix_timestamp = int(time.mktime(sql_now.timetuple()))

nfl_team_id_lookup = dict()
nfl_club_id_lookup = dict()

try:
    proxies = c.PROXIES
except:
    proxies = {}

try:
    headers = c.HTTP_HEADERS
except:
    headers = None

api = shieldquery.ShieldAPI(token_path=c.API_TOKEN_PATH, proxies=proxies, headers=headers)

def main():
    global nfl_team_id_lookup, nfl_club_id_lookup
    nfl_team_id_lookup = get_nfl_team_id_dict()
    nfl_club_id_lookup = get_nfl_club_id_dict()

    result = api.current_season_state()

    cur_year = result["data"]["viewer"]["league"]["current"]["week"]["seasonValue"]
    cur_week = result["data"]["viewer"]["league"]["current"]["week"]["weekValue"]
    cur_weektype = result["data"]["viewer"]["league"]["current"]["week"]["seasonType"]

    now = datetime.datetime.now()

    if args.year == "0": year = str(cur_year)
    else: year = args.year
    if args.week == "0": week = str(cur_week)
    else: week = args.week
    if args.weektype == "none": weektype = str(cur_weektype)
    else: weektype = args.weektype.upper()

    if weektype not in ("REG","PRE","POST"):
        print
        sys.exit("Invalid weektype: "+weektype)

    if args.hello:
        print
        print "Year: "+str(year)+", Week: "+str(week)+", Weektype: "+str(weektype)
        print
        sys.exit()
        
    update_games(year, week, weektype, args.all)


def update_games(year, week, weektype, update_all = False):
    global sql_now

    # ----------------------
    # update_games has two parts:
    # 1. update nfl_statistic for games currently in progress
    # 2. calculate fantasy_statistic based on data in nfl_statistic table
    #
    # Theoretically, you should never have to update nfl_statistic data after games are complete.
    # If you change any league scoring values, you may have to re-calculate the fantasy_statistic
    # values though.
    # ----------------------

    # Sometimes the url to the raw data is useful:
    # game_url = 'http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json'

    print "Udpating NFL Statistics (%s)" % (sql_now)
    update_nfl_statistics(year, week, weektype, update_all)

    # if livegamecount == 0:
    #     print "No games currently in progress."

    # print("--- %s seconds ---\n" % str(time.time()-start_time))


    # print "Calculating and storing fantasy values"
    # update_fantasy_statistics(year, week, weektype)

    # print("--- %s seconds ---\n" % str(time.time()-start_time))

    # if live_changes_made:
    #     query = 'update league_settings set live_scores_key = %s' % (str(unix_timestamp))
    #     cur.execute(query)
    #     db.commit()
    # else:
    #     print "\n -No changes to live game data-\n"

    print("--- %s seconds ---\n" % str(time.time()-start_time))


# I think I'm going to have to re-write all of this reading through all plays in each game
# Start with getting nfl stats saved, phase 2 is live game stats
# maybe observe how the NFL site pulls it off when browsing what stats a player has in a game
def update_nfl_statistics(year, week, weektype, update_all):
    query = "select shield_id, gameDetailId from nfl_schedule where year=%s and week=%s and gt='%s'" % (str(year), str(week), str(weektype))
    cur.execute(query)
    schedule = cur.fetchall()
    for s in schedule:
        game = api.player_game_stats(s["shield_id"])

        # Stats using playerGameStats are a 1:1 copy, no calculation needed
        for p_node in game["data"]["viewer"]["playerGameStats"]["edges"]:
            p = p_node["node"]
            shield_id = p['id'][3:18]
            for s in p['gameStats'].items():
                print s
            print shield_id
            break
        break

        # defensiveAssists
        # defensiveInterceptions
        # defensiveInterceptionsYards
        # defensiveForcedFumble
        # defensivePassesDefensed
        # defensiveSacks
        # defensiveSafeties
        # defensiveSoloTackles
        # defensiveTotalTackles
        # defensiveTacklesForALoss
        # touchdownsDefense
        # fumblesLost
        # fumblesTotal
        # kickReturns
        # kickReturnsLong
        # kickReturnsTouchdowns
        # kickReturnsYards
        # kickingFgAtt
        # kickingFgLong
        # kickingFgMade
        # kickingXkAtt
        # kickingXkMade
        # passingAttempts
        # passingCompletions
        # passingTouchdowns
        # passingYards
        # passingInterceptions
        # puntReturns
        # puntingAverageYards
        # puntingLong
        # puntingPunts
        # puntingPuntsInside20
        # receivingReceptions
        # receivingTarget
        # receivingTouchdowns
        # receivingYards
        # rushingAttempts
        # rushingAverageYards
        # rushingTouchdowns
        # rushingYards
        # kickoffReturnsTouchdowns
        # kickoffReturnsYards
        # puntReturnsLong
        # opponentFumbleRecovery
        # totalPointsScored
        # kickReturnsAverageYards
        # puntReturnsAverageYards
        # puntReturnsTouchdowns

    #api.player_game_stats(game_id)
    #api.game_detail(gameDetailId)


# Used to look up database IDs for NFL teams
def get_nfl_team_id_dict():
  cur.execute('select id, club_id, alt_club_ids from nfl_team')
  team_dict = collections.defaultdict(lambda: 0, {})
  for row in cur.fetchall():
      team_dict[row['club_id']] = row['id']
      if row['alt_club_ids']:
          for alt in row['alt_club_ids'].split(','):
              alt = alt.strip()
              if alt != "":
                  team_dict[alt] = row['id']
  return team_dict

# Look up club_id from database ids
def get_nfl_club_id_dict():
    cur.execute('select id, club_id from nfl_team')
    team_dict = dict()
    for row in cur.fetchall():
        team_dict[row['id']] = row['club_id']

    return team_dict

parser = argparse.ArgumentParser(description='Update Game Statistics using NFL Game')

parser.add_argument('-hello', action="store_true", default=False, help="Just tell me what the current Year, Week, and WeekType is!")
parser.add_argument('-year', action="store", default="0", required=False, help="Year")
parser.add_argument('-week', action="store", default="0", required=False, help="Week")
parser.add_argument('-weektype', action="store", default="none", required=False, help="Type: REG, POST, PRE")
parser.add_argument('-all', action="store_true", default=False, help="Update all games, not just live ones.")
parser.add_argument('-recalc_all', action="store_true", default=False, help="Recalculate all fantasy values, not just the ones with new NFL stat values.")

start_time = time.time()
args = parser.parse_args()
main()
