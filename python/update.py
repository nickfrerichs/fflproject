import sys
import MySQLdb
import MySQLdb.cursors
import nflgame
import nflgame.update_players
import nflgame.update_sched
import argparse
import collections
import stat_functions as f
import math
import time
import datetime
import urllib2
import xml.dom.minidom as xml
import subprocess
import pytz
from tzlocal import get_localzone
import urllib2
import urllib
import os
import player_photo
import config as c

db = MySQLdb.connect(host=c.DBHOST, user=c.DBUSER, passwd=c.DBPASS, db=c.DBNAME, cursorclass=MySQLdb.cursors.DictCursor)
cur = db.cursor()
query = 'select current_timestamp'
cur.execute(query)
sql_now = cur.fetchone()['current_timestamp']

def main():

  cur_year, cur_week = nflgame.live.current_year_and_week()
  cur_weektype = nflgame.live._cur_season_phase

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

  if(args.schedule): # Update schedule
    update_schedule(year, week, weektype)

  #if(args.g): # Update games
#    update_games(year, week, weektype, args.all)

  if(args.players): # Update players
    update_players(year, week, weektype)

  if(args.summary): # calculate statistic_week
    update_statistic_summaries(year, week, weektype)

  if(args.standings):
    update_standings(year, week, weektype)


def update_standings(year, week ,weektype):

    if week == 'all':
        weeks = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17']
    else:
        weeks = [week]

    # Get all leagues of current weektype
    query = ('select league.id from league join league_settings on league.id = league_settings.league_id where nfl_season = "%s"' % weektype)
    cur.execute(query)
    leagues = cur.fetchall()

    for l in leagues:
        leagueid = l['id']

        for week in weeks:
            # Get score totals for this week
            query = (('SELECT sum(fs.points) as points, team_id FROM fantasy_statistic as fs join starter as s on '+
                    's.player_id = fs.player_id and s.year = fs.year and s.week = fs.week where fs.league_id = %s and '+
                    'fs.nfl_week_type_id = (select id from nfl_week_type where text_id = "%s") and fs.year = %s and fs.week = %s '+
                    'and s.league_id = %s group by team_id, fs.year, fs.week') % (str(leagueid), weektype, str(year), str(week), str(leagueid)))

            scores = dict()
            cur.execute(query)
            rows = cur.fetchall()
            for row in rows:
                scores[row['team_id']] = row['points']

            # Get all match ups for this week
            query = (('select id, home_team_id, away_team_id from schedule where league_id = %s and '+
                    'week = %s and year = %s and nfl_week_type_id = (select id from nfl_week_type where text_id = "%s")') %
                    (str(leagueid),str(week),str(year),weektype))
            cur.execute(query)

            schedule = cur.fetchall()
            for one in schedule:

                sched_id = one['id']
                #find winner team_id and loser team_id
                winid = 0
                lossid = 0
                tie = 0
                homewin = 0
                homeloss = 0
                awaywin = 0
                awayloss = 0
                homeid = one['home_team_id']
                awayid = one['away_team_id']
                if scores.get(homeid) is None: homescore = 0
                else: homescore = int(scores[homeid])
                if scores.get(awayid) is None: awayscore = 0
                else: awayscore = int(scores[awayid])



                if homescore > awayscore:
                    winid = homeid
                    lossid = awayid
                    homewin = 1
                    awayloss = 1
                if awayscore > homescore:
                    winid = awayid
                    lossid = homeid
                    awaywin = 1
                    homeloss = 1
                if awayscore == homescore:
                    tie = 1

                # There was no opponent, don't award win/loss/tie
                if homeid == 0 or awayid == 0:
                    winid = 0
                    lossid = 0
                    homewin = 0
                    awaywin = 0
                    homeloss = 0
                    awayloss = 0
                    tie = 0


                query = 'select id from schedule_result where schedule_id = %s and team_id = %s' % (str(sched_id),str(homeid))

                cur.execute(query)
                if cur.rowcount == 1:
                    sched_result_id = cur.fetchone()['id']
                    query = (('update schedule_result set team_id=%s,opp_id=%s,team_score=%s,opp_score=%s,win=%s,loss=%s,tie=%s,year=%s,week=%s '+
                        'where id=%s') %(str(homeid),str(awayid),str(homescore),str(awayscore),str(homewin),str(homeloss),str(tie),str(year),str(week),str(sched_result_id)))
                else:
                    query = ('insert into schedule_result (schedule_id,team_id,opp_id,team_score,opp_score,win,loss,tie,year,week) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)' %
                        (str(sched_id),str(homeid),str(awayid),str(homescore),str(awayscore),str(homewin),str(homeloss),str(tie),str(year),str(week)))
                cur.execute(query)

                query = 'select id from schedule_result where schedule_id = %s and team_id = %s' % (str(one['id']),str(awayid))
                cur.execute(query)

                if cur.rowcount == 1:
                    sched_result_id = cur.fetchone()['id']
                    query = (('update schedule_result set team_id=%s,opp_id=%s,team_score=%s,opp_score=%s,win=%s,loss=%s,tie=%s,year=%s,week=%s '+
                        'where id=%s') %(str(awayid),str(homeid),str(awayscore),str(homescore),str(awaywin),str(awayloss),str(tie),str(year),str(week),str(sched_result_id)))
                else:
                    query = ('insert into schedule_result (schedule_id,team_id,opp_id,team_score,opp_score,win,loss,tie,year,week) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)' %
                        (str(sched_id),str(awayid),str(homeid),str(awayscore),str(homescore),str(awaywin),str(awayloss),str(tie),str(year),str(week)))
                cur.execute(query)

            db.commit()


