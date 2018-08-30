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
    $content .= "    <tr><td>" . $summary->num_patients . "</td><td>Patients</td></tr>";
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
    $content .= "  <thead><tr><th>Bicluster</th><th>Role</th><th>Cox Hazard Ratio</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/bicluster/?bicluster=" . $e->bicluster . "\">" .
                 $e->bicluster . "</a></td><td>" . $e->role . "</td><td>" . $e->hazard_ratio  . "</td></tr>";
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
    $content = "<a name=\"genes\"></a>";
    //$content .= "<h3>Genes for bicluster " . $bicluster_name . "</h3>";
    $content .= "<ul style=\"list-style: none\">";
    foreach ($entries as $e) {
        $content .= "  <li style=\"display: inline\"><a href=\"index.php/gene-biclusters?gene=" . $e . "\">" . $e . "</a></li>";
    }
    $content .= "</ul>";
    return $content;
}

function bicluster_tfs_table_shortcode($attr, $content=null)
{
    $bicluster_name = get_query_var('bicluster');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/bicluster/" .
                                     rawurlencode($bicluster_name));
    $entries = json_decode($result_json)->tfs_bc;
    $content = "<a name=\"regulators\"></a>";
    //$content .= "<h3>Regulators for bicluster " . $bicluster_name . "</h3>";
    $content .= "<table id=\"bc_tfs\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Regulator</th><th>Role</th><th>Cox Hazard Ratio</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/regulator/?regulator=" . $e->tf . "\">" . $e->tf .
                 "</a></td><td>" . $e->role . "</td><td>" . $e->hazard_ratio .  "</td></tr>";
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
    //$content = "<h3>Mutations - Regulators for bicluster " . $bicluster_name . "</h3>";
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
    //$content = "<h3>Biclusters for gene " . $gene_name . "</h3>";
    $content .= "<table id=\"biclusters\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Bicluster</th><th>Survival (Hazard Ratio)</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/bicluster/?bicluster=" . $e->cluster_id . "\">" . $e->cluster_id . "</a></td><td>$e->hazard_ratio</td></tr>";
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

function gene_info_table($gene_name)
{
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/gene_info/" .
                                     rawurlencode($gene_name));
    $gene_info = json_decode($result_json);
    $content = "";
    if ($gene_info->preferred == 'NA') {
        return $content;
    }
    $desc = preg_replace('/\[.*\]/', '', $gene_info->description);
    $content .= "<h3>" . $gene_info->preferred . " - " . $desc;
    $content .= "</h3>";
    $content .= "<table>";
    $content .= "  <thead>";
    $content .= "    <tr><th>Entrez ID</th><th>EnsEMBL ID</th><th>Preferred Name</th><th>Uniprot ID</th></tr>";
    $content .= "  </thead>";
    $content .= "  <tbody>";
    $content .= "    <tr>";
    $content .= "      <td><a href=\"https://www.ncbi.nlm.nih.gov/gene/?term=" . $gene_info->entrez_id . "\" target=\"_blank\">" . $gene_info->entrez_id . "</a></td>";
    $content .= "      <td><a href=\"http://www.ensembl.org/id/" . $gene_info->ensembl_id . "\" target=\"_blank\">" . $gene_info->ensembl_id . "</a></td>";
    $content .= "      <td>" . $gene_info->preferred . "</td>";
    $content .= "      <td><a href=\"https://www.uniprot.org/uniprot/" . $gene_info->uniprot_id . "\" target=\"_blank\">" . $gene_info->uniprot_id . "</a></td>";
    $content .= "    </tr></tr>";
    $content .= "      <td colspan=\"4\"><b>Function:</b> " . $gene_info->function . "</td>";
    $content .= "    </tr>";
    $content .= "  </tbody>";
    $content .= "</table>";
    /*
    $content .= "<div><span class=\"entry-title\">Entrez ID: </span><span><a href=\"https://www.ncbi.nlm.nih.gov/gene/?term=" . $gene_info->entrez_id . "\" target=\"_blank\">" . $gene_info->entrez_id . "</a></span></div>";
    $content .= "<div><span class=\"entry-title\">Ensembl ID: </span><span><a href=\"http://www.ensembl.org/id/" . $gene_info->ensembl_id . "\" target=\"_blank\">" . $gene_info->ensembl_id . "</a></span></div>";
    $content .= "<div><span class=\"entry-title\">Preferred Name: </span><span>" . $gene_info->preferred . "</span></div>";


    $content .= "<div><span class=\"entry-title\">UniProt ID: </span><span><a href=\"https://www.uniprot.org/uniprot/" . $gene_info->uniprot_id . "\" target=\"_blank\">" . $gene_info->uniprot_id . "</a></span></div>";
    $content .= "<div><span class=\"entry-title\">Function: </span><span>" . $gene_info->function . "</span></div>";
    */
    $content .= "";
    return $content;
}

