import sys
import MySQLdb
import MySQLdb.cursors
import nflgame
import argparse
import stat_functions as f
import math
import time
import datetime
import subprocess
import os
import config as c
from pprint import pprint


db = MySQLdb.connect(host=c.DBHOST, user=c.DBUSER, passwd=c.DBPASS, db=c.DBNAME, cursorclass=MySQLdb.cursors.DictCursor)
cur = db.cursor()
query = 'select current_timestamp'
cur.execute(query)
sql_now = cur.fetchone()['current_timestamp']
unix_timestamp = int(time.mktime(sql_now.timetuple()))

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
  (live_changes_made, livegamecount) = update_nfl_statistics(year, week, weektype, update_all)

  if livegamecount == 0:
    print "No games currently in progress."

  print("--- %s seconds ---\n" % str(time.time()-start_time))


  print "Calculating and storing fantasy values"
  update_fantasy_statistics(year, week, weektype)

  print("--- %s seconds ---\n" % str(time.time()-start_time))

  if live_changes_made:
      query = 'update league_settings set live_scores_key = %s' % (str(unix_timestamp))
      cur.execute(query)
      db.commit()
  else:
      print "\n -No changes to live game data-\n"

  print("--- %s seconds ---\n" % str(time.time()-start_time))

def update_nfl_statistics(year, week, weektype, update_all):
  global sql_now
  last_updated = sql_now
  live_changes_made = False

  # -----------------------------------------------------------------------------------------
  # 1. Get nfl_statistics from nflgame
  #  - Get nfl_schedule for selected week
  #  - For each game, if currently in progress:
  #    - Delete nfl_statistics with this game's gsis/gamekey
  #    - Loop through all game.player objects and insert 'direct mapped' stats
  #    - Insert team stats using game.stats_home and game.stats_away
  #    - loop through game.drives.plays() to calculate stats (this is the most complicated part)
  #
  # -----------------------------------------------------------------------------------------
  livegamecount = 0
  if int(year) > 2008: # years prior to 2008 need to be manually imported, they aren't in nflgame

    print '\nReading NFL stats for Week '+ str(week)

    games = nflgame.games(int(year), week=int(week), kind=weektype)

    print "Got all games"
    print("--- %s seconds ---\n" % str(time.time()-start_time))

    # variables to prune non-udpated rows later
    updated_games = ""


    for game in games:
      # Some options for storing pregame rather than having games show up as
      # final right before they start
      # print type(game.time)
      # print game.time.is_pregame()
      # print game.time
      # print game.nice_score()
      # ------------------------------------------
      # Update nfl_live_game, nfl_live_player
      # ------------------------------------------
      if game.playing() or game.game_over():
        livestatus = {}
        livestatus['gamekey'] = game.gamekey
        livestatus['to_go'] = game.togo
        livestatus['time'] = game.time.clock
        livestatus['quarter'] = game.time.qtr
        livestatus['down'] = game.down
        lastplay = None

        for drive in game.drives:
          if drive.team == "JAC": drive.team="JAX"
          if drive.team == "STL": drive.team="LA"
          if game.away == "JAC": game.away="JAX"
          if game.away == "STL": game.away="LA"
          if game.home == "JAC": game.home="JAX"
          if game.home == "STL": game.home="LA"
          livestatus['off'] = drive.team
          if drive.team == game.home:
            livestatus['def'] = game.away
          else:
            livestatus['def'] = game.home
          if drive.field_start is not None:
            livestatus['yardline'] = get_yard_line(drive.field_start.add_yards(drive.total_yds+drive.penalty_yds))
          else:
            livestatus['yardline'] = 0

          for play in drive.plays:
            livestatus['note'] = play.data['note']
            livestatus['details'] = play.data['desc']
            lastplay = play
        if game.playing():
            update_live_players(lastplay, game.gamekey)

        if game.playing():

            query = 'select id, play_id from nfl_live_game where nfl_schedule_gsis = %s' % (livestatus['gamekey'])
            cur.execute(query)
            ls = livestatus
            if ls['def'] == 'JAC': ls['def'] = 'JAX'
            if ls['def'] == 'STL': ls['def'] = 'LA'
            if ls['off'] == 'JAC': ls['off'] = 'JAX'
            if ls['off'] == 'STL': ls['off'] = 'LA'
            if cur.rowcount > 0:
              lgrow = cur.fetchone()
              if str(lastplay.playid) != str(lgrow['play_id']): #only update if it's a new play
                  query = (('update nfl_live_game set update_key = %s, down = %s, to_go = %s, quarter = "%s", off_nfl_team_id = '+
                    '(select id from nfl_team where club_id = "%s"), def_nfl_team_id = '+
                    '(select id from nfl_team where club_id = "%s"), yard_line = %s, time="%s", home_score = %s, away_score = %s, note = "%s", details = "%s", play_id = %s '+
                    'where id = %s') % (str(unix_timestamp), ls['down'],ls['to_go'],ls['quarter'],ls['off'],ls['def'],str(ls['yardline']),ls['time'],str(game.score_home), str(game.score_away),ls['note'],MySQLdb.escape_string(ls['details']),str(lastplay.playid),str(lgrow['id'])))
                  cur.execute(query)
                  live_changes_made = True
            else:
              query = (('insert into nfl_live_game (update_key, nfl_schedule_gsis, down, to_go, quarter, off_nfl_team_id, def_nfl_team_id, yard_line, time, week, nfl_week_type_id, year, home_score, away_score, note, details, play_id) values ('+
                '%s,%s,%s,%s,"%s",(select id from nfl_team where club_id = "%s"),(select id from nfl_team where club_id = "%s"),%s,"%s",%s,(select id from nfl_week_type where text_id = "%s"),%s,%s,%s,"%s","%s",%s)') %
                (str(unix_timestamp), ls['gamekey'],ls['down'],ls['to_go'],ls['quarter'],ls['off'],ls['def'],str(ls['yardline']),ls['time'],str(week),weektype,str(year), str(game.score_home), str(game.score_away),ls['note'],MySQLdb.escape_string(ls['details']),str(lastplay.playid)))

              cur.execute(query)
              live_changes_made = True

      # This updates nfl_statistic table, by default, just for live games update_all forces all games to be updated
      if game != None and (game.playing() or update_all):
        print str(game)+" - Q: "+str(game.time.qtr)+", "+game.time.clock
        livegamecount += 1
        # ----------------------------------------------
        # One Game - Create playerdict of stats
        # ----------------------------------------------

        # Playerdict to hold player_ids and scoring_cats/values for one game
        playerdict = dict(init_playerdict(str(game.home)).items() + init_playerdict(str(game.away)).items())

        # --------------------------
        # ADD STATS: simple 1:1 copy
        # --------------------------
        for player in game.players:
          add_other_player_stats(playerdict, player)

          for stat in player.stats:
            if playerdict.get(player.playerid) is None:
              playerdict[player.playerid] = {}
            playerdict[player.playerid][stat] = math.ceil(player.stats[stat])

        # --------------------------
        # ADD STATS: team stats
        # --------------------------
        home = game.home
        away = game.away
        # Team offensive line - Home
        playerdict[home+"_OL"]["team_rushing_yds"] = game.stats_home.rushing_yds
        playerdict[home+"_OL"]["team_passing_yds"] = game.stats_home.passing_yds
        # Team offensive line - Away
        playerdict[away+"_OL"]["team_rushing_yds"] = game.stats_away.rushing_yds
        playerdict[away+"_OL"]["team_passing_yds"] = game.stats_away.passing_yds
        # Team defense - Home
        playerdict[home+"_D"]["opp_score"] = game.score_away
        playerdict[home+"_D"]["opp_total_yds"] = game.stats_away.total_yds
        # Team defense - Away
        playerdict[away+"_D"]["opp_score"] = game.score_home
        playerdict[away+"_D"]["opp_total_yds"] = game.stats_home.total_yds
        # Def/ST Home
        playerdict[away+"_DST"]["opp_score"] = game.score_home
        playerdict[away+"_DST"]["opp_total_yds"] = game.stats_home.total_yds
        # Def/ST Away
        playerdict[home+"_DST"]["opp_score"] = game.score_away
        playerdict[home+"_DST"]["opp_total_yds"] = game.stats_away.total_yds
        # Special teams - Home
        # Special teams - Away

        # ---------------------------
        # ADD STATS: custom stats calculated by cycling through plays
        # ---------------------------

        for play in game.drives.plays():
          for event in play.events:
            for stat in event:
              value = None
              stat_name = None
              # Stats for team players
              if stat == "passing_sk": # Start here
                f.team_sack(event, game, playerdict)
              if stat == "fumbles_lost":
                f.team_fumble(event, game, playerdict)
              if stat == "defense_int":
                f.team_defint(event, game, playerdict)
              if stat == "defense_tds":
                f.player_def_td(event, playerdict)
                f.team_def_td(event, game, playerdict)
              if stat == "defense_safe":
                f.team_def_saf(event, game, playerdict)
              if stat == "puntret_tds" or stat == "kickret_tds":
                f.team_st_td(event, game, playerdict)
              # scenario where def recovers fumble, fumbles again and gets a TD
              if stat == "fumbles_rec_tds" and event["team"] != play.team:
                f.team_def_td(event, game, playerdict)

              # Stats for human players
              if stat == "kicking_fgm_yds": # Need yardages for each field goal
                f.player_field_goal(event, playerdict)

              if (stat == "kickret_yds" or stat == "puntret_yds") and play.note != "FUMBLE":
                f.AddPlayerStat(stat, event, playerdict)
              if (stat == "kicking_fgmissed"):
                f.AddPlayerStat(stat, event, playerdict)
              if (stat == "rushing_tds") or (stat == "receiving_tds"):
                f.AddPlayerTD(stat, event, playerdict)

              if (stat == "fumbles_rec_tds"):
                  f.AddPlayerStat(stat,event,playerdict)
              if (stat == "fumbles_rec_yds"):
                  f.AddPlayerStat(stat,event,playerdict)




        # -----------------------------------------
        # Save playerdict to nfl_statistic table
        # -----------------------------------------


        for player in playerdict:
          for stat in playerdict[player]:
            #print stat
            query = ('select id, value from nfl_statistic where player_nfl_id = "%s" and nfl_scoring_cat_id = (select id from nfl_scoring_cat where text_id = "%s") '+
              'and nfl_schedule_gsis = %s') % (player, stat, str(game.gamekey))
            cur.execute(query)

            if cur.rowcount > 0: # Row already exists, update
              row = cur.fetchone()
              rowid = row['id']
              value = row['value']
              if value == playerdict[player][stat]: # Value is unchanged, just update last_updated
                  query = ('update nfl_statistic set last_updated = "%s" where id = %s') % (last_updated,str(rowid))
              else: # Value changed, set last_change so I know to update that fantasy statistic
                  query = ('update nfl_statistic set value = %s, last_updated = "%s", last_changed = "%s" where id = %s') % (str(playerdict[player][stat]), last_updated, last_updated, str(rowid))
              cur.execute(query)
            else: # insert new row
              query = (('insert into nfl_statistic (player_nfl_id, player_id, nfl_scoring_cat_id, value, week, nfl_week_type_id, year, nfl_schedule_gsis, last_changed) values '+
                '("%s", (select id from player where player_id = "%s"), (select id from nfl_scoring_cat where text_id = "%s"), %s,%s,(select id from nfl_week_type where text_id = "%s"),%s,%s,"%s")') %
                (player,player,stat,playerdict[player][stat],week,weektype,year,str(game.gamekey),last_updated))

              cur.execute(query)


          # else delete live status ?

        updated_games += game.gamekey+","
        # commit all changes for this game

        db.commit()
      query = 'select id, hs, vs, q from nfl_schedule where gsis = %s' % (game.gamekey)
      cur.execute(query)
      s_row = cur.fetchone()
      if s_row['q'].lower() != game.time.qtr[0].lower() or s_row['hs'] != game.score_home or s_row['vs'] != game.score_away:
          query = 'update nfl_schedule set q = "%s", hs = %s, vs = %s where gsis = %s' % (game.time.qtr[0], str(game.score_home), str(game.score_away), str(game.gamekey))
          cur.execute(query)
          live_changes_made = True


    # delete any rows that were not updated, they must not exist anymore, probably temp errors in live scoring
    if livegamecount > 0:
      updated_games = updated_games[:-1]
      query = 'delete from nfl_statistic where week = %s and nfl_week_type_id = (select id from nfl_week_type where text_id = "%s") and year = %s and nfl_schedule_gsis in (%s) and last_updated < "%s"' % (week,weektype,year,updated_games,last_updated)
      cur.execute(query)
      deleted = cur.rowcount
      db.commit()
      print "Purged %s nfl_statistic rows." % str(deleted)

      # Also delete any rows where player_id is NULL
      query = 'delete from nfl_statistic where player_id is NULL'
      cur.execute(query)
      deleted = cur.rowcount
      db.commit()
      print "Purged %s nfl_statistic rows with NULL player_ids" % (str(deleted))

      # IF live changes were made, delete nfl_live_player lines that were not updated during this run
      if live_changes_made:
        query = 'delete from nfl_live_player where update_key != '+str(unix_timestamp)
        cur.execute(query)

        query = 'delete from nfl_live_game where nfl_schedule_gsis not in (%s)' % (updated_games)
        cur.execute(query)
        # Leave nfl_live_game rows so we know status is the same


    else: # Nothing is in progress, delete all live data
      query = 'truncate nfl_live_player'
      cur.execute(query)
      query = 'truncate nfl_live_game'
      cur.execute(query)
    db.commit()

    return (live_changes_made, livegamecount)
    # Done looking for week_type spot through here

