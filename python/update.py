import sys
import json
import MySQLdb
import MySQLdb.cursors
import argparse
import time, pytz
from tzlocal import get_localzone
import datetime
import collections

import shieldquery
import config as c
import queries as q

db = MySQLdb.connect(host=c.DBHOST, user=c.DBUSER, passwd=c.DBPASS, db=c.DBNAME, cursorclass=MySQLdb.cursors.DictCursor)
cur = db.cursor()
cur.execute("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));")

query = 'select current_timestamp'
cur.execute(query)
sql_now = cur.fetchone()['current_timestamp']

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

    if(args.schedule):      # Update schedule from NFL api
        update_schedule(year, week, weektype)

    if(args.players):       # Update players from NFL api
        update_players(year, week, weektype)

    if(args.stats_summary): # calculate statistic_week 
        update_statistic_summaries(year, week, weektype)

    if(args.standings):     # update records / standings for a week
        update_standings(year, week, weektype)

    if(args.team_photos):   # Update the team logo images used for non-player photos (team def. etc)
        update_team_photos()

#   if(args.player_news):
#     update_player_news(year, week, weektype)

#   if(args.player_draft_ranks):
#     update_player_draft_ranks()

#   if(args.player_injuries):
#     update_player_injuries()

#   if(args.clear_player_generic_photo is not None):
#       clear_player_photos(args.clear_player_generic_photo)