def update_players(year, week, weektype):
    # Helper functions for update_players
    def get_team_dict():
        cur.execute('select id, club_id from nfl_team')
        team_dict = collections.defaultdict(lambda: 0, {})
        for row in cur.fetchall():
            team_dict[row['club_id']] = row['id']

        return team_dict

    def get_pos_dict():
        cur.execute('select id, text_id from nfl_position')
        pos_dict = collections.defaultdict(lambda: 0, {})
        for row in cur.fetchall():
            pos_dict[row['text_id']] = row['id']

        return pos_dict

    def get_photo(player):
        photo = player_photo.get(player)
        if photo == "":
            if (player.team != ""):
                photo = 'nfl/'+player.team.upper()+".png"
            else:
                photo = "nfl/NOTEAM.png"
        else:
            photo = 'players/'+photo
        return photo


    # First, update nflgame
    if not args.photos:
        if args.year == "0" and args.week == "0" and args.weektype == "none":
            subprocess.call(c.PLAYER_UPDATE_CMD, shell=True)
        else:
            subprocess.call(c.PLAYER_UPDATE_CMD+' --year '+str(year)+' --week '+str(week)+' --phase '+weektype.upper(), shell=True)


    photodir = "./"

    print "Updating local FF database...."
    players = nflgame.players
    count = 0

    pos_dict = get_pos_dict()
    team_dict = get_team_dict()

    positions = {}

    add_count = 0
    update_count = 0
    for p in players:
        # print ".",
        count = count + 1
        try:
            birthdate = str(datetime.datetime.strptime(players[p].birthdate, '%m/%d/%Y'))
        except:
            birthdate = "0000-00-00"
        college = players[p].college
        full = players[p].full_name
        gsis_id = players[p].gsis_id
        gsis_name = players[p].gsis_name
        height = str(players[p].height)
        weight = str(players[p].weight)
        years_pro = str(players[p].years_pro)
        name = players[p].name
        number = str(players[p].number)
        player_id = players[p].player_id
        playerid = players[p].playerid
        profile_id = str(players[p].profile_id)
        profile_url = players[p].profile_url
        status = players[p].status
        uniform_number = players[p].uniform_number
        first = players[p].first_name
        last = players[p].last_name
        pos = str(pos_dict[players[p].position])
        team = str(team_dict[players[p].team])
        status = players[p].status
        active = (1 if status != "" else 0)

        cur.execute("select id, short_name, photo from player where player_id = '"+str(player_id)+"'")
        if cur.rowcount < 1: # Not found, must be a new player
            print "New player: "+p+" "+first+" "+last+" ("+pos+" - "+team+")"
            photo = get_photo(players[p])

            add_count = add_count + 1
            query = ("insert into player (player_id,nfl_position_id,nfl_team_id,first_name,last_name,birthdate,college,"+
            "short_name,height,weight,years_pro,number,profile_id,profile_url,status, active,photo) "+
            "values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')" %
            			(player_id,
            			pos,
            			team,
            			MySQLdb.escape_string(first),
            			MySQLdb.escape_string(last),
            			MySQLdb.escape_string(birthdate),
            			MySQLdb.escape_string(college),
            			MySQLdb.escape_string(gsis_name),
            			height,
            			weight,
            			years_pro,
            			number,
            			profile_id,
            			MySQLdb.escape_string(profile_url),
            			status,
                        active,
                        photo))

        else: # Already have this player, update some stuff.
            row = cur.fetchone()
            short_name = MySQLdb.escape_string(gsis_name)
            if (short_name == ""):
                short_name = MySQLdb.escape_string(first)[0]+'.'+MySQLdb.escape_string(last)
            if row['photo'] == "":
                if (players[p].team != ""):
                    photo = 'nfl/'+players[p].team.upper()+".png"
                else:
                    photo = "nfl/NOTEAM.png"
            else:
                photo = row['photo']

            # Recheck for photo from get_photo function if this player has the default team photo.
            # and photos arg was specified
            if args.photos and 'nfl/' in photo and active:
                photo = get_photo(players[p])

            update_count = update_count + 1
            query = ("update player set "+
            "nfl_team_id = " + team +
            ", years_pro = " + years_pro +
            ", number = " + number +
            ", active = "+str(active)+
            ", short_name = '"+short_name+"'"+
            ", status = '"+status+"'"+
            ", photo = '"+photo+"'"+
            " where player_id = '" + str(gsis_id)+"'")
        cur.execute(query)
    db.commit()
    print "Added: " + str(add_count) + " players."
    print "Updated: " + str(update_count) + " players."

