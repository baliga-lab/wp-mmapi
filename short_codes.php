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
        $content .= "    <tr><td><a href=\"index.php/bicluster/?bicluster=" . $e->bicluster . "\">" . $e->bicluster . "</a></td><td><a href=\"index.php/regulator/?regulator=" . $e->regulator . "\">" . $e->regulator . "</a></td><td>" . $e->role . "</td></tr>";
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
        $content .= "    <tr><td><a href=\"index.php/bicluster/?bicluster=" . $e->bicluster . "\">" . $e->bicluster . "</a></td><td>" . $e->role . "</td></tr>";
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

/*
 * TODO: Add information from EnsEMBL and Uniprot
 *
 * Example call to EnsEMBL
 * https://rest.ensembl.org/lookup/id/ENSG00000214900?content-type=application/json;expand=1
 *
 * XREF to Uniprot
 * https://rest.ensembl.org/xrefs/id/ENSG00000181991?content-type=application/json
 */
function bicluster_genes_table_shortcode($attr, $content=null)
{
    $bicluster_name = get_query_var('bicluster');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/bicluster/" .
                                     rawurlencode($bicluster_name));
    $entries = json_decode($result_json)->genes;
    $content = "";
    $content = "<h3>Genes for bicluster " . $bicluster_name . "</h3>";
    $content .= "<table id=\"bc_genes\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Gene</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/gene-biclusters?gene=" . $e . "\">" . $e . "</a></td></tr>";
    }
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#bc_genes').DataTable({";
    $content .= "    })";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}

function bicluster_tfs_table_shortcode($attr, $content=null)
{
    $bicluster_name = get_query_var('bicluster');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/bicluster/" .
                                     rawurlencode($bicluster_name));
    $entries = json_decode($result_json)->tfs_bc;
    $content = "";
    $content = "<h3>Regulators for bicluster " . $bicluster_name . "</h3>";
    $content .= "<table id=\"bc_tfs\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Regulator</th><th>Role</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/regulator/?regulator=" . $e->tf . "\">" . $e->tf . "</a></td><td>" . $e->role . "</td></tr>";
    }
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#bc_tfs').DataTable({";
    $content .= "    })";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}

function bicluster_mutation_tfs_table_shortcode($attr, $content=null)
{
    $bicluster_name = get_query_var('bicluster');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/bicluster/" .
                                     rawurlencode($bicluster_name));
    $entries = json_decode($result_json)->mutations_tfs;
    $content = "";
    $content = "<h3>Mutations - Regulators for bicluster " . $bicluster_name . "</h3>";
    $content .= "<table id=\"bc_mutations_tfs\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Mutation</th><th>Role</th><th>Regulator</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/mutation/?mutation=" . $e->mutation . "\">" . $e->mutation . "</a></td><td>" . $e->role . "</td><td><a href=\"index.php/regulator/?regulator=" . $e->tf . "\">" . $e->tf . "</a></td></tr>";
    }
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#bc_mutations_tfs').DataTable({";
    $content .= "    })";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}


function search_box_shortcode($attr, $content)
{
    $ajax_action = "completions";
    $content = "<form action=\"" . esc_url(admin_url('admin-post.php')) .  "\" method=\"post\">";
    $content .= "Search Term: ";
    $content .= "<div><input name=\"search_term\" type=\"text\" id=\"mmapi-search\"></input><input type=\"submit\" value=\"Search\" id=\"mmapi-search-button\"></input></div>";
    $content .= "<input type=\"hidden\" name=\"action\" value=\"search_mmapi\">";
    $content .= "</form>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#mmapi-search').autocomplete({";
    $content .= "      source: function(request, response) {";
    $content .= "                jQuery.ajax({ url: ajax_dt.ajax_url, type: 'POST', data: { action: '" . $ajax_action . "', term: request.term }, success: function(data) { response(data.completions) }});";
    $content .= "              },";
    $content .= "      minLength: 2";
    $content .= "    });";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}

function search_results_shortcode($attr, $content)
{
    $search_term = $_GET['search_term'];
    $content = "<div>Search Term: " . $search_term . "</div>";
    $result_json = file_get_contents($source_url . "/api/v1.0.0/search/" .
                                     rawurlencode($search_term));
    $result = json_decode($result_json);
    if ($result->found == "no") {
        $content .= "<div>no entries found</div>";
    } else {
        $content .= "<div>yes, entries found, type: " . $result->data_type .  "</div>";
    }
    return $content;
}

