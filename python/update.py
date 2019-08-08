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
import json
import hashlib
import pprint

try:
	import player_photo
	custom_player_photo = True
except:
	custom_player_photo = False

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

  if(args.stats_summary): # calculate statistic_week
    update_statistic_summaries(year, week, weektype)

  if(args.standings):
    update_standings(year, week, weektype)

  if(args.team_photos):
    update_team_photos()

  if(args.player_news):
    update_player_news(year, week, weektype)

  if(args.player_draft_ranks):
    update_player_draft_ranks()

  if(args.player_injuries):
    update_player_injuries()

  if(args.backfill_esbids):
    backfill_esbids(year)

  if(args.clear_player_generic_photo is not None):
      clear_player_photos(args.clear_player_generic_photo)


def update_standings(year, week ,weektype):

    if week == 'all':
        weeks = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17']
        weeks = range(1,18)
    else:
        weeks = [week]

    if args.week == "0":
        weeks = range(1,int(week)+1)

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


            # If there are no stats for this week, skip it, it's probably not happend yet.
            if len(rows) == 0:
                continue

            print "Updating week "+str(week)+" for leagueid "+str(leagueid)

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

            query = ('delete from schedule_result where schedule_id not in (select schedule.id from schedule)')
            cur.execute(query)
            db.commit()


def update_team_photos():
    print "(Save team photos should be saved as T_<club_id>.ext)"
    path = raw_input("Enter the path where they are located.    ./images/")
    ext = raw_input("Enter the extension of the files (.gif, .jpg, etc): ")

    query = ('SELECT player.id AS player_id, club_id, player.player_id as nflgame_id FROM nfl_position JOIN player ON nfl_position.id = player.nfl_position_id '+
             'JOIN nfl_team ON nfl_team.id = player.nfl_team_id WHERE nfl_position.type = 3 OR nfl_position.type = 4')

    cur.execute(query)
    rows = cur.fetchall()
    for row in rows:
        photo = os.path.join(path,"T_"+row['club_id']+ext)
        query = 'update player set photo = "'+photo+'" where player.id = '+str(row['player_id'])
        cur.execute(query)
        db.commit()
        print "Updated "+row['nflgame_id']+" with "+photo



def clear_player_photos(photo_filename):
    photo_path =  os.path.join(c.BASEDIR,"images/players/"+photo_filename)
    try:
        md5_to_match = hashlib.md5(open(photo_path,'rb').read()).hexdigest()
    except IOError:
        sys.exit('File not found: '+photo_path)
        
        
    count = 0
    print "Scanning photos...\n"
    for p_filename in os.listdir(os.path.join(c.BASEDIR,"images/players/")):
        p_path = os.path.join(c.BASEDIR,"images/players/"+p_filename)
        p_md5 = hashlib.md5(open(p_path,'rb').read()).hexdigest()
        if p_md5 == md5_to_match:
            
            query = 'update player set photo = "" where photo = "players/'+p_filename+'"'
            cur.execute(query)
            
            if cur.rowcount > 0:
                count += cur.rowcount
            db.commit()
            os.remove(p_path)

            print p_filename+" matched file hash and was cleared."
    print
    if count > 0:
        print "Player photos cleared: "+str(count)
        print "\nRun 'update.py -players -photos' to re-scan for new player photos"
    else:
        print "No player photos matched "
    print




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
	photo = ""
	if custom_player_photo:
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
            subprocess.call(c.PLAYER_UPDATE_CMD.split(' '))
        else:
            subprocess.call((c.PLAYER_UPDATE_CMD+' --year '+str(year)+' --week '+str(week)+' --phase '+weektype.upper()).split(' '))


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
            "short_name,height,weight,years_pro,number,profile_id,profile_url,status, active,photo, last_seen) "+
            "values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',now())" %
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
            if args.photos and ('nfl/' in photo or photo == "") and active:
                print "Checking photo for "+player_info(players[p])
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
            ", last_seen = now()"+
            " where player_id = '" + str(gsis_id)+"'")
        cur.execute(query)
	db.commit()

    print "Added: " + str(add_count) + " players."
    print "Updated: " + str(update_count) + " players."

    update_esbids(year,week)

    # Adding this temporarily to correct team positions that got marked inactive
    query = ('UPDATE player JOIN nfl_position ON nfl_position.id = player.nfl_position_id SET player.active = 1 '
            +'WHERE nfl_position.type = 3 or nfl_position.type = 4')
    
    cur.execute(query)

    # This one stays, sets all non-team players inactive if they have last_seen older than 30 days ago
    query = ('UPDATE player JOIN nfl_position ON nfl_position.id = player.nfl_position_id SET player.active = 0 '
            +'WHERE last_seen < DATE_SUB( NOW( ) , INTERVAL 30 DAY ) AND nfl_position.type !=3 AND nfl_position.type !=4')
    cur.execute(query)


    num_inactive = cur.rowcount
    print "Players marked inactive: "+str(num_inactive)
    db.commit()


