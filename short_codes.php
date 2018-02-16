<?php

/**********************************************************************
 * Custom Short codes
 * Render the custom fields by interfacting with the web service
 **********************************************************************/

function summary_shortcode($attr, $content=null)
{
    $source_url = get_option('source_url', '');
    $summary_json = file_get_contents($source_url . "/api/v1.0.0/summary");
    $summary = json_decode($summary_json);
    $content = "<h2>Model Overview</h2>";
    $content .= "<table id=\"summary\" class=\"row-border\">";
    $content .= "  <thead><tr><th>#</th><th>Description</th></tr></thead>";
    $content .= "  <tbody>";
    $content .= "    <tr><td>" . $summary->num_biclusters . "</td><td>Biclusters</td></tr>";
    $content .= "    <tr><td>" . $summary->num_mutations . "</td><td>Mutations</td></tr>";
    $content .= "    <tr><td>" . $summary->num_regulators . "</td><td>Regulators</td></tr>";
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";

    $content .= "    jQuery('#summary').DataTable({";
    $content .= "      'paging': false,";
    $content .= "      'info': false,";
    $content .= "      'searching': false";
    $content .= "    });";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}


function mutation_table_shortcode($attr, $content=null)
{
    $mutation_name = get_query_var('mutation');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/mutation/" .
                                     rawurlencode($mutation_name));
    $entries = json_decode($result_json)->entries;

    $content = "";
    $content .= "<h3>Biclusters for Mutation <i>" . $mutation_name . "</i></h3>";
    $content .= "<table id=\"biclusters\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Bicluster</th><th>Regulator</th><th>Role</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td>" . $e->bicluster . "</td><td><a href=\"index.php/regulator/?regulator=" . $e->regulator . "\">" . $e->regulator . "</a></td><td>" . $e->role . "</td></tr>";
    }
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#biclusters').DataTable({";
    $content .= "    })";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}


function regulator_table_shortcode($attr, $content=null)
{
    $regulator_name = get_query_var('regulator');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/regulator/" .
                                     rawurlencode($regulator_name));
    $entries = json_decode($result_json)->entries;
    $content = "";
    $content = "<h3>Biclusters for regulator " . $regulator_name . "</h3>";
    $content .= "<table id=\"biclusters\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Bicluster</th><th>Role</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td>" . $e->bicluster . "</td><td>" . $e->role . "</td></tr>";
    }
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#biclusters').DataTable({";
    $content .= "    })";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}

function search_box_shortcode($attr, $content)
{
    $content = "<form action=\"" . esc_url(admin_url('admin-post.php')) .  "\" method=\"post\">";
    $content .= "Search Term: <input name=\"search_term\" type=\"text\"></input>";
    $content .= "<div style=\"margin-top: 5px;\"><input type=\"submit\" value=\"Search\"></input></div>";
    $content .= "<input type=\"hidden\" name=\"action\" value=\"search_biclusters\">";
    $content .= "</form>";
    return $content;
}

function search_results_shortcode($attr, $content)
{
    $search_term = $_GET['search_term'];
    $content = "<div>Search Term: " . $search_term . "</div>";
    $solr_server = "http://garda:8983/solr";
    $core1 = "mtb_corems";
    $core2 = "mtb_clusters";

    $results_json = file_get_contents($solr_server . "/" . $core1 . "/select?indent=on&q=" .
                                      $search_term . "&wt=json&rows=1000");
    $results = json_decode($results_json);
    $num_found = $results->response->numFound;
    if ($num_found > 0) {
        $content .= "<div># corems found: " . $num_found . "</div>";
        $corems = array();
        foreach ($results->response->docs as $doc) {
            $corems []= (object) array('id' => $doc->id,
                                       'num_genes' => count($doc->genes),
                                       'num_conditions' => count($doc->conditions));
        }
        $content .= corems_table_html2($corems);
    } else {
        $results_json = file_get_contents($solr_server . "/" . $core2 . "/select?indent=on&q=" .
                                          $search_term . "&wt=json&rows=1000");
        $results = json_decode($results_json);
        $num_found = $results->response->numFound;
        $content .= "<div># biclusters found: " . $num_found . "</div>";
        $biclusters = array();
        foreach ($results->response->docs as $doc) {
            $biclusters []= (object) array('id' => $doc->id,
                                           'num_genes' => count($doc->genes),
                                           'num_conditions' => count($doc->conditions),
                                           'residual' => $doc->residual[0]);
        }
        $content .= biclusters_table_html($biclusters);
    }
    return $content;
}


function mmapi_add_shortcodes()
{
    add_shortcode('summary', 'summary_shortcode');
    add_shortcode('mutation_table', 'mutation_table_shortcode');
    add_shortcode('regulator_table', 'regulator_table_shortcode');

    add_shortcode('mmapi_search_box', 'search_box_shortcode');
    add_shortcode('mmapi_search_results', 'search_results_shortcode');
}

?>
