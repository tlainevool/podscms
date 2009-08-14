<?php
/*
==================================================
PodAPI.class.php

http://pods.uproot.us/codex/

$data = array(
    0 => array(
        'name' => 'Item A',
        'date' => '2009-10-05 10:30:00',
        'related_companies' => array('AT&T', 'Verizon')
    ),
    1 => array(
        'name' => 'Item B',
        'date' => '2010-02-14 08:45:00',
        'related_companies' => array('Microsoft', 'Verizon', 'IBM')
    )
);
==================================================
*/
class PodAPI
{
    var $dt;
    var $dtname;
    var $fields;
    var $format;
    var $column_types;

    function PodAPI($dtname, $format = 'php')
    {
        $this->dtname = $dtname;
        $this->format = $format;

        $sql = "
        SELECT
            id
        FROM
            @wp_pod_fields
        WHERE
            datatype = (SELECT id FROM @wp_pod_types WHERE name = '$dtname' LIMIT 1)
        LIMIT
            1
        ";
        $result = pod_query($sql);
        if (0 < mysql_num_rows($result))
        {
            $this->dt = mysql_result($result, 0);
            $result = pod_query("SELECT id, name, coltype, pickval FROM @wp_pod_fields WHERE datatype = '$this->dt' ORDER BY weight");
            if (0 < mysql_num_rows($result))
            {
                while ($row = mysql_fetch_assoc($result))
                {
                    $this->fields[] = $row['name'];
                    $this->column_types[$row['name']] = $row;
                }
            }
        }
    }

    function import($data)
    {
        // Loop through each item, getting an array of columns
        foreach ($data as $key => $columns)
        {
            $pick_columns = array();
            $table_columns = array();

            // Loop through each column
            foreach ($this->fields as $key => $column_name)
            {
                $field_id = $this->column_types[$column_name]['id'];
                $coltype = $this->column_types[$column_name]['coltype'];
                $pickval = $this->column_types[$column_name]['pickval'];

                if (!empty($column_value))
                {
                    // PICK column
                    if ('pick' == $coltype)
                    {
                        if (!is_numeric($pickval))
                        {
                            $column_value = is_array($column_value) ? $column_value : array($column_value);
                            foreach ($column_value as $pick_key => $pick_value)
                            {
                                
                            }
                        }
                    }
                    // Standard table column
                    else
                    {
                        $column_value = mysql_real_escape_string(trim($columns[$column_name]));
                        $table_columns[] = "`$column_name` = '$column_value'";
                    }
                }
            }
            // Insert the row
            $tbl_row_id = pod_query("INSERT INTO @wp_pod_tbl_{$this->dtname} (" . implode(',', $this->fields . ") VALUES ()");
            $pod_id = pod_query("INSERT INTO @wp_pod (tbl_row_id, datatype, name, created, modified) VALUES ('$tbl_row_id', '{$this->dt}', '', NOW(), NOW())");
            pod_query("INSERT INTO @wp_pod_rel (pod_id, field_id, tbl_row_id) VALUES ('', '$pod_id', '$tbl_row_id')");
        }
    }

    function export()
    {
        // Get all Pod fields
        $result = pod_query("SELECT id, name, coltype, pickval FROM @wp_pod_fields WHERE datatype = '{$this->dt}' ORDER BY weight");
        while ($row = mysql_fetch_assoc($result))
        {
            $field_ids[] = $row['id'];
            $field_names[] = $row['name'];
        }

        $result = pod_query("SELECT id, name FROM @wp_pod");
        while ($row = mysql_fetch_assoc($result))
        {
            $pod_ids[$row['id']] = $row['name'];
        }

        $result = pod_query("SELECT field_id, tbl_row_id FROM @wp_pod_rel WHERE field_id IN (" . implode(',', $field_ids) . ")");
        while ($row = mysql_fetch_assoc($result))
        {
            
        }
    }

    function find_pick_id($item_name, $dtname)
    {
        
    }
}