function bicluster_cytoscape_shortcode($attr, $content)
{
    $bicluster_name = get_query_var('bicluster');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/bicluster_network/" .
                                     rawurlencode($bicluster_name));
    $content = "";
    $content .= "<div id=\"cytoscape\"><h3>Influences</h3></div>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    var cy = cytoscape({";
    $content .= "      container: jQuery('#cytoscape'),";
    $content .= "      style: [";
    $content .= "        { selector: 'node', style: { label: 'data(id)'}},";
    $content .= "        { selector: 'edge', style: { label: 'data(role)', 'line-color': '#000', 'target-arrow-shape': 'triangle', 'target-arrow-color': '#000', 'opacity': 0.8, 'curve-style': 'bezier'}},";
    $content .= "        { selector: '.bicluster', style: { 'background-color': 'red', 'shape': 'square'}},";
    $content .= "        { selector: '.tf', style: { 'background-color': 'blue', 'shape': 'triangle'}},";
    $content .= "        { selector: '.mutation', style: { 'background-color': 'green', 'shape': 'diamond'}}";
    $content .= "      ],";
    $content .= "      layout: { name: 'dagre' },";
    $content .= "      elements: " . json_encode(json_decode($result_json)->elements);
    $content .= "    });";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}

function gene_biclusters_table_shortcode($attr, $content=null)
{
    $gene_name = get_query_var('gene');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/biclusters_for_gene/" .
                                     rawurlencode($gene_name));
    $entries = json_decode($result_json)->biclusters;
    $content = "";
    $content = "<h3>Biclusters for gene " . $gene_name . "</h3>";
    $content .= "<table id=\"biclusters\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Bicluster</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/bicluster/?bicluster=" . $e . "\">" . $e . "</a></td></tr>";
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

function gene_info_shortcode($attr, $content=null)
{
    $gene_name = get_query_var('gene');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/gene_info/" .
                                     rawurlencode($gene_name));
    $gene_info = json_decode($result_json);
    $content = "";
    $content .= "<h3>" . $gene_info->preferred . " - " . $gene_info->description;
    $content .= "</h3>";
    $content .= "<div><span class=\"entry-title\">Entrez ID: </span><span><a href=\"https://www.ncbi.nlm.nih.gov/gene/?term=" . $gene_info->entrez_id . "\" target=\"_blank\">" . $gene_info->entrez_id . "</a></span></div>";
    $content .= "<div><span class=\"entry-title\">Ensembl ID: </span><span><a href=\"http://www.ensembl.org/id/" . $gene_info->ensembl_id . "\" target=\"_blank\">" . $gene_info->ensembl_id . "</a></span></div>";
    $content .= "<div><span class=\"entry-title\">Preferred Name: </span><span>" . $gene_info->preferred . "</span></div>";


    $content .= "<div><span class=\"entry-title\">UniProt ID: </span><span><a href=\"https://www.uniprot.org/uniprot/" . $gene_info->uniprot_id . "\" target=\"_blank\">" . $gene_info->uniprot_id . "</a></span></div>";
    $content .= "<div><span class=\"entry-title\">Function: </span><span>" . $gene_info->function . "</span></div>";
    $content .= "";
    return $content;
}

function gene_uniprot_shortcode($attr, $content=null)
{
    $gene_name = get_query_var('gene');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/gene_info/" .
                                     rawurlencode($gene_name));
    $gene_info = json_decode($result_json);
    $content = "";
    $content .= "<h3>UniProtKB " . $gene_info->uniprot_id . "</h3>";
    $content .= "<div id=\"uniprot-viewer\"></div>";
    $content .= "  <script>";
    $content .= "    window.onload = function() {";
    $content .= "      var yourDiv = document.getElementById('uniprot-viewer');";
    $content .= "      var ProtVista = require('ProtVista');";
    $content .= "      var instance = new ProtVista({";
    $content .= "        el: yourDiv,";
    $content .= "        uniprotacc: '" . $gene_info->uniprot_id . "'";
    $content .= "      });";
    $content .= "    }";
    $content .= "  </script>";
    $content .= "";
    return $content;
}


function mmapi_add_shortcodes()
{
    add_shortcode('summary', 'summary_shortcode');
    add_shortcode('mutation_table', 'mutation_table_shortcode');
    add_shortcode('regulator_table', 'regulator_table_shortcode');

    // bicluster page short codes
    add_shortcode('bicluster_genes_table', 'bicluster_genes_table_shortcode');
    add_shortcode('bicluster_tfs_table', 'bicluster_tfs_table_shortcode');
    add_shortcode('bicluster_mutation_tfs_table', 'bicluster_mutation_tfs_table_shortcode');

    add_shortcode('mmapi_search_box', 'search_box_shortcode');
    add_shortcode('mmapi_search_results', 'search_results_shortcode');

    add_shortcode('gene_biclusters_table', 'gene_biclusters_table_shortcode');
    add_shortcode('gene_info', 'gene_info_shortcode');
    add_shortcode('gene_uniprot', 'gene_uniprot_shortcode');
    add_shortcode('bicluster_cytoscape', 'bicluster_cytoscape_shortcode');
}

?>