function gene_info_shortcode($attr, $content=null)
{
    return gene_info_table(get_query_var('gene'));
}

function regulator_info_shortcode($attr, $content=null)
{
    return gene_info_table(get_query_var('regulator'));
}

function gene_uniprot_shortcode($attr, $content=null)
{
    $gene_name = get_query_var('gene');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/gene_info/" .
                                     rawurlencode($gene_name));
    $gene_info = json_decode($result_json);
    $content = "";
    //$content .= "<h3>UniProtKB " . $gene_info->uniprot_id . "</h3>";
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

function bicluster_summary_shortcode($attr, $content)
{
    $bicluster_name = get_query_var('bicluster');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/bicluster/" .
                                     rawurlencode($bicluster_name));
    $result = json_decode($result_json);
    $num_genes = count($result->genes);
    $num_regulators = count($result->tfs_bc);
    $num_patients = 0;
    $num_hallmarks = 1;

    $content = "";
    $content .= "<table id=\"summary1\" class=\"row-border\" style=\"margin-bottom: 10px\">";
    $content .= "  <thead><tr><th>Genes</th><th>Patient Tumors</th><th>FPC Var.Exp.<br>(Perm. p-value)</th><th>Survival<br>(Cox Hazard Ratio)</th><th>Independent Replication</th></tr></thead>";
    $content .= "  <tbody>";
    $content .= "    <tr><td><a href=\"#genes\">$num_genes</a></td><td><a href=\"#patients\">$num_patients</a></td><td>-</td><td>$result->hazard_ratio</td><td>-</td></tr>";
    $content .= "  </tbody>";
    $content .= "</table>";

    $content .= "<table id=\"summary2\" class=\"row-border\" style=\"margin-bottom: 10px\">";
    $content .= "  <thead><tr><th>Regulators</th><th>Causal Flows</th><th>Enriched GO BPs</th><th>Enriched<br>Hallmarks of Cancer</th></tr></thead>";
    $content .= "  <tbody>";
    $content .= "    <tr><td><a href=\"#regulators\">$num_regulators</a></td><td>-</td><td>-</td><td><a href=\"#hallmarks\">$num_hallmarks</a></td></tr>";
    $content .= "  </tbody>";
    $content .= "</table>";
    return $content;
}

function bicluster_expressions_graph_shortcode($attr, $content)
{
    $bicluster_name = get_query_var('bicluster');

    $source_url = get_option('source_url', '');
    $content .= '<div id="bicluster_exps" style="width: 100%; height: 300px"></div>';
    $content .= "<script>\n";
    $content .= "    function makeBiclusterExpChart(data) {";
    $content .= "      var x, chart = Highcharts.chart('bicluster_exps', {\n";
    $content .= "        chart: { type: 'boxplot' },";
    $content .= "        title: { text: 'Bicluster Expressions' },\n";
    $content .= "        xAxis: { title: { text: 'Conditions' }},\n";
    $content .= "        yAxis: { title: { text: 'Relative expression'} },\n";
    $content .= "        series: [{name: 'All', showInLegend: false, colorByPoint: true, data: data.data}]\n";
    $content .= "     })\n";
    $content .= "   }\n";

    $content .= "  function loadBiclusterExpressions() {\n";
    $content .= "    jQuery.ajax({\n";
    $content .= "      url: ajax_dt.ajax_url,\n";
    $content .= "      method: 'GET',\n";
    $content .= "      data: {'action': 'bicluster_exps_dt', 'bicluster': '" . $bicluster_name . "' }\n";
    $content .= "    }).done(function(data) {\n";
    $content .= "      makeBiclusterExpChart(data);\n";
    $content .= "    });\n";
    $content .= "  };\n";


    $content .= "  jQuery(document).ready(function() {\n";
    $content .= "    loadBiclusterExpressions();\n";
    $content .= "  });\n";
    $content .= "</script>\n";
    return $content;
}

