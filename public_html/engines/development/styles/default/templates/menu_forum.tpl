{* Smarty *}

		<div class="button"><a href="{$baseurl}forum.php?fn=forum_topics">Forum</a></div>
{if $reply_page != "true"}
		<div class="button"><a href="{$baseurl}forum.php?fn=forum_post">New Topic</a></div>
{if !empty($forum_topic_id)}
		<div class="button"><a href="{$baseurl}forum.php?fn=forum_post&forum_topic_id={$forum_topic_id}">Post Reply</a></div>
{/if}
{/if}