<?php

class Imgs {
    public static function getImgs($columns, $album, $tags, $except, $nsfw, $sort, $location, $length) {
        global $map_table, $db, $data_table, $album_table, $tag_table;

        if(is_numeric($location) && is_numeric($length)) {
            $limit_sql = " LIMIT $location, $length";
        } else {
            $limit_sql = " LIMIT 0, 10";
        }

        if($columns) {
            if(!is_array($columns)) {
                $columns = explode_trim($columns);
            }
            $select = "$data_table." . implode(", $data_table.", $columns);
        } else {
            $select = "$data_table.*";
        }

        if($album) {
            if($album != "_NULL_") {
                $result_album = run_sql("SELECT id, info, tags, except, sort, loadingimg FROM $album_table WHERE name='$album';");
                if($result_album) {
                    $array = $result_album -> fetch_array();
                    $album_id = $array['id'];
                    $tag_info = $array['info'];
                    $album_tags = explode(',', $array['tags']);
                    for($i = 0; $i < count($album_tags); $i++) {
                        $album_tags[$i] = trim($album_tags[$i]);
                        if(!$album_tags[$i]) {
                            unset($album_tags[$i]);
                        }
                    }
                    $album_except = explode(',', $array['except']);
                    for($i = 0; $i < count($album_except); $i++) {
                        $album_except[$i] = trim($album_except[$i]);
                        if(!$album_except[$i]) {
                            unset($album_except[$i]);
                        }
                    }
                    $sort = $array['sort'] ? $array['sort'] : $sort;
                    //$loadingimg = $array['loadingimg'] ? $array['loadingimg'] : $loadingimg;
                    //$album_sql = " AND $data_table.id IN (SELECT $amap_table.data_id FROM $amap_table WHERE album_id='$album_id')";
                }
            } else {
                $album_tags = array();
                $album_except = array();
            }
        } else {
            require("./page/common/albums.php");
            exit();
        }

# NSFW?
        $nsfw_except = array();
        if(is_numeric($nsfw)) {
            $result_nsfw = $db -> query("SELECT name FROM $tag_table WHERE nsfw > $nsfw;");
            for ($i = 0; $i < $result_nsfw -> num_rows; $i++) {
                $nsfw_except[$i] = $result_nsfw -> fetch_array()['name'];
            }
            $album_except = array_merge($album_except, $nsfw_except);
        }

# Set Sort Mode
        $sort = isset($_GET["sort"]) ? $_GET["sort"] : $sort;
        if($sort == "RAND") {
            $sort_sql = " ORDER BY RAND()";
        } else if($sort == "ASC") {
            $sort_sql = " ORDER BY $data_table.time ASC";
        } else {
            $sort_sql = " ORDER BY $data_table.time DESC";
        }

# Get Data
        $tags = explode(",", $tags);
        $count = count($tags);

        for($i = 0; $i < $count; $i++) {
            $tags[$i] = trim($tags[$i]);
            if(!$tags[$i]) {
                unset($tags[$i]);
            }
        }
        $count = count($tags) + count($album_tags);

        if($count == 0) {
            if($except || $album_except) {
                $except = explode(",", $except);
                $count_except = count($except);
                for($i = 0; $i < $count_except; $i++) {
                    $except[$i] = trim($except[$i]);
                    if(!$except[$i]) {
                        unset($except[$i]);
                    }
                }
                $except_to_organize = implode(",", $except);
                //$tag_title .= $except ? (",-" . implode(",-", $except)) : "";
            }
            $result=$db->query("SELECT $select FROM $data_table WHERE $data_table.id NOT IN (SELECT $data_table.id FROM $data_table, $map_table, $tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ('". implode("','", array_merge($except ? $except : array(), $album_except)) ."'))$album_sql GROUP BY $data_table.id$sort_sql$limit_sql;");
        } else if($count == 1) {
            foreach($tags as $tag) {
                if($tag == "_NULL_") {
                    $tag_title = "无标签";
                    $tag_to_add = "";
                    $tag_to_organize = $tag;
                    $result=$db->query("SELECT $select FROM $data_table WHERE id NOT IN (SELECT data_id FROM $map_table)$album_sql$sort_sql$limit_sql;");
                    $is_null = TRUE;
                } else {
                    $allow_edit = TRUE;
                    $result_tag_info = $db->query("SELECT info FROM $tag_table WHERE name='$tag';");
                    if($result_tag_info) {
                        $tag_info = $result_tag_info -> fetch_array()['info'];
                    }
                }
                break;
            }
        }
        if($count >= 1 && !$is_null) {
            $tag_title = implode(",", $tags);
            $tag_to_add = $tag_title;
            $tag_to_organize = $tag_title;
            $sql = "'" . implode("','", array_merge($tags, $album_tags)) . "'";
            if($except || $album_except) {
                $except = explode(",", $except);
                $count_except = count($except);
                for($i = 0; $i < $count_except; $i++) {
                    $except[$i] = trim($except[$i]);
                    if(!$except[$i]) {
                        unset($except[$i]);
                    }
                }
                $except_to_organize = implode(",", $except);
                $tag_title .= $except ? (",-" . implode(",-", $except)) : "";
                $except_sql = " AND $data_table.id NOT IN (SELECT $data_table.id FROM $data_table, $map_table, $tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ('". implode("','", array_merge($except, $album_except)) ."'))";
            }
            $result=$db->query("SELECT $select FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ($sql)$except_sql$album_sql GROUP BY $data_table.id HAVING COUNT($tag_table.name)=$count$sort_sql$limit_sql;");
        }

        return $result;
    }
}