function bicluster_name_shortcode($attr, $content)
{
    $bicluster_name = get_query_var('bicluster');
    return $bicluster_name;
}

function bicluster_enrichment_graph_shortcode($attr, $content)
{
    $bicluster_name = get_query_var('bicluster');

    $source_url = get_option('source_url', '');
    $content .= '<div id="bicluster_enrich" style="width: 100%; height: 300px"></div>';
    $content .= "<script>\n";
    $content .= "    function makeBiclusterEnrichmentChart(data, conds) {";
    $content .= "      var x, chart = Highcharts.chart('bicluster_enrich', {\n";
    $content .= "        chart: { type: 'column' },";
    $content .= "        title: { text: 'Enrichment of Tumor Subtypes in Quintiles (Example Data)' },\n";
    $content .= "        xAxis: { title: { text: 'Conditions' }, categories: conds,\n";
    $content .= "                 labels: {\n";
    $content .= "                   formatter: function() {\n";
    $content .= "                     return this.axis.categories.indexOf(this.value);\n";
    $content .= "                   }}},\n";
    $content .= "        yAxis: { title: { text: 'Enrichment of Subtypes in Quintiles'} },\n";
    $content .= "        series: data\n";
    $content .= "     })\n";
    $content .= "   }\n";

    $content .= "  function loadBiclusterEnrichment() {\n";
    $content .= "    jQuery.ajax({\n";
    $content .= "      url: ajax_dt.ajax_url,\n";
    $content .= "      method: 'GET',\n";
    $content .= "      data: {'action': 'bicluster_enrichment_dt', 'bicluster': '" . $bicluster_name . "' }\n";
    $content .= "    }).done(function(data) {\n";
    $content .= "      makeBiclusterEnrichmentChart(data.expressions, data.conditions);\n";
    $content .= "    });\n";
    $content .= "  };\n";


    $content .= "  jQuery(document).ready(function() {\n";
    $content .= "    loadBiclusterEnrichment();\n";
    $content .= "  });\n";
    $content .= "</script>\n";
    return $content;
}


function bicluster_hallmarks_shortcode($attr, $content)
{
    $content = "";
    $content = "<a name=\"hallmarks\"></a>";
    $content .= "<h3>Hallmarks</h3>";
    $content .= "<div style=\"width:100%\">";
    $content .= "<div style=\"width: 50%; display: inline-block; vertical-align: top\">";
    $content .= "  <h4>Enriched Hallmarks</h4>";
    $content .= "  <ul style=\"list-style: none\">";
    $content .= "    <li><img style=\"width: 20px\" src=\"" . esc_url(plugins_url('images/angiogenesis.gif', __FILE__)). "\"> Inducing angiogenesis</li>";
    $content .= "  </ul>";

    $content .= "</div>";
    $content .= "<div style=\"width: 50%; display: inline-block\">";
    $content .= "  <h4>Legend</h4>>";
    $content .= "  <img src=\"" . esc_url(plugins_url('images/legend.jpg', __FILE__)). "\">";
    $content .= "</div>";
    $content .= "</div>";
    return $content;
}

function regulator_survival_plot_shortcode($attr, $content=null)
{
    $regulator_name = get_query_var('regulator');
    $static_url = get_option('static_url', '');
    $img_url = $static_url . "/survival_plots_tf/" . rawurlencode($regulator_name) . ".png";

    // check if available, otherwise return nothing
    $file_headers = @get_headers($img_url);
    if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'
        || $file_headers[0] == 'HTTP/1.1 400 Bad Request') {
        return "<p>no survival information available ($img_url)</p>";
    }
    else {
        return "<img src=\"" . $img_url . "\"></img>";
    }
}

function bicluster_survival_plot_shortcode($attr, $content=null)
{
    $bicluster_name = get_query_var('bicluster');
    $static_url = get_option('static_url', '');
    // check if available, otherwise return nothing
    $img_url = $static_url . "/survival_plots_biclusters/" . rawurlencode($bicluster_name) . ".png";
    $file_headers = @get_headers($img_url);
    if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'
        || $file_headers[0] == 'HTTP/1.1 400 Bad Request') {
        return "<p>no survival information available ($img_url)</p>";
    }
    else {
        return "<img src=\"" . $img_url . "\"></img>";
    }
}