def update_fantasy_statistics(year, week, weektype):

  # ---------------------------------------------------------------------------------------------------------
  # 2. (Re)Calculate all fantasy_statistic for this week using nfl_statistic and scoring_def
  #   -  Select all nfl_statistic rows for current week
  #
  #
  #
  # ---------------------------------------------------------------------------------------------------------

  # TO MAKE IT FASTER
  # 1. In update_nfl_statistic, set last_changed for any stat that actually changed
  # 2. Update ONLY the stats where nfl_statistic_id was last_changed this run.
  # 3. Delete any stats that don't have a corresponding nfl_statistic_id, they must have been mistakes that were corrected

  # Get the current timestamp from sql so we can delete fantasy_statistics that no longer exist at the end.
  global sql_now
  last_updated = sql_now


  # Get all leagues of current weektype
  query = ('select league.id from league join league_settings on league.id = league_settings.league_id where nfl_season = "%s"' % weektype)
  cur.execute(query)
  leagues = cur.fetchall()


  # Get nfl_statistic stats for this week to be used to calculate fantasy stats for all leagues
  query = ('select nfl_statistic.player_id, nfl_statistic.id, nfl_scoring_cat_id, value, nfl_position.id as pos_id from nfl_statistic '+
	'inner join player on nfl_statistic.player_id = player.id inner join nfl_position on nfl_position.id = player.nfl_position_id '+
	'where year = %s and week = %s and nfl_week_type_id = (select id from nfl_week_type where text_id = "%s")' % (str(year), str(week), weektype))


  query = ('select nfl_statistic.player_id, nfl_statistic.id, nfl_scoring_cat_id, value, nfl_position.id as pos_id from nfl_statistic '+
	'inner join player on nfl_statistic.player_id = player.id inner join nfl_position on nfl_position.id = player.nfl_position_id '+
	'where year = %s and week = %s and nfl_week_type_id = (select id from nfl_week_type where text_id = "%s" and last_changed = "%s")' % (str(year), str(week), weektype, last_updated))

  if (args.recalc_all):
      query = ('select nfl_statistic.player_id, nfl_statistic.id, nfl_scoring_cat_id, value, nfl_position.id as pos_id from nfl_statistic '+
    	'inner join player on nfl_statistic.player_id = player.id inner join nfl_position on nfl_position.id = player.nfl_position_id '+
    	'where year = %s and week = %s and nfl_week_type_id = (select id from nfl_week_type where text_id = "%s")' % (str(year), str(week), weektype))

  cur.execute(query)
  print
  print '******'
  print '******   %s fantasy_statistics to calculate' % (str(cur.rowcount))
  print '******'
  print
  nfl_stat_rows = cur.fetchall()

  for l in leagues:
      leagueid = l['id']
      scoring_def = get_scoring_def_dict(leagueid, year)

      # scoring_def[row['nfl_position_id']][row['nfl_scoring_cat_id']].append(row)

      for row in nfl_stat_rows:  # for each nfl_statistic this week, find a scoring_def for this league
        s = None
        if scoring_def.get(row['pos_id']) is not None and scoring_def[row['pos_id']].get(row['nfl_scoring_cat_id']) is not None:
          s = scoring_def[row['pos_id']][row['nfl_scoring_cat_id']]
        elif scoring_def.get(0) is not None and scoring_def[0].get(row['nfl_scoring_cat_id']) is not None:
          s = scoring_def[0][row['nfl_scoring_cat_id']]
        # A scoring def for this category exists if not None
        if s is not None:
          # Calculate points, this is done different depending if this scoring def is a range or not
          if len(s) == 1:
              if s[0]['per'] == 0:
                points = 0
              elif s[0]['round'] == 1:
                points = int(math.ceil(row['value'] * (s[0]['points']/float(s[0]['per']))))
              else:
                points = int(math.floor(row['value'] * (s[0]['points']/float(s[0]['per']))))
          elif s[0]['is_range']:
              # Check to see if row['value'] is in the range, if so set points, if not continue to next scoring def (or add one that's zero points?)
              for one in s:
                  if row['value'] >= one['range_start'] and row['value'] <= one['range_end']:
                      points = int(one['points'])

          query = (('select id, points from fantasy_statistic where week = %s and nfl_week_type_id = (select id from nfl_week_type where text_id = "%s") and year = %s and player_id = %s and league_id = %s and nfl_scoring_cat_id = %s')
            % (str(week),weektype,str(year),str(row['player_id']),leagueid,row['nfl_scoring_cat_id']))

          cur.execute(query)
          if cur.rowcount > 0:
            query = 'update fantasy_statistic set points = %s, last_updated = now(), nfl_statistic_id = %s where id = %s' % (str(points),str(row['id']),str(cur.fetchone()['id']))
          else:
            query = 'insert into fantasy_statistic (player_id, nfl_scoring_cat_id, points, week, nfl_week_type_id, year, league_id, nfl_statistic_id) values (%s,%s,%s,%s,(select id from nfl_week_type where text_id = "%s"),%s,%s,%s)' % (row['player_id'],row['nfl_scoring_cat_id'],str(points),str(week),weektype,str(year),leagueid,row['id'])
          cur.execute(query)

      db.commit()

  # Delete any stats that were no longer found in this update, they must have been retracted ?
  # all fantasy_statistics are recaculated for the given week, even if not all games are live
  query = (('delete from fantasy_statistic where week = %s and year = %s and nfl_week_type_id = (select id from nfl_week_type where text_id = "%s") and '+
            'nfl_statistic_id not in (select id from nfl_statistic where week = %s and year = %s and '+
            'nfl_week_type_id = (select id from nfl_week_type where text_id = "%s"))') % (str(week),str(year),weektype,str(week),str(year),weektype))

  cur.execute(query)

  deleted = cur.rowcount
  db.commit()

  print "Purged %s fantasy_statistic rows." % (str(deleted))

