<?php

/* report_rendimentoxdespesas.twig */
class __TwigTemplate_951e7d98448cdae0c50ea93beffbec4b407e2fd6f78e12a652dafaf392534a8e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("starter.twig", "report_rendimentoxdespesas.twig", 1);
        $this->blocks = array(
            'styles' => array($this, 'block_styles'),
            'page_content' => array($this, 'block_page_content'),
            'scripts' => array($this, 'block_scripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "starter.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_styles($context, array $blocks = array())
    {
    }

    // line 5
    public function block_page_content($context, array $blocks = array())
    {
        // line 6
        echo "


\t
      <div class=\"row\">
        <div class=\"col-md-12\">
          <!-- Line chart -->
          <div class=\"box box-primary\">
            <div class=\"box-header with-border\">
              <i class=\"fa fa-bar-chart-o\"></i>

              <h3 class=\"box-title\">Line Chart</h3>

              <div class=\"box-tools pull-right\">
                <button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"collapse\"><i class=\"fa fa-minus\"></i>
                </button>
                <button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\"><i class=\"fa fa-times\"></i></button>
              </div>
            </div>
            <div class=\"box-body\">
              <div id=\"line-chart\" style=\"height: 300px;\"></div>
            </div>
            <!-- /.box-body-->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->

";
    }

    // line 39
    public function block_scripts($context, array $blocks = array())
    {
        // line 40
        echo "

<!-- FLOT CHARTS -->
<script src=\"";
        // line 43
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/flot/jquery.flot.min.js\"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src=\"";
        // line 45
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/flot/jquery.flot.resize.min.js\"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src=\"";
        // line 47
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/flot/jquery.flot.pie.min.js\"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src=\"";
        // line 49
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/flot/jquery.flot.categories.min.js\"></script>
<!-- Page script -->
<script>
  \$(function () {


    /*
     * LINE CHART
     * ----------
     */
    //LINE randomly generated data

    var sin = [], cos = [], res = [];
    for (var i = 0; i < 31; i += 0.5) {
      sin.push([i, Math.sin(i)]);
      cos.push([i, Math.cos(i)]);
      res.push([i, Math.sin(i)-Math.cos(i)]);
    }
    var line_data1 = {
      data: sin,
      color: \"#3c8dbc\"
    };
    var line_data2 = {
      data: cos,
      color: \"#00c0ef\"
    };

    var line_data3 = {
      data: res,
      color: \"#a0c0ef\"
    };

    \$.plot(\"#line-chart\", [line_data1, line_data2, line_data3], {
      grid: {
        hoverable: true,
        borderColor: \"#f3f3f3\",
        borderWidth: 1,
        tickColor: \"#f3f3f3\"
      },
      series: {
      \tshadowSize: 0,
      \tlines: {
      \t\tshow: true
      \t},
      \tpoints: {
      \t\tshow: true
      \t}
      },
      lines: {
        fill: false,
        color: [\"#3c8dbc\", \"#f56954\", \"#666666\"]
      },
      yaxis: {
        show: true,
      },
      xaxis: {
        show: true
      }
    });
    //Initialize tooltip on hover
    \$('<div class=\"tooltip-inner\" id=\"line-chart-tooltip\"></div>').css({
      position: \"absolute\",
      display: \"none\",
      opacity: 0.8
    }).appendTo(\"body\");
    \$(\"#line-chart\").bind(\"plothover\", function (event, pos, item) {

      if (item) {
        var x = item.datapoint[0].toFixed(2),
            y = item.datapoint[1].toFixed(2);

        \$(\"#line-chart-tooltip\").html(item.series.label + \" of \" + x + \" = \" + y)
            .css({top: item.pageY + 5, left: item.pageX + 5})
            .fadeIn(200);
      } else {
        \$(\"#line-chart-tooltip\").hide();
      }

    });
    /* END LINE CHART */

  });


</script>

";
    }

    public function getTemplateName()
    {
        return "report_rendimentoxdespesas.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  96 => 49,  91 => 47,  86 => 45,  81 => 43,  76 => 40,  73 => 39,  38 => 6,  35 => 5,  30 => 3,  11 => 1,);
    }
}
/* {% extends 'starter.twig' %}*/
/* */
/* {% block styles %}{% endblock %}*/
/* */
/* {% block page_content %}*/
/* */
/* */
/* */
/* 	*/
/*       <div class="row">*/
/*         <div class="col-md-12">*/
/*           <!-- Line chart -->*/
/*           <div class="box box-primary">*/
/*             <div class="box-header with-border">*/
/*               <i class="fa fa-bar-chart-o"></i>*/
/* */
/*               <h3 class="box-title">Line Chart</h3>*/
/* */
/*               <div class="box-tools pull-right">*/
/*                 <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>*/
/*                 </button>*/
/*                 <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>*/
/*               </div>*/
/*             </div>*/
/*             <div class="box-body">*/
/*               <div id="line-chart" style="height: 300px;"></div>*/
/*             </div>*/
/*             <!-- /.box-body-->*/
/*           </div>*/
/*           <!-- /.box -->*/
/*         </div>*/
/*         <!-- /.col -->*/
/* */
/*       </div>*/
/*       <!-- /.row -->*/
/* */
/* {% endblock %}*/
/* */
/* {% block scripts %}*/
/* */
/* */
/* <!-- FLOT CHARTS -->*/
/* <script src="{{ vita.config.template_url }}plugins/flot/jquery.flot.min.js"></script>*/
/* <!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->*/
/* <script src="{{ vita.config.template_url }}plugins/flot/jquery.flot.resize.min.js"></script>*/
/* <!-- FLOT PIE PLUGIN - also used to draw donut charts -->*/
/* <script src="{{ vita.config.template_url }}plugins/flot/jquery.flot.pie.min.js"></script>*/
/* <!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->*/
/* <script src="{{ vita.config.template_url }}plugins/flot/jquery.flot.categories.min.js"></script>*/
/* <!-- Page script -->*/
/* <script>*/
/*   $(function () {*/
/* */
/* */
/*     /**/
/*      * LINE CHART*/
/*      * ----------*/
/*      *//* */
/*     //LINE randomly generated data*/
/* */
/*     var sin = [], cos = [], res = [];*/
/*     for (var i = 0; i < 31; i += 0.5) {*/
/*       sin.push([i, Math.sin(i)]);*/
/*       cos.push([i, Math.cos(i)]);*/
/*       res.push([i, Math.sin(i)-Math.cos(i)]);*/
/*     }*/
/*     var line_data1 = {*/
/*       data: sin,*/
/*       color: "#3c8dbc"*/
/*     };*/
/*     var line_data2 = {*/
/*       data: cos,*/
/*       color: "#00c0ef"*/
/*     };*/
/* */
/*     var line_data3 = {*/
/*       data: res,*/
/*       color: "#a0c0ef"*/
/*     };*/
/* */
/*     $.plot("#line-chart", [line_data1, line_data2, line_data3], {*/
/*       grid: {*/
/*         hoverable: true,*/
/*         borderColor: "#f3f3f3",*/
/*         borderWidth: 1,*/
/*         tickColor: "#f3f3f3"*/
/*       },*/
/*       series: {*/
/*       	shadowSize: 0,*/
/*       	lines: {*/
/*       		show: true*/
/*       	},*/
/*       	points: {*/
/*       		show: true*/
/*       	}*/
/*       },*/
/*       lines: {*/
/*         fill: false,*/
/*         color: ["#3c8dbc", "#f56954", "#666666"]*/
/*       },*/
/*       yaxis: {*/
/*         show: true,*/
/*       },*/
/*       xaxis: {*/
/*         show: true*/
/*       }*/
/*     });*/
/*     //Initialize tooltip on hover*/
/*     $('<div class="tooltip-inner" id="line-chart-tooltip"></div>').css({*/
/*       position: "absolute",*/
/*       display: "none",*/
/*       opacity: 0.8*/
/*     }).appendTo("body");*/
/*     $("#line-chart").bind("plothover", function (event, pos, item) {*/
/* */
/*       if (item) {*/
/*         var x = item.datapoint[0].toFixed(2),*/
/*             y = item.datapoint[1].toFixed(2);*/
/* */
/*         $("#line-chart-tooltip").html(item.series.label + " of " + x + " = " + y)*/
/*             .css({top: item.pageY + 5, left: item.pageX + 5})*/
/*             .fadeIn(200);*/
/*       } else {*/
/*         $("#line-chart-tooltip").hide();*/
/*       }*/
/* */
/*     });*/
/*     /* END LINE CHART *//* */
/* */
/*   });*/
/* */
/* */
/* </script>*/
/* */
/* {% endblock %}*/