function patient_info_shortcode($attr, $content=null)
{
    $patient_name = get_query_var('patient');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/patient/" .
                                     rawurlencode($patient_name));
    $patient_info = json_decode($result_json);
    $content = "";
    $content .= "<table id=\"summary\" class=\"row-border\" style=\"margin-bottom: 10px\">";
    $content .= "  <thead><tr><th>Progression-free Survival</th><th>Survival Status</th><th>Sex</th><th>Age</th></tr></thead>";
    $content .= "  <tbody>";
    $content .= "    <tr><td>$patient_info->pfs_survival</td><td>$patient_info->pfs_status</td><td>$patient_info->sex</td><td>$patient_info->age</td></tr>";
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

function patient_tf_activity_table_shortcode($attr, $content=null)
{
    $patient_name = get_query_var('patient');
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/patient/" .
                                     rawurlencode($patient_name));
    $patient_info = json_decode($result_json);
    $entries = $patient_info->tf_activity;
    $content = "";
    $content = "<h3>Regulator Activity for Patient " . $patient_name . "</h3>";
    $content .= "<table id=\"tf_activity\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Regulator</th><th>Activity</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td><a href=\"index.php/regulator/?regulator=" . $e->tf . "\">" . $e->tf . "</a></td><td>$e->activity</td></tr>";
    }
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#tf_activity').DataTable({";
    $content .= "    })";
    $content .= "  });";
    $content .= "</script>";
    return $content;
}


function causal_flow_table_shortcode($attr, $content=null)
{
    $source_url = get_option('source_url', '');
    $result_json = file_get_contents($source_url . "/api/v1.0.0/causal_flow");
    $entries = json_decode($result_json)->entries;
    $content = "";
    $content .= "<table id=\"causal_flow\" class=\"stripe row-border\">";
    $content .= "  <thead><tr><th>Mutation</th><th>Role</th><th>Regulator</th><th>Role</th><th>Bicluster</th><th>Hazard Ratio</th><th># bicluster genes</th></tr></thead>";
    $content .= "  <tbody>";
    foreach ($entries as $e) {
        $content .= "    <tr><td>$e->mutation</td><td>$e->mutation_role</td>";
        $content .= "<td>$e->regulator</td><td>$e->regulator_role</td><td>$e->bicluster</td>";
        $content .= "<td>$e->hazard_ratio</td>";
        $content .= "<td><a href=\"index.php/bicluster/?bicluster=$e->bicluster#genes\">$e->num_genes</a></td>";
        $content .= "</tr>";
    }
    $content .= "  </tbody>";
    $content .= "</table>";
    $content .= "<script>";
    $content .= "  jQuery(document).ready(function() {";
    $content .= "    jQuery('#causal_flow').DataTable({";
    $content .= "    })";
    $content .= "  });";
    $content .= "</script>";
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
    add_shortcode('regulator_info', 'regulator_info_shortcode');
    add_shortcode('gene_uniprot', 'gene_uniprot_shortcode');
    add_shortcode('bicluster_cytoscape', 'bicluster_cytoscape_shortcode');
    add_shortcode('bicluster_summary', 'bicluster_summary_shortcode');
    add_shortcode('bicluster_expressions', 'bicluster_expressions_graph_shortcode');
    add_shortcode('bicluster_enrichment', 'bicluster_enrichment_graph_shortcode');
    add_shortcode('bicluster_hallmarks', 'bicluster_hallmarks_shortcode');
    add_shortcode('bicluster_name', 'bicluster_name_shortcode');

    add_shortcode('regulator_survival_plot', 'regulator_survival_plot_shortcode');
    add_shortcode('bicluster_survival_plot', 'bicluster_survival_plot_shortcode');

    add_shortcode('patient_info', 'patient_info_shortcode');
    add_shortcode('patient_tf_activity_table', 'patient_tf_activity_table_shortcode');

    add_shortcode('causal_flow_table', 'causal_flow_table_shortcode');
}

?>
