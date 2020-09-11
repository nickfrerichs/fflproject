def GET_PLAYERS(first, season, after=None):
    q = """
    {
        viewer{
            players(first:%s *AFTER* season_season:%s){
                edges{
                    node{
                        id
                        person{
                            firstName
                            lastName
                            middleName
                            nickName
                            birthDate
                            gsisId
                            esbId
                            collegeName
                            slug
                            displayName
                            headshot{
                                url
                            }
                            currentPlayer{
                                id
                                gsisId
                                currentTeam{
                                    id
                                    abbreviation
                                }
                                height
                                weight
                                jerseyNumber
                                nflExperience
                                position
                                status
                            }
                            property{
                                enabled
                            }
                            status
                        } 
                    }
                }
                pageInfo {
                    hasNextPage
                    hasPreviousPage
                    startCursor
                    endCursor
                    previousPageStartCursor
                    total
                }
            }
        }
    }

    """ % (str(first), str(season))

    if after is not None:
        q = q.replace("*AFTER*",'after:"'+after+'"')
    else:
        q = q.replace("*AFTER*","")

    return q
