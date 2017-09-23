def get_key(stat):
  if stat == "passing_cmp": return "Cpl"
  if stat == "passing_att": return "PassAtt"
  if stat == "passing_yds": return "PS Yds"
  if stat == "passing_ints": return "Int"
  if stat == "passing_tds": return "TD-P"
  if stat == "rushing_yds": return "RU Yds"
  if stat == "rushing_tds": return "TD-Ru"
  if stat == "receiving_rec": return "Rec"
  if stat == "receiving_yds": return "REC Yds"
  if stat == "receiving_tds": return "TD-Re"
  if stat == "fumbles_lost": return "Fum"
  if stat == "defense_tkl": return "Tk"
  if stat == "defense_sk": return "Sck"
  if stat == "defense_int": return "Intercepts"
  if stat == "defense_ffum": return "FF"
  if stat == "kicking_xpmade": return "XP"
  if stat == "kicking_xptot": return "XPAtt"
  if stat == "kicking_xpmissed": return "XPM"
  if stat == "kicking_fgmissed": return "Ms FG"
  if stat == "passing_twoptm": return "2-ptP"
  if stat == "rushing_twoptm": return "2-ptR"
  if stat == "receiving_twoptm": return "2-ptR"
  if stat == "kickret_yds": return "KR Yds"
  if stat == "puntret_yds": return "PR Yds"
  if stat == "kickret_tds": return "TD-S"
  if stat == "puntret_tds": return "TD-S"
  if stat == "passing_int": return "Int"
  if stat == "final overtime": return "Final"
  return stat

def team_sack(event, game, playerdict):
  home = str(game.home)
  away = str(game.away)
  if event["team"] == away:
    if playerdict[away+"_OL"].get("team_sacked") == None:
      playerdict[away+"_OL"]["team_sacked"] = 0.0
    playerdict[away+"_OL"]["team_sacked"] += event["passing_sk"]
    if playerdict[home+"_D"].get("team_sack") == None:
      playerdict[home+"_D"]["team_sack"] = 0.0
    playerdict[home+"_D"]["team_sack"] += event["passing_sk"]
    if playerdict[home+"_DST"].get("team_sack") == None:
      playerdict[home+"_DST"]["team_sack"] = 0.0
    playerdict[home+"_DST"]["team_sack"] += event["passing_sk"]
  else:
    if playerdict[home+"_OL"].get("team_sacked") == None:
      playerdict[home+"_OL"]["team_sacked"] = 0.0
    playerdict[home+"_OL"]["team_sacked"] += event["passing_sk"]
    if playerdict[away+"_D"].get("team_sack") == None:
      playerdict[away+"_D"]["team_sack"] = 0.0
    playerdict[away+"_D"]["team_sack"] += event["passing_sk"]
    if playerdict[away+"_DST"].get("team_sack") == None:
      playerdict[away+"_DST"]["team_sack"] = 0.0
    playerdict[away+"_DST"]["team_sack"] += event["passing_sk"]

def team_fumble(event, game, playerdict):
  home = str(game.home)
  away = str(game.away)
  if event["team"] == away:
    if playerdict[home+"_D"].get("team_fumble") == None:
      playerdict[home+"_D"]["team_fumble"] = 0
    playerdict[home+"_D"]["team_fumble"] = playerdict[home+"_D"]["team_fumble"] + event["fumbles_lost"]
    if playerdict[home+"_DST"].get("team_fumble") == None:
      playerdict[home+"_DST"]["team_fumble"] = 0
    playerdict[home+"_DST"]["team_fumble"] = playerdict[home+"_DST"]["team_fumble"] + event["fumbles_lost"]
  else:
    if playerdict[away+"_D"].get("team_fumble") == None:
      playerdict[away+"_D"]["team_fumble"] = 0
    playerdict[away+"_D"]["team_fumble"] = playerdict[away+"_D"]["team_fumble"] + event["fumbles_lost"]
    if playerdict[away+"_DST"].get("team_fumble") == None:
      playerdict[away+"_DST"]["team_fumble"] = 0
    playerdict[away+"_DST"]["team_fumble"] = playerdict[away+"_DST"]["team_fumble"] + event["fumbles_lost"]

