<?php
// Get all pages
$result = pod_query("SELECT id, name FROM @wp_pod_templates ORDER BY name");
while ($row = mysql_fetch_assoc($result))
{
    $templates[$row['id']] = $row['name'];
}
?>

<!--
==================================================
Begin template area
==================================================
-->
<script type="text/javascript">
jQuery(function() {
    jQuery(".select-template").change(function() {
        template_id = jQuery(this).val();
        if ("" == template_id) {
            jQuery("#templateContent").hide();
            jQuery("#template_code").val("");
        }
        else {
            jQuery("#templateContent").show();
            loadTemplate();
        }
    });
    jQuery(".select-template").change();
    jQuery("#templateBox").jqm();
});

function loadTemplate() {
    jQuery.ajax({
        type: "post",
        url: "<?php echo PODS_URL; ?>/ajax/load.php",
        data: "auth="+auth+"&template_id="+template_id,
        success: function(msg) {
            if ("Error" == msg.substr(0, 5)) {
                alert(msg);
            }
            else {
                var json = eval("("+msg+")");
                var code = (null == json.code) ? "" : json.code;
                jQuery("#template_code").val(code);
            }
        }
    });
}

function addTemplate() {
    var name = jQuery("#new_template").val();
    jQuery.ajax({
        type: "post",
        url: "<?php echo PODS_URL; ?>/ajax/add.php",
        data: "auth="+auth+"&type=template&name="+name,
        success: function(msg) {
            if ("Error" == msg.substr(0, 5)) {
                alert(msg);
            }
            else {
                var id = msg;
                var html = '<option value="'+id+'">'+name+'</option>';
                jQuery(".select-template").append(html);
                jQuery("#templateBox #new_template").val("");
                jQuery(".select-template > option[value="+id+"]").attr("selected", "selected");
                jQuery(".select-template").change();
                jQuery("#templateBox").jqmHide();
            }
        }
    });
}

function editTemplate() {
    var code = jQuery("#template_code").val();
    jQuery.ajax({
        type: "post",
        url: "<?php echo PODS_URL; ?>/ajax/edit.php",
        data: "auth="+auth+"&action=edittemplate&template_id="+template_id+"&code="+encodeURIComponent(code),
        success: function(msg) {
            if ("Error" == msg.substr(0, 5)) {
                alert(msg);
            }
            else {
                alert("Success!");
            }
        }
    });
}

function dropTemplate() {
    if (confirm("Do you really want to drop this template?")) {
        jQuery.ajax({
            type: "post",
            url: "<?php echo PODS_URL; ?>/ajax/drop.php",
            data: "auth="+auth+"&template="+template_id,
            success: function(msg) {
                if ("Error" == msg.substr(0, 5)) {
                    alert(msg);
                }
                else {
                    jQuery(".select-template > option[value="+template_id+"]").remove();
                    jQuery(".select-template").change();
                }
            }
        });
    }
}
</script>

<!--
==================================================
Template popups
==================================================
-->
<div id="templateBox" class="jqmWindow">
    <input type="text" id="new_template" style="width:280px" />
    <input type="button" class="button" onclick="addTemplate()" value="Add Template" />
    <div>Ex: <strong>event_list</strong> or <strong>gallery_photo_detail</strong></div>
</div>

<!--
==================================================
Template HTML
==================================================
-->
<div id="templateArea" class="area hidden">
    <select class="area-select select-template">
        <option value="">Choose a Template</option>
<?php
if (isset($templates))
{
    foreach ($templates as $key => $val)
    {
?>
        <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php
    }
}
?>
    </select>
    <input type="button" class="button-primary" onclick="jQuery('#templateBox').jqmShow()" value="Add new template" />
    <div id="templateContent">
        <textarea id="template_code"></textarea><br />
        <input type="button" class="button" onclick="editTemplate()" value="Save changes" /> or
        <a href="javascript:;" onclick="dropTemplate()">drop template</a>
    </div>
</div>