def update_esbids(year, week):
    print 'Updating esbids from api.fantasy.nfl.com using year '+year+', week '+week

    stats_url = 'http://api.fantasy.nfl.com/v1/players/stats?statType=seasonStats&season=%s&week=%s&format=json' % (year, week)
    players = list()
    
    response = urllib.urlopen(stats_url)
    data = json.loads(response.read())
    esbid_count = 0
    for one in data['players']:
        gsis_id = one['gsisPlayerId']
        query = 'select id from player where player_id = "%s" and (esbid is null or esbid="" or esbid="None")' %(gsis_id)
        cur.execute(query)
        player_row = cur.fetchone()


        if player_row:
            query = 'update player set esbid = "%s" where id = %s' % (one['esbid'],str(player_row['id']))
            cur.execute(query)
            db.commit()
            esbid_count+=1
    if esbid_count > 0:
        print "Added "+str(esbid_count)+" esbids"

def backfill_esbids(year):
    for year in range(int(year)-4,int(year)+1):
        update_esbids(str(year),"1")

def update_statistic_summaries(year, week, weektype):

    if week == 'all' and weektype == "REG":
        weeks = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17']
    elif weektype == "REG" or weektype == "PRE":
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
  elif weektype == "POST" and week == "all":
    weeks = ['1','2','3','4']
  elif weektype == "PRE" and week == "all":
    weeks = ['1','2','3','4']
  else:
	weeks = [week]

  for week in weeks:

    if (args.schedule_clear):
        query = ('delete from nfl_schedule where week = %s and year = %s and gt = "%s"' %
            (str(week),str(season_year), weektype))
        cur.execute(query)
        db.commit()

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
      (hour, minute) = time.split(":")

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
	       (eid, gsis, day, time, quarter, k, home, home_long, home_score, away, away_long, away_score, p, rz, ga, weektype, week, season_year, str(start_time)[:-6]))
        added += 1
      cur.execute(query)


    db.commit()

    print "(%s Week %s): %s updated, %s added." % (str(season_year),str(week),str(updated),str(added))
  #http://www.nfl.com/ajax/scorestrip?season=2013&seasonType=REG&week=1
  #sched_url = 'http://www.nfl.com/ajax/scorestrip?season=%d&seasonType=%s&week=%d'

  #game_url = http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json
  #game_url = 'http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json'


def player_info(player):
    name = player.player_id+": "
    name += player.first_name+" "+player.last_name
    name += " ("+player.team+" - "+player.position+")"
    return name