def get_yard_line(yard_line, team = ""):

  if str(yard_line) == '50' or str(yard_line) == 'MIDFIELD':
    return 0

  territory, yd_str = str(yard_line).split()
  yd = int(yd_str)
  if team == "":
    if territory == 'OWN':
      return -(50 - yd)
    else:
      return 50 - yd
  else:
    if territory == team:
      return -(50 - yd)
    else:
      return 50 - yd

def get_scoring_def_dict(leagueid, year):

  def_year = get_scoring_def_year(leagueid, year)
  scoring_def = {}

  # No scoring defs defined?
  if def_year is None:
    return scoring_def

  query = (('select scoring_def.nfl_scoring_cat_id, per, points, round, is_range, range_start, range_end, nfl_position_id from scoring_def '+
  'join nfl_scoring_cat on nfl_scoring_cat.id = scoring_def.nfl_scoring_cat_id where league_id = %s and year = %s') % (str(leagueid), str(def_year)))

  cur.execute(query)

  for row in cur.fetchall():
    if scoring_def.get(row['nfl_position_id']) is None:
        scoring_def[row['nfl_position_id']] = {}

    if scoring_def[row['nfl_position_id']].get(row['nfl_scoring_cat_id']) is None:
        scoring_def[row['nfl_position_id']][row['nfl_scoring_cat_id']] = list()
    scoring_def[row['nfl_position_id']][row['nfl_scoring_cat_id']].append(row)

  return scoring_def