def update_players(year, week, weektype):
    # Helper functions for update_players
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



    # Get all players in a list using pagination
    players = list()
    after = None
    count = 0
  
    print("Retreiving player data, please wait...")
    while True:
        result = api.query(q.GET_PLAYERS(200,2019,after))
        count += len(result["data"]["viewer"]["players"]["edges"])
        if args.verbose:
            print("Got "+str(count)+" players.")

        for player in result["data"]["viewer"]["players"]["edges"]:
            players.append(player["node"])
        if result["data"]["viewer"]["players"]["pageInfo"]["hasNextPage"]:
            after = result["data"]["viewer"]["players"]["pageInfo"]["endCursor"]
        else:
            after = None
        
        if after is None:
            break

        
    
    # print(json.dumps(players[-1],indent=4))
    # print(str(len(players)))

    # photodir = "./"

    pos_dict = get_pos_dict()
    team_dict = get_team_dict()

    positions = {}

    add_count = 0
    update_count = 0
    checked_count = 0
    count = 0
    print("Processing "+str(len(players))+" players... this can take a few minutes")
    for p in players:
        if count % 100 == 0:
            sys.stdout.write(".")
            sys.stdout.flush()
        count = count + 1
        try:
            birthdate = str(datetime.datetime.strptime(p["person"]["birthDate"], '%Y-%m-%d').date())
        except:
            birthdate = "0000-01-01"

        college = p["person"]["collegeName"]
        first = p["person"]["firstName"]
        last = p["person"]["lastName"]
        full = p["person"]["firstName"] + " " +p["person"]["lastName"]
        gsis_id = p["person"]["gsisId"]
        esbid = p["person"]["esbId"]
        
        # Used to be gsis_name, not sure if we have one of these, but creating it manually
        short_name = first[0]+". "+last
        if p["person"]["currentPlayer"]["height"] is not None:
            ft, inch = p["person"]["currentPlayer"]["height"].split("-")
            height = str(int(ft)*12+(int(inch)))
        else:
            height = 0

        if p["person"]["currentPlayer"]["weight"] is None:
            weight = 0
        else:
            weight = str(p["person"]["currentPlayer"]["weight"])
    
        if p["person"]["currentPlayer"]["nflExperience"] is None:
            years_pro = "0"
        else:
            years_pro = str(p["person"]["currentPlayer"]["nflExperience"])
        

        name = p["person"]["firstName"] + " " +p["person"]["lastName"]

        if p["person"]["currentPlayer"]["jerseyNumber"] is None:
            number = "0"
        else:
            number = str(p["person"]["currentPlayer"]["jerseyNumber"])

        current_shield_id = p["id"]
        shield_id = p["id"][3:18]
    
        headshot_url = str(p["person"]["headshot"]["url"])
        
        profile_id = "0" # Gone, probably related to profile_url, and can go away
        profile_url = ""  # This is gone, need to find a new one

        status = str(p["person"]["status"])
        status = str(p["person"]["currentPlayer"]["status"])

        pos = str(pos_dict[p["person"]["currentPlayer"]["position"]])

        if p["person"]["currentPlayer"].get("currentTeam") is None:
            team = str(team_dict["None"])
        else:
            team = str(team_dict[p["person"]["currentPlayer"]["currentTeam"]["id"]])

        active = (1 if status not in ["None","CUT"] else 0)


        cur.execute("select id, short_name, photo from player where shield_id = '"+shield_id+"' or player_id = '"+str(gsis_id)+"'")
    #    cur.execute("select id, short_name, photo from player where shield_id = '"+shield_id+"'")
        if cur.rowcount < 1: # Not found, must be a new player
            if args.verbose:
                print ("New player: "+first+" "+last+" ("+pos+" - "+team+") "+str(gsis_id)+" | "+shield_id)
    #         photo = get_photo(players[p])
            # Need to figure out adding the photo, empty string for now
            photo = ""
            add_count = add_count + 1
            query = ("insert into player (player_id,nfl_position_id,nfl_team_id,first_name,last_name,birthdate,college,"+
            "short_name,height,weight,years_pro,number,profile_id,profile_url,status, active,photo, esbid, shield_id, current_shield_id, headshot_url, last_seen) "+
            "values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',now())" %
            			(gsis_id,
            			pos,
            			team,
            			MySQLdb.escape_string(first),
            			MySQLdb.escape_string(last),
            			MySQLdb.escape_string(birthdate),
            			MySQLdb.escape_string(college),
            			MySQLdb.escape_string(short_name),
            			height,
            			weight,
            			years_pro,
            			number,
            			profile_id,
            			MySQLdb.escape_string(profile_url),
            			status,
                        active,
                        photo,
                        esbid,
                        shield_id,
                        current_shield_id,
                        MySQLdb.escape_string(headshot_url)))

        else: # Already have this player, update some stuff.
            if args.verbose:
                print ("Have player: "+first+" "+last+" ("+pos+" - "+team+") "+str(gsis_id)+" | "+shield_id)
            row = cur.fetchone()
            short_name = MySQLdb.escape_string(short_name)
            if (short_name == ""):
                short_name = MySQLdb.escape_string(first)[0]+'.'+MySQLdb.escape_string(last)
            if row['photo'] == "":
                if p["person"]["currentPlayer"].get("currentTeam") is not None and p["person"]["currentPlayer"]["currentTeam"]["abbreviation"] != "":
                    photo = 'nfl/'+p["person"]["currentPlayer"]["currentTeam"]["abbreviation"].upper()+".png"
                else:
                    photo = "nfl/NOTEAM.png"
            else:
                photo = row['photo']

            # Recheck for photo from get_photo function if this player has the default team photo.
            # and photos arg was specified

            #if args.photos and ('nfl/' in photo or photo == "") and active:
            if False:
                print "Checking photo for "+player_info(p)
                photo = ""
               # photo = get_photo(players[p])
               # need to figure out photo yet

            checked_count += 1
            query = ("update player set "+
            "nfl_team_id = " + team +
            ", years_pro = " + years_pro +
            ", number = " + number +
            ", active = "+str(active)+
            ", short_name = '"+short_name+"'"+
            ", status = '"+status+"'"+
            ", photo = '"+photo+"'"+
            ", esbid = '"+esbid+"'"+
            ", shield_id = '"+shield_id+"'"+
            ", current_shield_id ='"+current_shield_id+"'"+
            ", headshot_url = '"+MySQLdb.escape_string(headshot_url)+"'"
            " where (player_id = '" + str(gsis_id)+"'"+
            " OR shield_id = '"+shield_id+"')")

        


        cur.execute(query)
        if cur.rowcount > 0:
            update_count += 1
        
        query = 'update player set last_seen = now() where player_id ="%s" OR shield_id = "%s"' % (str(gsis_id), shield_id)
        cur.execute(query)
        
	db.commit()
    print ("\n================================")
    print ("Added: " + str(add_count) + " new players.")
    print ("Checked: "+str(checked_count)+ " players for changes.")
    print ("Updated: " + str(update_count) + " players.")
    print ("================================\n")

    # These are part of the shield api, don't need it
    # update_esbids(year,week)

    # # Adding this temporarily to correct team positions that got marked inactive
    # query = ('UPDATE player JOIN nfl_position ON nfl_position.id = player.nfl_position_id SET player.active = 1 '
    #         +'WHERE nfl_position.type = 3 or nfl_position.type = 4')
    
    # cur.execute(query)

    # This one stays, sets all non-team players inactive if they have last_seen older than 30 days ago
    query = ('UPDATE player JOIN nfl_position ON nfl_position.id = player.nfl_position_id SET player.active = 0 '
            +'WHERE last_seen < DATE_SUB( NOW( ) , INTERVAL 30 DAY ) AND nfl_position.type !=3 AND nfl_position.type !=4')
    cur.execute(query)


    num_inactive = cur.rowcount
    print "Players marked inactive: "+str(num_inactive)
    db.commit()