def update_player_news(year,week,weektype):
    news_url = 'http://api.fantasy.nfl.com/v1/players/news?format=json'
    editor_rank_url = 'http://api.fantasy.nfl.com/v1/players/editorweekranks?format=json&season=%s&week=%s&count=75' % (str(year),str(week))
    researchinfo_url = 'http://api.fantasy.nfl.com/v1/players/researchinfo?format=json&season=%s&week=%s&count=200' % (str(year),str(week))

    # Update player researchinfo

    response = urllib.urlopen(researchinfo_url)
    data = json.loads(response.read())
    research_last_updated = data['lastUpdated']

    added = 0
    updated = 0

    for row in data['players']:
        # Get the gsisPlayerId from this rank, either a player or defense
        if row['gsisPlayerId'] == False and row['position'] == 'DEF':
            gsisPlayerId = row['teamAbbr']+'_D'
        elif row['gsisPlayerId'] != '':
            gsisPlayerId = row['gsisPlayerId']
        else:
            continue
        if row['depthChartOrder'] is None: row['depthChartOrder'] = 0

        query = 'select id, lastUpdated from player_researchinfo where gsisPlayerId="%s"' % (gsisPlayerId)
        cur.execute(query)
        if cur.rowcount > 0:
            research_row = cur.fetchone()
            if str(research_row['lastUpdated']) == str(research_last_updated): continue
            query = (('update player_researchinfo set percentOwned=%s,percentOwnedChange=%s,depthChartOrder=%s,percentStartedChange=%s,'
                     +'numAdds=%s,percentStarted=%s,numDrops=%s,lastUpdated="%s",player_id=(select id from player where player_id="%s") where id=%s') 
                %(row['percentOwned'],row['percentOwnedChange'],row['depthChartOrder'],row['percentStartedChange'],
                  row['numAdds'],row['percentStarted'],row['numDrops'],str(research_last_updated),gsisPlayerId,str(research_row['id'])))
            cur.execute(query)
            updated+=1
        else:
            query = (('insert into player_researchinfo (gsisPlayerId,percentOwned,percentOwnedChange,depthChartOrder,percentStartedChange,'
                     +'numAdds,percentStarted,numDrops,lastUpdated,player_id) VALUES("%s",%s,%s,%s,%s,%s,%s,%s,"%s",(select id from player where player_id="%s"))') 
                %(gsisPlayerId,row['percentOwned'],row['percentOwnedChange'],row['depthChartOrder'],row['percentStartedChange'],
                  row['numAdds'],row['percentStarted'],row['numDrops'],str(research_last_updated),gsisPlayerId))

            cur.execute(query)
            added+=1
    db.commit()
    query = ('delete from player_researchinfo where lastUpdated < "%s"' %(research_last_updated))
    cur.execute(query)
    deleted = cur.rowcount
    db.commit()

    if added > 0:
        print "Researchinfo players added: " + str(added)
    elif updated >0:
        print "Researchinfo players updated: " + str(updated)
    else:
        print "No new researchinfo players"

    if deleted > 0:
        print "Deleted "+str(deleted)+" researchinfo players."
    else:
        print "No researchinfo players to delete"


    # Update editor player ranks
    editor_rank_positions = ['QB','RB','WR','TE','K','DEF']
    added = 0
    updated = 0
    for pos in editor_rank_positions:
        temp_url = editor_rank_url+'&position='+pos

        response = urllib.urlopen(temp_url)
        data = json.loads(response.read())
        rank_last_updated = data['lastUpdated']
        for row in data['players']:
            # Get the gsisPlayerId from this rank, either a player or defense
            if row['gsisPlayerId'] == False and row['position'] == 'DEF':
                gsisPlayerId = row['teamAbbr']+'_D'
            elif row['gsisPlayerId'] != '':
                gsisPlayerId = row['gsisPlayerId']
            else:
                continue
            query = 'select id, lastUpdated from player_rank where gsisPlayerId="%s"' % (gsisPlayerId)
            cur.execute(query)
            if cur.rowcount > 0:
                rank_row = cur.fetchone()
                if str(rank_row['lastUpdated']) == str(rank_last_updated): continue

                rank_id = rank_row['id']
                query = (('update player_rank set rank=%s,rank_pos="%s",player_id=(select id from player where player_id="%s"),lastUpdated="%s" where id=%s')
                    %(row['rank'],row['position'],str(gsisPlayerId),str(rank_last_updated),str(rank_id)))
                cur.execute(query)
                updated+=1
            else:
                query = (('insert into player_rank (gsisPlayerId,rank,rank_pos,lastUpdated,player_id) VALUES ("%s",%s,"%s","%s",(select id from player where player_id="%s"))') 
                    %(gsisPlayerId,row['rank'],row['position'],str(rank_last_updated),gsisPlayerId))
                cur.execute(query)
                added+=1
    db.commit()
    query = ('delete from player_rank where lastUpdated < "%s"' %(rank_last_updated))
    cur.execute(query)
    deleted = cur.rowcount
    db.commit()

    if added > 0:
        print "Ranked players added: " + str(added)
    elif updated >0:
        print "Ranked players updated: " + str(updated)
    else:
        print "No new ranked players"

    if deleted > 0:
        print "Deleted "+str(deleted)+" ranked players."
    else:
        print "No ranked players to delete"


    response = urllib.urlopen(news_url)
    data = json.loads(response.read())

    added = 0

    for row in data['news']:

        # Check if news ID already exists in player_news table
        query = 'select id from player_news where news_id = %s' % (str(row['id']))
        cur.execute(query)
        if cur.rowcount > 0: continue

        # Check if the player's gsisPlayerId is in the player table
        query = 'select id from player where player_id = "%s"' % (row['gsisPlayerId'])
        cur.execute(query)
        if cur.rowcount == 0: continue
        
        player_id = cur.fetchone()['id']

        query = (('insert into player_news (news_id,gsisPlayerId,news_date,source,headline,body,analysis,player_id) values (%s,"%s","%s","%s","%s","%s","%s",%s)') 
                % (row['id'],row['gsisPlayerId'],str(row['timestamp']),MySQLdb.escape_string(row['source']),MySQLdb.escape_string(row['headline']),
                   MySQLdb.escape_string(row['body']),MySQLdb.escape_string(row['analysis']),str(player_id)))

        cur.execute(query)
        db.commit()

        added += 1

    query = ('delete from player_news where news_date < NOW() - INTERVAL 90 DAY')
    cur.execute(query)
    deleted = cur.rowcount
    db.commit()
        
    if added > 0:
        print "New news items added: " + str(added)
    else:
        print "No new news items to add."

    if deleted > 0:
        print "Deleted "+str(deleted)+" news items older than 90 days."
    else:
        print "No news items older than 90 days to delete."

    

