import os,sys
import json
import time
import requests
import datetime

try:
	import custom_get_auth_token
except:
    sys.exit("custom_get_auth_token.py should exist and contain a function get_token()")

class ShieldAPI:

    def __init__(self, proxies=None, headers=None):

        if proxies is None:
            self.proxies = dict()
        else:
            self.proxies = proxies

        self.headers = {
            "Content-Type"  : "application/json",
            "Connection"    : "keep-alive",
            "Cache-Control" : "max-age=0",
        }

        if headers is not None:
            self.headers.update(headers)

        self.session = requests.Session()
        self.session.headers.update(self.headers)

        self.api_auth_token = None
        self.__loadToken()

    def __query(self,q,v="null"):
        self.__setAuthHeader()
        q = ' '.join(q.split())
        chars = ['{','}',':','(',')',',']
        for c in chars:
            q = q.replace(' %s '%(c),c).replace('%s '%(c),c).replace(' %s'%(c),c)
        params = {
            "query" : q,
            "variables" : v
        }
        
        response = self.session.get("https://api.nfl.com/v3/shield/",params = params,proxies=self.proxies)
        return json.loads(response.content)

    def query(self,q,v="null"):
        return self.__query(q,v)

    # ===================================================
    # Public queries performed when browsing NFL scores
    # ===================================================

    # https://api.nfl.com/v1/config?c=%2Fpublic%2Fweb
    # This one has team logos and colors


    def current_season_state(self, date=None):
        # Original request URL
        # https://api.nfl.com/v3/shield/?query=%20query%7Bviewer%7Bleague%7Bcurrent(date%3A%222020-07-15%22)%7Bid%20week%7Bid%20seasonType%20seasonValue%20weekOrder%20weekType%20weekValue%7D%7D%7D%7D%7D&variables=null
        if date is None:
            date = datetime.datetime.now().strftime('%Y-%m-%d')
        return self.__query(
            """
            query {
                viewer {
                    league {
                        current(date: "%s") {
                            id
                            week {
                                id
                                seasonType
                                seasonValue
                                weekOrder
                                weekType
                                weekValue
                            }
                        }
                    }
                }
            }
            """ % (date)
        )

    def game_detail(self, gameDetailId):
        # Original request URL
        # https://api.nfl.com/v3/shield/?query=query%7Bviewer%7BgameDetail(id%3A%2210160000-0579-00cc-9a8c-d465ab211561%22)%7Bid%20attendance%20distance%20down%20gameClock%20goalToGo%20homePointsOvertime%20homePointsTotal%20homePointsQ1%20homePointsQ2%20homePointsQ3%20homePointsQ4%20homeTeam%7Babbreviation%20nickName%7DhomeTimeoutsUsed%20homeTimeoutsRemaining%20period%20phase%20playReview%20possessionTeam%7Babbreviation%20nickName%7Dredzone%20scoringSummaries%7BplayId%20playDescription%20patPlayId%20homeScore%20visitorScore%7Dstadium%20startTime%20visitorPointsOvertime%20visitorPointsOvertimeTotal%20visitorPointsQ1%20visitorPointsQ2%20visitorPointsQ3%20visitorPointsQ4%20visitorPointsTotal%20visitorTeam%7Babbreviation%20nickName%7DvisitorTimeoutsUsed%20visitorTimeoutsRemaining%20homePointsOvertimeTotal%20visitorPointsOvertimeTotal%20possessionTeam%7BnickName%7Dweather%7BcurrentFahrenheit%20location%20longDescription%20shortDescription%20currentRealFeelFahrenheit%7DyardLine%20yardsToGo%20drives%7BquarterStart%20endTransition%20endYardLine%20endedWithScore%20firstDowns%20gameClockEnd%20gameClockStart%20howEndedDescription%20howStartedDescription%20inside20%20orderSequence%20playCount%20playIdEnded%20playIdStarted%20playSeqEnded%20playSeqStarted%20possessionTeam%7Babbreviation%20nickName%20franchise%7BcurrentLogo%7Burl%7D%7D%7DquarterEnd%20realStartTime%20startTransition%20startYardLine%20timeOfPossession%20yards%20yardsPenalized%7Dplays%7BclockTime%20down%20driveNetYards%20drivePlayCount%20driveSequenceNumber%20driveTimeOfPossession%20endClockTime%20endYardLine%20firstDown%20goalToGo%20nextPlayIsGoalToGo%20nextPlayType%20orderSequence%20penaltyOnPlay%20playClock%20playDeleted%20playDescription%20playDescriptionWithJerseyNumbers%20playId%20playReviewStatus%20isBigPlay%20playType%20playStats%7BstatId%20yards%20team%7Bid%20abbreviation%7DplayerName%20gsisPlayer%7Bid%7D%7DpossessionTeam%7Babbreviation%20nickName%20franchise%7BcurrentLogo%7Burl%7D%7D%7DprePlayByPlay%20quarter%20scoringPlay%20scoringPlayType%20scoringTeam%7Bid%20abbreviation%20nickName%7DshortDescription%20specialTeamsPlay%20stPlayType%20timeOfDay%20yardLine%20yards%20yardsToGo%20latestPlay%7D%7D%7D%7D&variables=null

            # "playStats": [
            #   {
            #     "statId": 410,
            #     "yards": 75,
            #     "team": {
            #       "id": "10040325-2019-fe5a-96d1-46b29a6a2b60",
            #       "abbreviation": "BAL"
            #     },
            #     "playerName": "J.Tucker",
            #     "gsisPlayer": {
            #       "id": "32013030-2d30-3032-3935-39371b9a6ac1"  <= might be able to find this using livegame / livestats
            #     }
            #   }

        return self.__query("""
            query {
                viewer {
                    gameDetail(id: "%s") {
                        id
                        attendance
                        distance
                        down
                        gameClock
                        goalToGo
                        homePointsOvertime
                        homePointsTotal
                        homePointsQ1
                        homePointsQ2
                        homePointsQ3
                        homePointsQ4
                        homeTeam {
                            abbreviation
                            nickName
                        }
                        homeTimeoutsUsed
                        homeTimeoutsRemaining
                        period
                        phase
                        playReview
                        possessionTeam {
                            abbreviation
                            nickName
                        }
                        redzone
                        scoringSummaries {
                            playId
                            playDescription
                            patPlayId
                            homeScore
                            visitorScore
                        }
                        stadium
                        startTime
                        visitorPointsOvertime
                        visitorPointsOvertimeTotal
                        visitorPointsQ1
                        visitorPointsQ2
                        visitorPointsQ3
                        visitorPointsQ4
                        visitorPointsTotal
                        visitorTeam {
                            abbreviation
                            nickName
                        }
                        visitorTimeoutsUsed
                        visitorTimeoutsRemaining
                        homePointsOvertimeTotal
                        visitorPointsOvertimeTotal
                        possessionTeam {
                            nickName
                        }
                        weather {
                            currentFahrenheit
                            location
                            longDescription
                            shortDescription
                            currentRealFeelFahrenheit
                        }
                        yardLine
                        yardsToGo
                        drives {
                            quarterStart
                            endTransition
                            endYardLine
                            endedWithScore
                            firstDowns
                            gameClockEnd
                            gameClockStart
                            howEndedDescription
                            howStartedDescription
                            inside20
                            orderSequence
                            playCount
                            playIdEnded
                            playIdStarted
                            playSeqEnded
                            playSeqStarted
                            possessionTeam {
                                abbreviation
                                nickName
                                franchise {
                                    currentLogo {
                                        url
                                    }
                                }
                            }
                            quarterEnd
                            realStartTime
                            startTransition
                            startYardLine
                            timeOfPossession
                            yards
                            yardsPenalized
                        }
                        plays {
                            clockTime
                            down
                            driveNetYards
                            drivePlayCount
                            driveSequenceNumber
                            driveTimeOfPossession
                            endClockTime
                            endYardLine
                            firstDown
                            goalToGo
                            nextPlayIsGoalToGo
                            nextPlayType
                            orderSequence
                            penaltyOnPlay
                            playClock
                            playDeleted
                            playDescription
                            playDescriptionWithJerseyNumbers
                            playId
                            playReviewStatus
                            isBigPlay
                            playType
                            playStats {
                                statId
                                yards
                                team {
                                    id
                                    abbreviation
                                }
                                playerName
                                gsisPlayer {
                                    id
                                }
                            }
                            possessionTeam {
                                abbreviation
                                nickName
                                franchise {
                                    currentLogo {
                                        url
                                    }
                                }
                            }
                            prePlayByPlay
                            quarter
                            scoringPlay
                            scoringPlayType
                            scoringTeam {
                                id
                                abbreviation
                                nickName
                            }
                            shortDescription
                            specialTeamsPlay
                            stPlayType
                            timeOfDay
                            yardLine
                            yards
                            yardsToGo
                            latestPlay
                        }
                    }
                }
            }
        """ % (gameDetailId))

    def league_games(self,year,week,week_type):
        # Original request URL
        # https://api.nfl.com/v3/shield/?query=query%7Bviewer%7Bleague%7Bgames(first%3A100%2Cweek_seasonValue%3A2020%2Cweek_seasonType%3AREG%2Cweek_weekValue%3A2%2C)%7Bedges%7Bcursor%20node%7Bid%20esbId%20gameDetailId%20gameTime%20gsisId%20networkChannels%20radioLinks%20ticketUrl%20venue%7BfullName%20city%20state%7DawayTeam%7BnickName%20id%20abbreviation%20franchise%7BcurrentLogo%7Burl%7D%7D%7DhomeTeam%7BnickName%20id%20abbreviation%20franchise%7BcurrentLogo%7Burl%7D%7D%7Dslug%7D%7D%7D%7D%7D%7D&variables=null
        return self.__query("""
            query {
                viewer {
                    league {
                        games(
                            first: 100
                            week_seasonValue: %s
                            week_seasonType: %s
                            week_weekValue: %s
                        ) {
                            edges {
                                cursor
                                node {
                                    id
                                    esbId
                                    gameDetailId
                                    gameTime
                                    gsisId
                                    networkChannels
                                    radioLinks
                                    ticketUrl
                                    venue {
                                        fullName
                                        city
                                        state
                                    }
                                    awayTeam {
                                        nickName
                                        id
                                        abbreviation
                                        franchise {
                                            currentLogo {
                                                url
                                            }
                                        }
                                    }
                                    homeTeam {
                                        nickName
                                        id
                                        abbreviation
                                        franchise {
                                            currentLogo {
                                                url
                                            }
                                        }
                                    }
                                    slug
                                }
                            }
                        }
                    }
                }
            }
        """ % (str(year),str(week_type),str(week)))

    def player_game_stats(self, game_id):
        # Original request URL
        # https://api.nfl.com/v3/shield/?query=query%7Bviewer%7BplayerGameStats(first%3A200%2Cgame_id%3A%2210012019-0908-0811-7a44-785cc0a26345%22)%7Bedges%7Bcursor%20node%7BcreatedDate%20game%7Bid%7DgameStats%7BdefensiveAssists%20defensiveInterceptions%20defensiveInterceptionsYards%20defensiveForcedFumble%20defensivePassesDefensed%20defensiveSacks%20defensiveSafeties%20defensiveSoloTackles%20defensiveTotalTackles%20defensiveTacklesForALoss%20touchdownsDefense%20fumblesLost%20fumblesTotal%20kickReturns%20kickReturnsLong%20kickReturnsTouchdowns%20kickReturnsYards%20kickingFgAtt%20kickingFgLong%20kickingFgMade%20kickingXkAtt%20kickingXkMade%20passingAttempts%20passingCompletions%20passingTouchdowns%20passingYards%20passingInterceptions%20puntReturns%20puntingAverageYards%20puntingLong%20puntingPunts%20puntingPuntsInside20%20receivingReceptions%20receivingTarget%20receivingTouchdowns%20receivingYards%20rushingAttempts%20rushingAverageYards%20rushingTouchdowns%20rushingYards%20kickoffReturnsTouchdowns%20kickoffReturnsYards%20puntReturnsLong%20opponentFumbleRecovery%20totalPointsScored%20kickReturnsAverageYards%20puntReturnsAverageYards%20puntReturnsTouchdowns%7Did%20lastModifiedDate%20player%7Bposition%20jerseyNumber%20currentTeam%7Babbreviation%20nickName%7Dperson%7BfirstName%20lastName%20displayName%20headshot%7Basset%7Burl%7D%7D%7D%7Dseason%7Bid%7Dweek%7Bid%7D%7D%7D%7D%7D%7D&variables=null

            #   This part is shield_id: 35349-4541-0345
            # "id": "10435349-4541-0345-2019-2019112500e5",
            #   "lastModifiedDate": "2019-11-27T22:23:13.725Z",
            #   "player": {
            #     "position": "DE",
            #     "jerseyNumber": "92",
            #     "currentTeam": {
            #       "abbreviation": "MIA",
            #       "nickName": "Dolphins"
            #     },
            #     "person": {
            #       "firstName": "Zach",
            #       "lastName": "Sieler",
            #       "displayName": "Zach Sieler",
            #       "headshot": {
            #         "asset": {
            #           "url": "http://static.nfl.com/static/content/public/static/img/fantasy/transparent/512x512/SIE410345.png"
            #         }
            #       }
            #     }
            #   },

        return self.__query("""
            query {
                viewer {
                    playerGameStats(first: 200, game_id: "%s") {
                        edges {
                            cursor
                            node {
                                createdDate
                                game {
                                    id
                                }
                                gameStats {
                                    defensiveAssists
                                    defensiveInterceptions
                                    defensiveInterceptionsYards
                                    defensiveForcedFumble
                                    defensivePassesDefensed
                                    defensiveSacks
                                    defensiveSafeties
                                    defensiveSoloTackles
                                    defensiveTotalTackles
                                    defensiveTacklesForALoss
                                    touchdownsDefense
                                    fumblesLost
                                    fumblesTotal
                                    kickReturns
                                    kickReturnsLong
                                    kickReturnsTouchdowns
                                    kickReturnsYards
                                    kickingFgAtt
                                    kickingFgLong
                                    kickingFgMade
                                    kickingXkAtt
                                    kickingXkMade
                                    passingAttempts
                                    passingCompletions
                                    passingTouchdowns
                                    passingYards
                                    passingInterceptions
                                    puntReturns
                                    puntingAverageYards
                                    puntingLong
                                    puntingPunts
                                    puntingPuntsInside20
                                    receivingReceptions
                                    receivingTarget
                                    receivingTouchdowns
                                    receivingYards
                                    rushingAttempts
                                    rushingAverageYards
                                    rushingTouchdowns
                                    rushingYards
                                    kickoffReturnsTouchdowns
                                    kickoffReturnsYards
                                    puntReturnsLong
                                    opponentFumbleRecovery
                                    totalPointsScored
                                    kickReturnsAverageYards
                                    puntReturnsAverageYards
                                    puntReturnsTouchdowns
                                }
                                id
                                lastModifiedDate
                                player {
                                    position
                                    jerseyNumber
                                    currentTeam {
                                        abbreviation
                                        nickName
                                    }
                                    person {
                                        firstName
                                        lastName
                                        displayName
                                        headshot {
                                            asset {
                                                url
                                            }
                                        }
                                    }
                                }
                                season {
                                    id
                                }
                                week {
                                    id
                                }
                            }
                        }
                    }
                }
            }

        
        """% (game_id))

    # =============================================================================================
    # For loading token authorized for queries 
    # - custom_get_auth_token.py should exist
    # - it should contain a function get_token() that returns the token as a string
    # =============================================================================================

    def __loadToken(self):
        self.api_auth_token = custom_get_auth_token.get_token()
        self.__setAuthHeader()

    def __setAuthHeader(self):
        if self.api_auth_token is None:
            self.__loadToken()
        self.session.headers.update({
            "Authorization" :   "Bearer "+self.api_auth_token
        })