def update_schedule(season_year, week, weektype="REG"):

    team_dict = get_team_dict()

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

        # Get the schedule data from the Shield api
        results = api.league_games(season_year, week, weektype)

        updated = 0
        added = 0

        for game in results["data"]["viewer"]["league"]["games"]["edges"]:
            g = game["node"]
            # =================================================
            # Get timezone crap out of the way
            # =================================================
            gametime = datetime.datetime.strptime(g["gameTime"], "%Y-%m-%dT%H:%M:%S.000Z")
            # Make it timezone aware, UTC
            gametime = pytz.utc.localize(gametime)
            # Now adjust to the local timezone
            gametime = gametime.astimezone(pytz.timezone(str(get_localzone())))
            # Get unixtimestamp, cause I like that better than dealing with timezones
            unix_gametime = int(time.mktime(gametime.timetuple()))

            eid = g["esbId"]
            gsis = g["gsisId"]
            shield_id = g["id"]
            detail_id = g["gameDetailId"]
            day = gametime.day
            month = gametime.month
            year = gametime.year
            hour = gametime.hour                # I think this was only used before a timestamp was available
            minute = gametime.minute            # I think this was only used before a timestamp was available
            t = gametime.strftime("%H:%M")   # This used to be 12-hour, leaving it at 24-hour. Not even sure if it's used

            home = g["homeTeam"]["abbreviation"]
            away = g["awayTeam"]["abbreviation"]

            home_long = g["homeTeam"]["nickName"]
            away_long = g["awayTeam"]["nickName"]
            h_id = team_dict[g["homeTeam"]["id"]]
            v_id = team_dict[g["awayTeam"]["id"]]

            # Otherstuff I used to have available here, maybe just leave since this is the schedule update
            quarter = ""
            k = ""
            home_score = "0"
            away_score = "0"
            p = ""
            rz = ""
            ga = ""

            cur.execute("Select id from nfl_schedule where gsis = "+gsis)
            if cur.rowcount > 0:
                query = (('update nfl_schedule set eid=%s,d=%s,t="%s",h="%s",hnn="%s",v="%s",vnn="%s"'
                +',start_time="%s",year="%s",h_id=%s,v_id=%s,shield_id="%s",gameDetailId="%s" where id = %s') %
                (eid,day,t,home,home_long,away,away_long,str(gametime)[:-6],str(season_year),str(h_id),str(v_id),shield_id,detail_id,cur.fetchone()['id']))
                updated += 1
            else:
                query = ("Insert into nfl_schedule (eid, gsis, d, t, q, k, h, hnn, hs, v, vnn, vs, p, rz, ga, gt, week, year, start_time,h_id,v_id,shield_id,gameDetailId) "+
                "Values(%s,%s,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',%s,%s,'%s','%s')" %
                (eid, gsis, day, t, quarter, k, home, home_long, home_score, away, away_long, away_score, p, rz, ga, 
                weektype, week, season_year, str(gametime)[:-6],str(h_id),str(v_id),shield_id,detail_id))
                added += 1
            cur.execute(query)


        db.commit()

        print "(%s Week %s): %s updated, %s added." % (str(season_year),str(week),str(updated),str(added))


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
            results = cur.fetchall()
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

            # Check if this weeks NFL games are all marked complete?
            # Either by the nfl_schedule quarter having F in it, or by 3 hours past the start times of all games

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
    path = raw_input("Enter the sub-directory where they are located inside of ./images/")
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