def update_player_draft_ranks():
    ranks_url = 'http://api.fantasy.nfl.com/v1/players/userdraftranks?format=json&count=100'
    players = list()
    
    for i in range(0,5):
        response = urllib.urlopen(ranks_url+'&offset='+str(i*100))
        data = json.loads(response.read())
        players += data['players']

    for p in players:

        # Get the gsisPlayerId from this rank, either a player or defense
        if p['gsisPlayerId'] == False and p['position'] == 'DEF':
            gsisPlayerId = p['teamAbbr']+'_D'
        elif p['gsisPlayerId'] != '':
            gsisPlayerId = p['gsisPlayerId']
        else:
            continue

        # Look up an id in the player table for this gsisPlayerId
        query = 'select id from player where player_id = "%s"' % (gsisPlayerId)
        cur.execute(query)

        if cur.rowcount < 1: continue
        player_id = cur.fetchone()['id']

        # Sometimes aav is None, but we're expecting a float
        if p['aav'] == None:
            p['aav'] = 0.0

        # Check to see if that player_id is already in the rank table
        query = 'select * from draft_player_rank where player_id = %s' % (str(player_id))
        cur.execute(query)

        # Player is already ranked, update the record with new data
        if cur.rowcount == 1:
            rank_id = cur.fetchone()['id']
            query = (('update draft_player_rank set rank=%s, aav=%s, last_updated="%s" where id = %s') 
                    % (str(p['rank']),str(p['aav']),sql_now,str(rank_id)))
            cur.execute(query)
        # Else, player is not yet ranked, add the new record
        else:
            query = (('insert into draft_player_rank (player_id, rank, aav, gsisPlayerId, last_updated) VALUES (%s,%s,%s,"%s","%s")')
                    % (str(player_id),str(p['rank']),str(p['aav']),gsisPlayerId,sql_now))
            cur.execute(query)
        db.commit()

    # Delete any records for players who haven't been updated, they are not ranked any longer.
    query = 'delete from draft_player_rank where last_updated != "%s"' % (sql_now)
    cur.execute(query)
    db.commit()

    # Lastly, set the rank_order so players can be ordered by integers 1 to n, best to worst
    query = 'select id from draft_player_rank order by rank asc'
    cur.execute(query)
    results = cur.fetchall()
    cur_rank = 1
    for row in results:
        query = 'update draft_player_rank set rank_order = %s where id = %s' % (str(cur_rank), str(row['id']))
        cur.execute(query)
        cur_rank += 1
    db.commit()