def add_other_player_stats(playerdict, player):
  # add rushing/receiving combined stat
  rush = rec = 0
  if player.stats.has_key('rushing_yds'):
    rush = player.stats['rushing_yds']
  if player.stats.has_key('receiving_yds'):
    rec = player.stats['receiving_yds']
  yards = rush + rec
  if player.stats.has_key('rushing_yds') or player.stats.has_key('receiving_yds'):
    if playerdict.get(player.playerid) is None:
      playerdict[player.playerid] = {}
    playerdict[player.playerid]['rush_rec_yds'] = yards

def update_live_players(play, gamekey):
    for event in play.events:
        elist = list()
        for key, value in event.iteritems():
            elist.append(key)
        text = ""

        if "rushing_tds" in elist and "rushing_yds" in elist and text == "":
            text = str(event["rushing_yds"])+" yard TD run!"

        if "passing_tds" in elist and "passing_yds" in elist and text == "":
            text = str(event["passing_yds"])+" yard TD pass!"

        if "receiving_tds" in elist and "receiving_yds" in elist and text == "":
            text = str(event["receiving_yds"])+" yard TD catch!"

        if "receiving_yds" in elist and text == "":
            text = str(event["receiving_yds"])+" yard catch"

        if "passing_yds" in elist and text == "":
            text = str(event["passing_yds"])+" yard completion"

        if "rushing_yds" in elist and text == "":
            text = str(event["rushing_yds"])+" yard run"

        if "passing_ints" in elist and text == "":
            text = "Interception!"

        if "kicking_fgm" in elist and text == "":
            text = str(event["kicking_fgm_yds"]) + " yard FG is good!"

        if "kicking_fgmissed" in elist and text == "":
            text = "Field goal missed!"

        if "kicking_xpmade" in elist and text == "":
            text = "XP is Good!"

        if "kicking_xpmade" in elist and text == "":
            text = "Extra point is good."

        if "kicking_xpmissed" in elist and text == "":
            text = "Extra point missed!"

        if "fumbles_lost" in elist and text == "":
            text = "FUMBLE LOST!"

        if text != "":
            query = 'select id, play_id from nfl_live_player where nfl_player_id = "%s"' % (event['playerid'])
            cur.execute(query)
            if cur.rowcount > 0:
                lprow = cur.fetchone()
                if str(lprow['play_id']) != str(play.playid):
                    query = ('update nfl_live_player set update_key = %s, play_id = %s, text="%s" where nfl_player_id = "%s"'
                            % (str(unix_timestamp),str(play.playid),text,event['playerid']))
                    cur.execute(query)
            else:
                query = (('insert into nfl_live_player (player_id, gsis_id, play_id, text, nfl_player_id, update_key)'+
                    'values((select id from player where player_id = "%s"),%s,%s,"%s","%s",%s)')
                    % (event['playerid'],str(gamekey),str(play.playid),text,event['playerid'],str(unix_timestamp)))

                cur.execute(query)

    db.commit()

def get_scoring_def_year(league_id, year):
    query = "select max(year) as y from scoring_def where league_id = %s and year <= %s" %(str(league_id), str(year))
    cur.execute(query)
    return cur.fetchone()['y']

def get_pos_dict():
  cur.execute('select id, text_id from nfl_position')
  pos_dict = collections.defaultdict(lambda: 0, {})
  for row in cur.fetchall():
    pos_dict[row['text_id']] = row['id']

  return pos_dict

def get_team_dict():
  cur.execute('select id, club_id from nfl_team')
  team_dict = collections.defaultdict(lambda: 0, {})
  for row in cur.fetchall():
    team_dict[row['club_id']] = row['id']

  return team_dict

def init_playerdict(team_id):
  playerdict = dict()
  playerdict[team_id+"_D"] = {}
  playerdict[team_id+"_DST"] = {}
  playerdict[team_id+"_OL"] = {}
  playerdict[team_id+"_ST"] = {}

  return playerdict

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
