<div class="ui-slider" data-options='$SliderOptionsJson'></div>
<% if NotOnlySlider %>
<input $AttributesHTML />
<% if NotEditableField %>
<span class="readonly">$Value</span>
<% end_if %>
<% if Units %> $Units<% end_if %>
<% end_if %>