def team_defint(event, game, playerdict):

  home = str(game.home)
  away = str(game.away)

  if event["team"] == home:
    if playerdict[home+"_D"].get("team_int") == None:
      playerdict[home+"_D"]["team_int"] = 0
    playerdict[home+"_D"]["team_int"] += event["defense_int"]
    if playerdict[home+"_DST"].get("team_int") == None:
      playerdict[home+"_DST"]["team_int"] = 0
    playerdict[home+"_DST"]["team_int"] += event["defense_int"]
  else:
    if playerdict[away+"_D"].get("team_int") == None:
      playerdict[away+"_D"]["team_int"] = 0
    playerdict[away+"_D"]["team_int"] += event["defense_int"]
    if playerdict[away+"_DST"].get("team_int") == None:
      playerdict[away+"_DST"]["team_int"] = 0
    playerdict[away+"_DST"]["team_int"] += event["defense_int"]

def team_def_td(event, game, playerdict):

  home = str(game.home)
  away = str(game.away)

  if event["team"] == home:
    if playerdict[home+"_D"].get("team_def_td") == None:
      playerdict[home+"_D"]["team_def_td"] = 0
    playerdict[home+"_D"]["team_def_td"] += 1
    if playerdict[home+"_DST"].get("team_def_td") == None:
      playerdict[home+"_DST"]["team_def_td"] = 0
    playerdict[home+"_DST"]["team_def_td"] += 1
  else:
    if playerdict[away+"_D"].get("team_def_td") == None:
      playerdict[away+"_D"]["team_def_td"] = 0
    playerdict[away+"_D"]["team_def_td"] += 1
    if playerdict[away+"_DST"].get("team_def_td") == None:
      playerdict[away+"_DST"]["team_def_td"] = 0
    playerdict[away+"_DST"]["team_def_td"] += 1

def team_def_saf(event, game, playerdict):

  home = str(game.home)
  away = str(game.away)

  if event["team"] == home:
    if playerdict[home+"_D"].get("team_safe") == None:
      playerdict[home+"_D"]["team_safe"] = 0
    playerdict[home+"_D"]["team_safe"] += event["defense_safe"]
    if playerdict[home+"_DST"].get("team_safe") == None:
      playerdict[home+"_DST"]["team_safe"] = 0
    playerdict[home+"_DST"]["team_safe"] += event["defense_safe"]
  else:
    if playerdict[away+"_D"].get("team_safe") == None:
      playerdict[away+"_D"]["team_safe"] = 0
    playerdict[away+"_D"]["team_safe"] += event["defense_safe"]
    if playerdict[away+"_DST"].get("team_safe") == None:
      playerdict[away+"_DST"]["team_safe"] = 0
    playerdict[away+"_DST"]["team_safe"] += event["defense_safe"]

def team_st_td(event, game, playerdict):
  home = str(game.home)
  away = str(game.away)

  if event["team"] == home:
    if playerdict[home+"_DST"].get("team_st_td") == None:
      playerdict[home+"_DST"]["team_st_td"] = 0
    playerdict[home+"_DST"]["team_st_td"] += 1
    if playerdict[home+"_ST"].get("team_st_td") == None:
      playerdict[home+"_ST"]["team_st_td"] = 0
    playerdict[home+"_ST"]["team_st_td"] += 1
    if playerdict[home+"_D"].get("team_st_td") == None:
      playerdict[home+"_D"]["team_st_td"] = 0
    playerdict[home+"_D"]["team_st_td"] += 1
  else:
    if playerdict[away+"_DST"].get("team_st_td") == None:
      playerdict[away+"_DST"]["team_st_td"] = 0
    playerdict[away+"_DST"]["team_st_td"] += 1
    if playerdict[away+"_ST"].get("team_st_td") == None:
      playerdict[away+"_ST"]["team_st_td"] = 0
    playerdict[away+"_ST"]["team_st_td"] += 1
    if playerdict[away+"_D"].get("team_st_td") == None:
      playerdict[away+"_D"]["team_st_td"] = 0
    playerdict[away+"_D"]["team_st_td"] += 1


def player_def_td(event, playerdict):
    player_id = event["playerid"]
    if playerdict.get(player_id) is None:
        playerdict[player_id] = {}
    if playerdict[player_id].get("defense_td") == None:
        playerdict[player_id]["defense_td"] = 0
    playerdict[player_id]["defense_td"] += 1

