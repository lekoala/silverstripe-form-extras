<% if AllSteps %>
<div class="steps">
	<% loop AllSteps %>
	<div class="step $Class">
		<span class="num">$Number</span>
		<% if Link and IsCompleted %>
		<a href='$Link'>
			$Title
		</a>
		<% else %>
		<span class="disabled">$Title</span>
		<% end_if %>
	</div>
	<% end_loop %>
</div>
<% end_if %>