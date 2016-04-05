	// player_search_view
	function playerSearchPost(page, pos, sort, search, url)
	{
		url = typeof url !== 'undefined' ? url : "player_search/get_player_table";
		$.post(url,{'page':page, 'sel_pos':pos, 'sel_sort':sort, 'search' : search }, function(data){
			$("div#roster-table").html(data);
		});
	}