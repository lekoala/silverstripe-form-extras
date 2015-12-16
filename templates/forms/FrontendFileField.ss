<input type="hidden" name="MAX_FILE_SIZE" value="$MaxFileSize" />

<% if PreviewAvailable %>
<table>
    <tr>
        <td width="120">
            <div class="frontend-file-preview">
                <% if IsImage %>
                <a href="$File.Link" target="_blank" rel="mp-iframe">$File.SetSize(100,100)</a>
                <% else %>
                <a href="$File.Link" target="_blank" rel="mp-iframe" >
                    <img src="/form-extras/images/file.png" />
                </a>
                <% end_if %>
            </div>
        </td>
        <td><input $AttributesHTML /></td>
    </tr>
</table>

<% else %>
<input $AttributesHTML />
<% end_if %>