def update_statistic_summaries(year, week, weektype):

    if week == 'all' and weektype == "REG":
        weeks = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17']
    elif weektype == "REG":
        weeks = [week]
    else:
        sys.exit('quitting')

    query = 'select id from nfl_week_type where text_id = "%s"' % (weektype)
    cur.execute(query)
    nfl_week_type_id = cur.fetchone()['id']
    query = ('select league.id from league join league_settings on league_settings.league_id = league.id where league_settings.nfl_season = "%s"' %(weektype))
    cur.execute(query)
    leaguerows = cur.fetchall()

    for league in leaguerows:
        leagueid = league['id']
        for w in weeks:
            query = ('select fs.player_id, sum(fs.points) as points, fs.week, fs.nfl_week_type_id, fs.year, fs.league_id, '+
                    'IFNULL(s.team_id,0) as team_id from fantasy_statistic as fs left join starter as s on s.league_id = fs.league_id and '+
                    's.week = fs.week and s.year = fs.year and s.nfl_week_type_id = fs.nfl_week_type_id and s.player_id = fs.player_id '+
                    'where fs.league_id = %s and fs.year = %s and fs.week=%s and fs.nfl_week_type_id = %s group by fs.player_id' %
                    (str(leagueid),str(year),str(w),str(nfl_week_type_id)))

            cur.execute(query)
            results = cur.fetchall();
            for row in results:
                query = ('select id from fantasy_statistic_week where player_id = %s and week=%s and year=%s and nfl_week_type_id=%s and league_id=%s'
                  % (row['player_id'], str(w), str(year), row['nfl_week_type_id'], row['league_id']))
                cur.execute(query)
                #print row
                if cur.rowcount == 0:
                    query = ('insert into fantasy_statistic_week (player_id,points,week,year,nfl_week_type_id,league_id,team_id)'+
                        ' VALUES(%s,%s,%s,%s,%s,%s,%s)' % (str(row['player_id']),str(row['points']),str(w),str(year),str(nfl_week_type_id),
                            str(row['league_id']),str(row['team_id'])))
                    cur.execute(query)
                else:
                    query = ('update fantasy_statistic_week set points = %s, team_id = %s where id = %s' %
                            (str(row['points']),str(row['team_id']),str(cur.fetchone()['id'])))
                    #print query
                    cur.execute(query)
            db.commit()


