{* Smarty *}

<div class="ikBlock" id="info_blurb">
	<div class="ikHead">What is Imperial Kingdoms?</div>
	
	<div class="ikBody">
		<p>Imperial Kingdoms is a futuristic space-combat game of research, building, diplomacy, and conquest. Play happens in real-time but at a pace that allows players to only have to check in a few times a day usually.</p>
		<p>The goal of the game is to research new technologies, build your planet infrastructure, and produce armies to conquer other worlds and players.</p>
	</div>
</div>

{include file="sidebar_rounds.tpl"}

<div id="news">
{section name=element loop=$news}
	<div class="ikBlock">
		<div class="ikHead">{$news[element].title}</div>
		<div class="ikSubhead">Posted: {$news[element].username} @ {$news[element].time|date_format:"%d %b %Y %I:%M %p"}</div>
		<div class="ikBody">{$news[element].post_text}</div>
		<div class="ikFoot">Replies: {$news[element].replies} :: <a href="{$baseurl}forum/showthread.php?t={$news[element].topic}">View Replies</a></div>
	</div>
{sectionelse}
	<div class="ikBlock">
		<div class="ikHead">News</div>
		<div class="ikBody">No news is good news, right?</div>
	</div>
{/section}
</div>