def update_player_injuries():
    # Get the injury types
    query = 'select text_id, short_text from player_injury_type'
    cur.execute(query)
    injury_type_results = cur.fetchall()
    injury_types = list()
    for i in injury_type_results:
        injury_types.append(i['short_text'])

    injury_url = 'http://www.nfl.com/injuries'

    response = urllib.urlopen(injury_url)
    data = response.read()

    # Figure out what week this injury data is from
    injury_week = data.split('<h3>NFL Injuries Week ')[1].split('</h3>')[0]
    
    prefix='{player: '
    suffix='}'
    player_start = data.split(prefix)
    updated_count = 0
    added_count = 0
    for p in player_start[1:]:
        player_json_like = prefix+p.split(suffix)[0].strip()+suffix
        player_parts_list = player_json_like.replace('{','').replace('}','').split(',')
        player_dict = dict()
        for p in player_parts_list:
            (key, val) = p.split(':')
            player_dict[key.strip()] = val.replace('"',"").strip()
        if player_dict['gameStatus'] not in injury_types: continue
        query = 'select id from player where esbid = "%s"' % (player_dict['esbId'])
        cur.execute(query)
        if cur.rowcount > 0:
            player_db_id = cur.fetchone()['id']
        else:
            continue
        query = 'select id from player_injury where player_id = %s' % (player_db_id)
        cur.execute(query)
        if cur.rowcount > 0:
            row_id = cur.fetchone()['id']
            query = (('update player_injury set injury="%s", practiceStatus="%s", last_updated="%s", player_injury_type_id='
                     +'(select id from player_injury_type where short_text="%s"), week=%s where id=%s')
                     %(player_dict['injury'],player_dict['practiceStatus'],sql_now,player_dict['gameStatus'],str(injury_week),str(row_id)))
            cur.execute(query)
            updated_count+=1
        else:
            query = (('insert into player_injury (player_id,injury,practiceStatus,last_updated,player_injury_type_id,description,week)'+
                    'values(%s,"%s","%s","%s",(select id from player_injury_type where short_text="%s"),"",%s)')
                    %(str(player_db_id),player_dict['injury'],player_dict['practiceStatus'],sql_now,player_dict['gameStatus'],str(injury_week)))
            cur.execute(query)
            added_count+=1
        db.commit()

    query = ('delete from player_injury where last_updated < "%s"' %(sql_now))
    cur.execute(query)
    deleted = cur.rowcount
    db.commit()

    print "Injury report for week "+str(injury_week)

    if added_count > 0:
        print "Injured players added: " + str(added_count)
    elif updated_count >0:
        print "Injured players updated: " + str(updated_count)
    else:
        print "No new injured players"

    if deleted > 0:
        print "Deleted "+str(deleted)+" injured players."
    else:
        print "No injured players to delete"

            

parser = argparse.ArgumentParser(description='FFLProject: Update various parts of the database')

parser.add_argument('-schedule', action="store_true", default=False, help="Update NFL schedule")
parser.add_argument('-schedule_clear', action="store_true", default=False, help="Add this to delete existing schedule records for the week and re-add them.")
#parser.add_argument('-g', action="store_true", default=False, help="Update NFL game stats and recalculate fantasy stats")
parser.add_argument('-players', action="store_true", default=False, help="Update NFL players")
parser.add_argument('-photos', action="store_true", default=False, help="Check for photos for players that don't have one.")
parser.add_argument('-clear_player_generic_photo', action="store", default=None, required=False, help="Specify filename of player photo. Photos matching this file's hash will be cleared so they can be re-scanned.")
parser.add_argument('-stats_summary', action="store_true", default=False, help="Calculate and store player fantasy stats summaries")
parser.add_argument('-standings', action="store_true", default=False, help="Calculate standings results and add to schedule table.")
parser.add_argument('-year', action="store", default="0", required=False, help="Year")
parser.add_argument('-week', action="store", default="0", required=False, help="Week, use 'all' for all weeks.")
parser.add_argument('-weektype', action="store", default="none", required=False, help="Type: REG, POST, PRE")
parser.add_argument('-hello', action="store_true", default=False, help="Just tell me what the current Year, Week, and WeekType is!")
parser.add_argument('-team_photos', action="store_true", default=False, help="Update generic team photos for defenses, offensive lines, and other non-players.")
parser.add_argument('-player_news', action="store_true", default=False, help="Update player news from NFL Fantasy api.")
parser.add_argument('-player_draft_ranks', action="store_true", default=False, help="Update player draft rankings from NFL Fantasy api.")
parser.add_argument('-player_injuries', action="store_true", default=False, help="Update player injury data from nfl.com/injuries.")
parser.add_argument('-backfill_esbids', action="store_true", default=False, help="Some NFL.com feeds use esbid's, attempt to go back 5 years and find them.")


start_time = time.time()
args = parser.parse_args()
main()

#update_players_from_game_data(2015, 1, "PRE")

print "Completed "+str(datetime.datetime.now())