def update_schedule(season_year, week, weektype="REG"):

  if week == 'all' and weektype == "REG":
    weeks = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17']
  elif weektype == "REG":
    weeks = [week]
  elif weektype == "POST":
    weeks = ['1','2','3','4']

  else:
    print "Week type not supported: "+weektype
    return

  for week in weeks:
    # Should probably change this to use nflgame somehow, otherwise the timezone stuff without am/pm is tricky.
    updated = 0
    added = 0
    sched_url = 'http://www.nfl.com/ajax/scorestrip?season=%s&seasonType=%s&week=%s' % (season_year, weektype, str(int(week)))

    try:
      dom = xml.parse(urllib2.urlopen(sched_url))
    except urllib2.HTTPError:
      print >> sys.stderr, 'Could not load %s' % sched_url


    for g in dom.getElementsByTagName("g"):
      #year type week
      eid = g.getAttribute('eid')
      gsis = g.getAttribute('gsis')
      day = g.getAttribute('d')
      month = int(eid[4:6])
      day = int(eid[6:8])
      year = int(eid[0:4])
      time = g.getAttribute('t')
      quarter = g.getAttribute('q')
      k = g.getAttribute('k')
      home = g.getAttribute('h')
      home_long = g.getAttribute('hnn')
      home_score = g.getAttribute('hs')
      away = g.getAttribute('v')
      away_long = g.getAttribute('vnn')
      away_score = g.getAttribute('vs')
      p = g.getAttribute('p')
      rz = g.getAttribute('rz')
      ga = g.getAttribute('ga')
      (hour, minute) = time.split(":");

      # Ugh, this timezone stuff is ugly since it's in 12-hour with no AM/PM and there are games
      # in London before noon.  Just sort of guessing for now.
      hour = int(hour)
      if hour != 12 and hour != 9:
          hour = hour+12
      minute = int(minute)

      utc = pytz.timezone("UTC")
      est = pytz.timezone("US/Eastern")
      local = pytz.timezone(str(get_localzone()))


      start_time = est.localize(datetime.datetime(int(year),month,day,hour,minute,0,0))
      start_time = start_time.astimezone(local)

      #print start_time

      if home_score == '':
          home_score = -1
      if away_score == '':
          away_score = -1

      cur.execute("Select id from nfl_schedule where gsis = "+gsis)
      if cur.rowcount > 0:
        query = (('update nfl_schedule set eid=%s,d=%s,t="%s",q="%s",k="%s",h="%s",hnn="%s",v="%s",vnn="%s",hs="%s",vs="%s",p="%s",rz="%s",ga="%s", start_time="%s", year="%s" where id = %s') %
            (eid,day,time,quarter,k,home,home_long,away,away_long,home_score,away_score,p,rz,ga,str(start_time)[:-6],str(season_year),cur.fetchone()['id']))
        updated += 1
      else:
        query = ("Insert into nfl_schedule (eid, gsis, d, t, q, k, h, hnn, hs, v, vnn, vs, p, rz, ga, gt, week, year, start_time) "+
			   "Values(%s,%s,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')" %
	       (eid, gsis, day, time, quarter, k, home, home_long, home_score, away, away_long, away_score, p, rz, ga, weektype, week, season_year, start_time))
        added += 1
      cur.execute(query)


    db.commit()

    print "(%s Week %s): %s updated, %s added." % (str(season_year),str(week),str(updated),str(added))
  #http://www.nfl.com/ajax/scorestrip?season=2013&seasonType=REG&week=1
  #sched_url = 'http://www.nfl.com/ajax/scorestrip?season=%d&seasonType=%s&week=%d'

  #game_url = http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json
  #game_url = 'http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json'

# I THINK THESE ARE USED FOR LIVE STATS ONLY
# def get_pos_dict():
#   cur.execute('select id, text_id from nfl_position')
#   pos_dict = collections.defaultdict(lambda: 0, {})
#   for row in cur.fetchall():
#     pos_dict[row['text_id']] = row['id']
#
#   return pos_dict
#
# def get_team_dict():
#   cur.execute('select id, club_id from nfl_team')
#   team_dict = collections.defaultdict(lambda: 0, {})
#   for row in cur.fetchall():
#     team_dict[row['club_id']] = row['id']
#
#   return team_dict
#
# def init_playerdict(team_id):
#   playerdict = dict()
#   playerdict[team_id+"_D"] = {}
#   playerdict[team_id+"_DST"] = {}
#   playerdict[team_id+"_OL"] = {}
#   playerdict[team_id+"_ST"] = {}
#   #query = ("insert into player (player_id, nfl_position_id, nfl_team_id, status) values ('"+team_id+"_DST', (select id from nfl_position where text_id = 'T_DST'),(select id from nfl_team where club_id = '"+team_id+"'), 'ACT')")
#
#   return playerdict

parser = argparse.ArgumentParser(description='Short sample app')

parser.add_argument('--schedule', action="store_true", default=False, help="Update NFL schedule")
#parser.add_argument('-g', action="store_true", default=False, help="Update NFL game stats and recalculate fantasy stats")
parser.add_argument('--players', action="store_true", default=False, help="Update NFL players")
parser.add_argument('--photos', action="store_true", default=False, help="Check for photos for players that don't have one.")
parser.add_argument('--summary', action="store_true", default=False, help="Stat summary update: use stored player stat values and recalculate weekly summary data.")
parser.add_argument('--standings', action="store_true", default=False, help="Calculate standings results and add to schedule table.")
parser.add_argument('--year', action="store", default="0", required=False, help="Year")
parser.add_argument('--week', action="store", default="0", required=False, help="Week, use 'all' for all weeks.")
parser.add_argument('--weektype', action="store", default="none", required=False, help="Type: REG, POST, PRE")
parser.add_argument('--hello', action="store_true", default=False, help="Just tell me what the current Year, Week, and WeekType is!")

start_time = time.time()
args = parser.parse_args()
main()

#update_players_from_game_data(2015, 1, "PRE")

print "Completed "+str(datetime.datetime.now())
