# fflproject
Fantasy Football League Project is an NFL fantasy football league manager created in the spirit of phpFFL. It's built on the Codeigniter framework with a goal of delivering a more modern experience to owners. It depnds on the python package nflgame for retrieving NFL data.

### 2020 Season:

The backend APIs used for stats went away recently. As such, much of this project is up in the air at the moment and mid-season development will likely happen this season. Be prepared to go several weeks with no scoring (yes, you may have to do pen/paper until a solution is figured out).

There is currently enough working for a draft to take place:
- Player updates
- NFL schedule updates

Currently not working
- Stats/scoring and live scoring
- Draft ranks
- Injuries
- Most anyting else needing an outside data source.

For those brave enough to proceed this season, here are steps to move from 2019s code to current 2020 code:
- Run db_upgrade.py (Takes a few seconds to run this time)
- add API_TOKEN_PATH to ./python/config.py (path to store a temporary token for data gathering)
- python update.py -schedule -year 2020 -week all
- python update.py -players
- python update.py -player_draft_ranks (these aren't working, but this resets them to 999 to avoid confusion)

In the GUI:
- admin > league settings > season > end season (should see ready to begin 2020 season)
- league > settings > offseason enabled off (logout and back in)
- admin > season > draft > create draft order
- admin > season > draft > draft settings

Good Luck!

![News screenshot](https://user-images.githubusercontent.com/5790350/62672877-ef580480-b961-11e9-946f-0e4e2fecade6.png)



Features:
- Live league chat
- Custom position definitions
- Custom scoring definitions
- Custom league schedule templates
- Multiple leagues
- Live draft
- Live scoring
- Bench scoring
- Player draft ranks import from NFL.com
- Responsive design for better mobile support
- League news page w/recent waiver wire moves
- Post player moves to Twitter
- Invite urls for new sign ups
- Historic stats for players and teams






More Screenshots

![My Team](https://user-images.githubusercontent.com/5790350/62673098-b8362300-b962-11e9-91fb-c21f1ce993ae.png)
![Player Search](https://user-images.githubusercontent.com/5790350/62673194-0e0acb00-b963-11e9-8376-eac893087cf1.png)
![Waiver Wire](https://user-images.githubusercontent.com/5790350/62673254-5c1fce80-b963-11e9-95ee-6f7e95eb10f4.png)