def player_field_goal(event, playerdict):

  player_id = event["playerid"]

  if playerdict.get(player_id) is None:  # new player, initialize
    playerdict[player_id] = {}

  distance = event["kicking_fgm_yds"]

  if distance > 0 and distance <= 24:
    if playerdict[player_id].get("fg_0_24") == None:
       playerdict[player_id]["fg_0_24"] = 0
    playerdict[player_id]["fg_0_24"] = playerdict[player_id]["fg_0_24"] + 1
  if distance > 24 and distance <= 29:
    if playerdict[player_id].get("fg_25_29") == None:
       playerdict[player_id]["fg_25_29"] = 0
    playerdict[player_id]["fg_25_29"] = playerdict[player_id]["fg_25_29"] + 1
  if distance > 29 and distance <= 34:
    if playerdict[player_id].get("fg_30_34") == None:
       playerdict[player_id]["fg_30_34"] = 0
    playerdict[player_id]["fg_30_34"] = playerdict[player_id]["fg_30_34"] + 1
  if distance > 34 and distance <= 39:
    if playerdict[player_id].get("fg_35_39") == None:
       playerdict[player_id]["fg_35_39"] = 0
    playerdict[player_id]["fg_35_39"] = playerdict[player_id]["fg_35_39"] + 1
  if distance > 39 and distance <= 44:
    if playerdict[player_id].get("fg_40_44") == None:
       playerdict[player_id]["fg_40_44"] = 0
    playerdict[player_id]["fg_40_44"] = playerdict[player_id]["fg_40_44"] + 1
  if distance > 44 and distance <= 49:
    if playerdict[player_id].get("fg_45_49") == None:
       playerdict[player_id]["fg_45_49"] = 0
    playerdict[player_id]["fg_45_49"] = playerdict[player_id]["fg_45_49"] + 1
  if distance > 49 and distance <= 54:
    if playerdict[player_id].get("fg_50_54") == None:
       playerdict[player_id]["fg_50_54"] = 0
    playerdict[player_id]["fg_50_54"] = playerdict[player_id]["fg_50_54"] + 1
  if distance >= 55:
    if playerdict[player_id].get("fg_55") == None:
       playerdict[player_id]["fg_55"] = 0
    playerdict[player_id]["fg_55"] = playerdict[player_id]["fg_55"] + 1

def AddPlayerStat(stat,event,playerdict):
  player_id = event["playerid"]
  if playerdict.get(player_id) is None:  # new player, initialize
    playerdict[player_id] = {}

  if playerdict[player_id].get(stat) is None:  # If first time seeing this stat, add it.
    playerdict[player_id][stat] = event[stat]
  else: # Otherwise check if it's an integer and add it.
    playerdict[player_id][stat] = playerdict[player_id][stat] + event[stat]

def AddPlayerTD(stat, event, playerdict):
  player_id = event["playerid"]
  if playerdict.get(player_id) is None:  # new player, initialize
    playerdict[player_id] = {}

  if event.get("rushing_yds") is not None:
    type = "rush"
    yards = int(event["rushing_yds"])
  if event.get("receiving_yds") is not None:
    type = "rec"
    yards = int(event["receiving_yds"])

  if yards > 0 and yards <= 1:
    stat_name = type+"_td_1"
  if yards > 1 and yards <= 9:
    stat_name = type+"_td_2_9"
  if yards > 9 and yards <= 19:
    stat_name = type+"_td_10_19"
  if yards > 19 and yards <= 29:
    stat_name = type+"_td_20_29"
  if yards > 29 and yards <= 39:
    stat_name = type+"_td_30_39"
  if yards > 39 and yards <= 49:
    stat_name = type+"_td_40_49"
  if yards > 49 and yards <= 59:
    stat_name = type+"_td_50_59"
  if yards > 59 and yards <= 69:
    stat_name = type+"_td_60_69"
  if yards > 69 and yards <= 79:
    stat_name = type+"_td_70_79"
  if yards > 79 and yards <= 89:
    stat_name = type+"_td_80_89"
  if yards > 89 and yards <= 99:
    stat_name = type+"_td_90_99"
  if yards > 100:
    stat_name = type+"_td_100"

  if playerdict[player_id].get(stat_name) is None:  # If first time seeing this stat, add i
    playerdict[player_id][stat_name] = 1
  else: # Otherwise check if it's an integer and add it.
    playerdict[player_id][stat_name] = playerdict[player_id][stat_name] + 1