def player_info(p):
    name = p["person"]["gsisId"]+": "
    name += p["person"]["firstName"]+" "+p["person"]["lastName"]
    if p["person"]["currentPlayer"].get("currentTeam") is not None:
        name += "( "+p["person"]["currentPlayer"]["currentTeam"]["abbreviation"]+" "
    else:
        name += "( None "
    
    name += p["person"]["currentPlayer"]["position"]+")"
    return name

# Used to look up database IDs for NFL teams
def get_team_dict():
    cur.execute('select shield_id, id, club_id, alt_club_ids from nfl_team')
    team_dict = collections.defaultdict(lambda: 0, {})
    for row in cur.fetchall():
        team_dict[row['shield_id']] = row['id']
        # Old from when club_ids were used to match
        # if row['alt_club_ids']:
        #     for alt in row['alt_club_ids'].split(','):
        #         alt = alt.strip()
        #         if alt != "":
        #             team_dict[alt] = row['id']


    return team_dict

parser = argparse.ArgumentParser(description='FFLProject: Update various parts of the database')
parser.add_argument('-year', action="store", default="0", required=False, help="Year")
parser.add_argument('-week', action="store", default="0", required=False, help="Week, use 'all' for all weeks.")
parser.add_argument('-weektype', action="store", default="none", required=False, help="Type: REG, POST, PRE")
parser.add_argument('-hello', action="store_true", default=False, help="Just tell me what the current Year, Week, and WeekType is!")
parser.add_argument('-verbose', action="store_true", default=False, help="Print out additional info.")
parser.add_argument('-schedule', action="store_true", default=False, help="Update NFL schedule") #
parser.add_argument('-schedule_clear', action="store_true", default=False, help="Add this to delete existing schedule records for the week and re-add them.")
parser.add_argument('-players', action="store_true", default=False, help="Update NFL players")
parser.add_argument('-stats_summary', action="store_true", default=False, help="Calculate and store player fantasy stats summaries")
parser.add_argument('-standings', action="store_true", default=False, help="Calculate standings results and add to schedule table.")
parser.add_argument('-team_photos', action="store_true", default=False, help="Update generic team photos for defenses, offensive lines, and other non-players.")
#parser.add_argument('-photos', action="store_true", default=False, help="Check for photos for players that don't have one.")
#parser.add_argument('-clear_player_generic_photo', action="store", default=None, required=False, help="Specify filename of player photo. Photos matching this file's hash will be cleared so they can be re-scanned.")
#parser.add_argument('-player_news', action="store_true", default=False, help="Update player news from NFL Fantasy api.")
#parser.add_argument('-player_draft_ranks', action="store_true", default=False, help="Update player draft rankings from NFL Fantasy api.")
#parser.add_argument('-player_injuries', action="store_true", default=False, help="Update player injury data from nfl.com/injuries.")


start_time = time.time()
args = parser.parse_args()

main()
